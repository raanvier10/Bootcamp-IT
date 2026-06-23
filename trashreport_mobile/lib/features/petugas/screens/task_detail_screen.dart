import 'dart:io';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:image_picker/image_picker.dart';
import 'package:dio/dio.dart';
import 'package:intl/intl.dart';
import '../../../core/api/api_client.dart';

class TaskDetailScreen extends StatefulWidget {
  final dynamic tugas;
  const TaskDetailScreen({Key? key, required this.tugas}) : super(key: key);

  @override
  _TaskDetailScreenState createState() => _TaskDetailScreenState();
}

class _TaskDetailScreenState extends State<TaskDetailScreen> {
  final ApiClient _apiClient = ApiClient();
  int _currentStep = 0;
  bool _isLoading = false;
  File? _image;
  final TextEditingController _keteranganController = TextEditingController();

  final Color primaryColor = const Color(0xFF0D530E);
  final Color bgColor = const Color(0xFFF9FAFB);
  final Color inkColor = const Color(0xFF111827);
  final Color muteColor = const Color(0xFF6B7280);
  final Color hairlineColor = const Color(0xFFE5E7EB);

  @override
  void initState() {
    super.initState();
    final laporan = widget.tugas['laporan'] ?? {};
    String status = (laporan['status'] ?? '').toString().toLowerCase();
    if (status == 'dalam perjalanan') _currentStep = 1;
    if (status == 'sedang dikerjakan' || status == 'sedang dibersihkan') _currentStep = 2;
    if (status == 'selesai') _currentStep = 3;
  }

  void _pickImage() async {
    final picker = ImagePicker();
    final pickedFile = await picker.pickImage(source: ImageSource.camera, imageQuality: 70);
    if (pickedFile != null) {
      setState(() => _image = File(pickedFile.path));
    }
  }

  void _updateStatus(String status) async {
    setState(() => _isLoading = true);
    try {
      final response = await _apiClient.dio.post(
        '/petugas/tugas/${widget.tugas['id']}/verifikasi',
        data: {'status': status},
      );
      if (response.statusCode == 200) {
        setState(() {
          if (status == 'Dalam Perjalanan') _currentStep = 1;
          if (status == 'Sedang Dibersihkan') _currentStep = 2;
        });
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Status diperbarui')));
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Gagal memperbarui status')));
    } finally {
      setState(() => _isLoading = false);
    }
  }

  void _submitSelesai() async {
    if (_image == null || _keteranganController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Foto dan keterangan wajib diisi!')));
      return;
    }

    setState(() => _isLoading = true);
    try {
      String fileName = _image!.path.split('/').last;
      FormData formData = FormData.fromMap({
        'status': 'Selesai',
        'keterangan': _keteranganController.text,
        'foto_bukti': await MultipartFile.fromFile(_image!.path, filename: fileName),
      });

      final response = await _apiClient.dio.post(
        '/petugas/tugas/${widget.tugas['id']}/verifikasi',
        data: formData,
      );

      if (response.statusCode == 200) {
        setState(() => _currentStep = 3);
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Tugas Selesai!')));
        Navigator.pop(context, true);
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Gagal menyelesaikan tugas')));
    } finally {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final laporan = widget.tugas['laporan'] ?? {};
    final wilayah = laporan['wilayah'] ?? {};
    final status = (laporan['status'] ?? 'MENUNGGU').toString().toUpperCase();
    final isSelesai = status == 'SELESAI';

    double lat = double.tryParse(laporan['lintang'].toString()) ?? -6.200000;
    double lng = double.tryParse(laporan['bujur'].toString()) ?? 106.816666;
    LatLng location = LatLng(lat, lng);

    return Scaffold(
      backgroundColor: bgColor,
      appBar: AppBar(
        title: Text('Detail Tugas', style: GoogleFonts.outfit(color: inkColor, fontWeight: FontWeight.bold)),
        backgroundColor: Colors.white,
        elevation: 0,
        iconTheme: IconThemeData(color: inkColor),
      ),
      body: LayoutBuilder(
        builder: (context, constraints) {
          if (constraints.maxWidth > 800) {
            return Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Expanded(flex: 1, child: _buildInfoPanel(laporan, wilayah, location)),
                Expanded(flex: 1, child: _buildStepperPanel(isSelesai)),
              ],
            );
          }
          return SingleChildScrollView(
            child: Column(
              children: [
                _buildInfoPanel(laporan, wilayah, location),
                _buildStepperPanel(isSelesai),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildInfoPanel(dynamic laporan, dynamic wilayah, LatLng location) {
    return Container(
      color: Colors.white,
      padding: const EdgeInsets.all(24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(laporan['judul'] ?? 'Tugas Pembersihan', style: GoogleFonts.outfit(fontSize: 24, fontWeight: FontWeight.bold, color: inkColor)),
          const SizedBox(height: 8),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
            decoration: BoxDecoration(color: primaryColor.withOpacity(0.1), borderRadius: BorderRadius.circular(20)),
            child: Text(laporan['kode_laporan'] ?? '-', style: GoogleFonts.outfit(color: primaryColor, fontWeight: FontWeight.bold, fontSize: 12)),
          ),
          const SizedBox(height: 24),
          Text('Lokasi', style: GoogleFonts.outfit(fontSize: 16, fontWeight: FontWeight.bold, color: inkColor)),
          const SizedBox(height: 8),
          Text(laporan['alamat'] ?? '-', style: GoogleFonts.outfit(color: muteColor)),
          const SizedBox(height: 16),
          ClipRRect(
            borderRadius: BorderRadius.circular(16),
            child: SizedBox(
              height: 200, width: double.infinity,
              child: FlutterMap(
                options: MapOptions(
                  initialCenter: location,
                  initialZoom: 15.0,
                ),
                children: [
                  TileLayer(urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png'),
                  MarkerLayer(markers: [Marker(point: location, child: const Icon(Icons.location_on, color: Colors.red, size: 40))]),
                ],
              ),
            ),
          ),
          const SizedBox(height: 24),
          Text('Deskripsi', style: GoogleFonts.outfit(fontSize: 16, fontWeight: FontWeight.bold, color: inkColor)),
          const SizedBox(height: 8),
          Text(laporan['deskripsi'] ?? '-', style: GoogleFonts.outfit(color: muteColor, height: 1.5)),
          const SizedBox(height: 24),
          if (laporan['foto'] != null)
            ClipRRect(
              borderRadius: BorderRadius.circular(16),
              child: Image.network('http://127.0.0.1:8000/storage/' + laporan['foto'], height: 200, width: double.infinity, fit: BoxFit.cover),
            )
        ],
      ),
    );
  }

  Widget _buildStepperPanel(bool isSelesai) {
    return Container(
      padding: const EdgeInsets.all(24),
      color: bgColor,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('Status Penugasan', style: GoogleFonts.outfit(fontSize: 20, fontWeight: FontWeight.bold, color: inkColor)),
          const SizedBox(height: 24),
          Stepper(
            physics: const NeverScrollableScrollPhysics(),
            currentStep: _currentStep,
            controlsBuilder: (context, details) => const SizedBox.shrink(),
            steps: [
              Step(
                title: Text('Berangkat ke Lokasi', style: GoogleFonts.outfit(fontWeight: FontWeight.bold)),
                content: _currentStep == 0 
                  ? ElevatedButton(
                      style: ElevatedButton.styleFrom(backgroundColor: primaryColor),
                      onPressed: _isLoading ? null : () => _updateStatus('Dalam Perjalanan'),
                      child: _isLoading ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2)) : const Text('Mulai Perjalanan', style: TextStyle(color: Colors.white)),
                    )
                  : const SizedBox.shrink(),
                isActive: _currentStep >= 0,
                state: _currentStep > 0 ? StepState.complete : StepState.indexed,
              ),
              Step(
                title: Text('Mulai Eksekusi', style: GoogleFonts.outfit(fontWeight: FontWeight.bold)),
                content: _currentStep == 1
                  ? ElevatedButton(
                      style: ElevatedButton.styleFrom(backgroundColor: primaryColor),
                      onPressed: _isLoading ? null : () => _updateStatus('Sedang Dibersihkan'),
                      child: _isLoading ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2)) : const Text('Mulai Pembersihan', style: TextStyle(color: Colors.white)),
                    )
                  : const SizedBox.shrink(),
                isActive: _currentStep >= 1,
                state: _currentStep > 1 ? StepState.complete : StepState.indexed,
              ),
              Step(
                title: Text('Penutupan Tugas', style: GoogleFonts.outfit(fontWeight: FontWeight.bold)),
                content: _currentStep == 2
                  ? Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const SizedBox(height: 8),
                        GestureDetector(
                          onTap: _pickImage,
                          child: Container(
                            height: 150, width: double.infinity,
                            decoration: BoxDecoration(color: Colors.grey[200], borderRadius: BorderRadius.circular(12), border: Border.all(color: hairlineColor), image: _image != null ? DecorationImage(image: FileImage(_image!), fit: BoxFit.cover) : null),
                            child: _image == null ? Column(mainAxisAlignment: MainAxisAlignment.center, children: [Icon(Icons.camera_alt, color: muteColor, size: 40), const SizedBox(height: 8), Text('Ambil Foto Bukti Selesai', style: GoogleFonts.outfit(color: muteColor))]) : null,
                          ),
                        ),
                        const SizedBox(height: 16),
                        TextField(controller: _keteranganController, maxLines: 3, decoration: InputDecoration(labelText: 'Keterangan Selesai', border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)))),
                        const SizedBox(height: 16),
                        SizedBox(
                          width: double.infinity, height: 45,
                          child: ElevatedButton(
                            style: ElevatedButton.styleFrom(backgroundColor: primaryColor, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))),
                            onPressed: _isLoading ? null : _submitSelesai,
                            child: _isLoading ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2)) : const Text('Selesaikan Tugas', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                          ),
                        )
                      ],
                    )
                  : const SizedBox.shrink(),
                isActive: _currentStep >= 2,
                state: _currentStep > 2 ? StepState.complete : StepState.indexed,
              ),
            ],
          )
        ],
      ),
    );
  }
}