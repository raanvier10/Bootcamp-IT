import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';

class LaporanDetailScreen extends StatefulWidget {
  final dynamic report;

  const LaporanDetailScreen({Key? key, required this.report}) : super(key: key);

  @override
  State<LaporanDetailScreen> createState() => _LaporanDetailScreenState();
}

class _LaporanDetailScreenState extends State<LaporanDetailScreen> {
  final Color primaryColor = const Color(0xFF0D530E);
  final Color borderDefault = const Color(0xFFE5E7EB);
  final Color textPrimary = const Color(0xFF1A1A1A);
  final Color textSecondary = const Color(0xFF666666);
  final Color textTertiary = const Color(0xFF999999);
  final Color bgColor = const Color(0xFFF8F9FA);

  // Status mapping to integers for the timeline
  final List<String> _timelineSteps = [
    'Menunggu Verifikasi',
    'Terverifikasi',
    'Ditugaskan ke Petugas',
    'Petugas Dalam Perjalanan',
    'Sedang Dibersihkan',
    'Selesai Diangkut',
    'Menunggu Konfirmasi',
    'Ditutup'
  ];

  int _getCurrentStepIndex(String status) {
    String s = status.toLowerCase();
    if (s.contains('menunggu verifikasi') || s == 'menunggu') return 0;
    if (s.contains('terverifikasi')) return 1;
    if (s.contains('ditugaskan')) return 2;
    if (s.contains('perjalanan')) return 3;
    if (s.contains('dibersihkan')) return 4;
    if (s.contains('diangkut')) return 5;
    if (s.contains('konfirmasi')) return 6;
    if (s.contains('selesai') || s.contains('ditutup')) return 7;
    return 0; // Default
  }

  @override
  Widget build(BuildContext context) {
    var report = widget.report;
    
    // Extracted properties
    String status = (report['status'] ?? 'Menunggu').toString();
    int currentStep = _getCurrentStepIndex(status);
    
    String _safeString(dynamic val, String defaultVal) {
      if (val == null) return defaultVal;
      if (val is String) return val;
      if (val is Map) return val['nama'] ?? val['name'] ?? val['judul'] ?? val.toString();
      return val.toString();
    }

    String trackingId = report['id'] != null ? 'REP-2026-${report['id'].toString().padLeft(4, '0').toUpperCase()}' : 'REP-0000';
    String judul = _safeString(report['judul'], 'Laporan Tanpa Judul');
    String kategori = _safeString(report['kategori'], 'Umum');
    String wilayah = _safeString(report['wilayah'], 'Belum ditentukan');
    String prioritas = _safeString(report['prioritas'], 'Sedang');
    String deskripsi = _safeString(report['deskripsi'], 'Tidak ada deskripsi.');
    String alamat = _safeString(report['alamat'], 'Lokasi tidak diketahui');
    
    String dateStr = report['dilaporkan_pada'] ?? report['created_at'];
    String tanggalLapor = dateStr != null ? DateFormat('dd MMM yyyy, HH:mm').format(DateTime.parse(dateStr)) : '-';

    double lat = double.tryParse(report['lintang']?.toString() ?? '0') ?? 0;
    double lng = double.tryParse(report['bujur']?.toString() ?? '0') ?? 0;
    LatLng location = (lat != 0 && lng != 0) ? LatLng(lat, lng) : const LatLng(-6.200000, 106.816666);

    String imgSebelum = '';
    String imgSesudah = '';
    
    if (report['gambar'] != null && report['gambar'] is List && report['gambar'].isNotEmpty) {
      for (var g in report['gambar']) {
        if (g['tipe_gambar'] == 'sebelum' && imgSebelum.isEmpty) {
          imgSebelum = 'http://127.0.0.1:8000/storage/' + g['jalur_gambar'];
        } else if (g['tipe_gambar'] == 'sesudah' && imgSesudah.isEmpty) {
          imgSesudah = 'http://127.0.0.1:8000/storage/' + g['jalur_gambar'];
        }
      }
    }
    
    // Fallback if structured differently
    if (imgSebelum.isEmpty && report['foto'] != null) {
      imgSebelum = 'http://127.0.0.1:8000/storage/' + report['foto'];
    }
    if (imgSesudah.isEmpty && report['foto_sesudah'] != null) {
      imgSesudah = 'http://127.0.0.1:8000/storage/' + report['foto_sesudah'];
    }

    return Scaffold(
      backgroundColor: bgColor,
      appBar: AppBar(
        title: Text('Detail Laporan', style: GoogleFonts.outfit(color: textPrimary, fontWeight: FontWeight.bold, fontSize: 18)),
        backgroundColor: Colors.white,
        elevation: 0,
        iconTheme: IconThemeData(color: textPrimary),
        centerTitle: true,
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(1.0),
          child: Container(color: borderDefault, height: 1.0),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
        child: Column(
          children: [
            _buildHeaderCard(trackingId, status, judul, kategori, wilayah, tanggalLapor, prioritas),
            const SizedBox(height: 16),
            _buildTimelineCard(status, currentStep),
            const SizedBox(height: 16),
            _buildDeskripsiCard(deskripsi),
            const SizedBox(height: 16),
            _buildLokasiCard(lat, lng, alamat, location, status),
            const SizedBox(height: 16),
            _buildFotoBuktiCard(imgSebelum, imgSesudah),
            const SizedBox(height: 16),
            _buildRiwayatStatusCard(status, tanggalLapor),
            const SizedBox(height: 40),
          ],
        ),
      ),
    );
  }

  // --- Helper Widgets Below ---

  Widget _buildCard({required Widget child}) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: borderDefault, width: 1.5),
      ),
      child: child,
    );
  }

  Color _getStatusColor(String status) {
    String s = status.toLowerCase();
    if (s.contains('menunggu') || s.contains('verifikasi')) return const Color(0xFFD97706);
    if (s.contains('selesai') || s.contains('ditutup')) return primaryColor;
    if (s.contains('ditolak')) return const Color(0xFFDC2626);
    return const Color(0xFF2563EB);
  }

  Widget _buildStatusBadge(String status) {
    Color bg;
    Color text = _getStatusColor(status);
    String s = status.toLowerCase();
    
    if (s.contains('menunggu') || s.contains('verifikasi')) {
      bg = const Color(0xFFFFF7ED);
    } else if (s.contains('selesai') || s.contains('ditutup')) {
      bg = const Color(0xFFEAF5EA);
    } else if (s.contains('ditolak')) {
      bg = const Color(0xFFFEF2F2);
    } else {
      bg = const Color(0xFFEFF6FF);
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(12)),
      child: Text(
        status, 
        style: GoogleFonts.outfit(color: text, fontSize: 11, fontWeight: FontWeight.bold)
      ),
    );
  }

  Widget _buildHeaderCard(String trackingId, String status, String judul, String kategori, String wilayah, String tanggal, String prioritas) {
    return _buildCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Text(trackingId, style: GoogleFonts.outfit(color: textSecondary, fontSize: 12, fontWeight: FontWeight.bold)),
              const SizedBox(width: 8),
              _buildStatusBadge(status),
            ],
          ),
          const SizedBox(height: 12),
          Text(judul, style: GoogleFonts.outfit(color: textPrimary, fontSize: 20, fontWeight: FontWeight.bold)),
          const SizedBox(height: 20),
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(child: _buildInfoItem('Kategori', kategori)),
              Expanded(child: _buildInfoItem('Wilayah', wilayah)),
            ],
          ),
          const SizedBox(height: 16),
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(child: _buildInfoItem('Tanggal Lapor', tanggal)),
              Expanded(child: _buildInfoItem('Prioritas', prioritas)),
            ],
          )
        ],
      )
    );
  }

  Widget _buildInfoItem(String label, String value) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: GoogleFonts.outfit(color: textSecondary, fontSize: 11)),
        const SizedBox(height: 4),
        Text(value, style: GoogleFonts.outfit(color: textPrimary, fontSize: 13, fontWeight: FontWeight.w600)),
      ],
    );
  }

  Widget _buildTimelineCard(String currentStatusText, int currentStepIndex) {
    return _buildCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('Perjalanan Laporan', style: GoogleFonts.outfit(color: textPrimary, fontSize: 16, fontWeight: FontWeight.bold)),
              _buildStatusBadge('Status: $currentStatusText'),
            ],
          ),
          const SizedBox(height: 24),
          ListView.builder(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: _timelineSteps.length,
            itemBuilder: (context, index) {
              bool isPast = index < currentStepIndex;
              bool isCurrent = index == currentStepIndex;
              bool isFuture = index > currentStepIndex;
              bool isLast = index == _timelineSteps.length - 1;

              return Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Timeline line and icon
                  Column(
                    children: [
                      Container(
                        width: 28, height: 28,
                        decoration: BoxDecoration(
                          color: isPast || isCurrent ? primaryColor : Colors.white,
                          shape: BoxShape.circle,
                          border: isFuture ? Border.all(color: borderDefault, width: 2) : null,
                        ),
                        child: Center(
                          child: isPast 
                            ? const Icon(Icons.check, color: Colors.white, size: 16)
                            : isCurrent
                              ? Container(width: 8, height: 8, decoration: const BoxDecoration(color: Colors.white, shape: BoxShape.circle))
                              : Text('${index + 1}', style: GoogleFonts.outfit(color: textSecondary, fontSize: 12, fontWeight: FontWeight.bold)),
                        ),
                      ),
                      if (!isLast)
                        Container(
                          width: 2,
                          height: 30, // Adjusted line height
                          color: isPast ? primaryColor : borderDefault,
                        ),
                    ],
                  ),
                  const SizedBox(width: 16),
                  // Text
                  Expanded(
                    child: Padding(
                      padding: const EdgeInsets.only(top: 4.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            _timelineSteps[index],
                            style: GoogleFonts.outfit(
                              color: isFuture ? textSecondary : textPrimary,
                              fontSize: 14,
                              fontWeight: isCurrent ? FontWeight.bold : FontWeight.normal
                            ),
                          ),
                          if (isCurrent)
                            Padding(
                              padding: const EdgeInsets.only(top: 2),
                              child: Text('Posisi Saat Ini', style: GoogleFonts.outfit(color: textSecondary, fontSize: 11)),
                            ),
                          const SizedBox(height: 24), // Spacing for row height to match line
                        ],
                      ),
                    ),
                  ),
                ],
              );
            },
          ),
        ],
      )
    );
  }

  Widget _buildDeskripsiCard(String deskripsi) {
    return _buildCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('Deskripsi', style: GoogleFonts.outfit(color: textPrimary, fontSize: 16, fontWeight: FontWeight.bold)),
          const SizedBox(height: 12),
          Text(deskripsi, style: GoogleFonts.outfit(color: textSecondary, fontSize: 14, height: 1.5)),
        ],
      )
    );
  }

  Widget _buildLokasiCard(double lat, double lng, String alamat, LatLng location, String status) {
    Color markerColor = _getStatusColor(status);
    return _buildCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('Lokasi', style: GoogleFonts.outfit(color: textPrimary, fontSize: 16, fontWeight: FontWeight.bold)),
          const SizedBox(height: 12),
          Container(
            height: 150,
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(8),
              border: Border.all(color: borderDefault),
            ),
            child: ClipRRect(
              borderRadius: BorderRadius.circular(8),
              child: FlutterMap(
                options: MapOptions(
                  initialCenter: location,
                  initialZoom: 15.0,
                  interactionOptions: const InteractionOptions(flags: InteractiveFlag.none),
                ),
                children: [
                  TileLayer(
                    urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                    userAgentPackageName: 'com.trashreport.app',
                  ),
                  MarkerLayer(
                    markers: [
                      Marker(
                        point: location,
                        width: 18,
                        height: 18,
                        child: Container(
                          decoration: BoxDecoration(
                            color: markerColor,
                            shape: BoxShape.circle,
                            border: Border.all(color: Colors.white, width: 3),
                            boxShadow: [BoxShadow(color: markerColor.withOpacity(0.4), blurRadius: 6, spreadRadius: 2)],
                          ),
                        ),
                      )
                    ],
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 12),
          Text('Titik Koordinat: $lat, $lng', style: GoogleFonts.outfit(color: textSecondary, fontSize: 12)),
          const SizedBox(height: 8),
          GestureDetector(
            onTap: () async {
               final url = Uri.parse('https://www.google.com/maps/search/?api=1&query=$lat,$lng');
               if (await canLaunchUrl(url)) await launchUrl(url);
            },
            child: Row(
              children: [
                const Icon(Icons.map_outlined, size: 14, color: Colors.green),
                const SizedBox(width: 4),
                Text('Buka di Google Maps', style: GoogleFonts.outfit(color: Colors.green, fontSize: 12, fontWeight: FontWeight.bold)),
              ],
            ),
          )
        ],
      )
    );
  }

  Widget _buildFotoBuktiCard(String imgSebelum, String imgSesudah) {
    return _buildCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('Foto Bukti', style: GoogleFonts.outfit(color: textPrimary, fontSize: 16, fontWeight: FontWeight.bold)),
          const SizedBox(height: 16),
          Row(
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Container(width: 6, height: 6, decoration: const BoxDecoration(color: Colors.red, shape: BoxShape.circle)),
                        const SizedBox(width: 6),
                        Text('Sebelum', style: GoogleFonts.outfit(color: textSecondary, fontSize: 12)),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Container(
                      height: 120,
                      decoration: BoxDecoration(
                        color: bgColor,
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(color: borderDefault),
                        image: imgSebelum.isNotEmpty 
                            ? DecorationImage(image: NetworkImage(imgSebelum), fit: BoxFit.cover) 
                            : null,
                      ),
                      child: imgSebelum.isEmpty ? Center(child: Text('Belum tersedia', style: GoogleFonts.outfit(fontSize: 11, color: textSecondary))) : null,
                    )
                  ],
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Container(width: 6, height: 6, decoration: const BoxDecoration(color: Colors.green, shape: BoxShape.circle)),
                        const SizedBox(width: 6),
                        Text('Sesudah', style: GoogleFonts.outfit(color: textSecondary, fontSize: 12)),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Container(
                      height: 120,
                      decoration: BoxDecoration(
                        color: bgColor,
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(color: borderDefault),
                        image: imgSesudah.isNotEmpty 
                            ? DecorationImage(image: NetworkImage(imgSesudah), fit: BoxFit.cover) 
                            : null,
                      ),
                      child: imgSesudah.isEmpty ? Center(child: Text('Belum tersedia', style: GoogleFonts.outfit(fontSize: 11, color: textSecondary))) : null,
                    )
                  ],
                ),
              ),
            ],
          )
        ],
      )
    );
  }

  Widget _buildRiwayatStatusCard(String status, String tanggal) {
    return _buildCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('Riwayat Status', style: GoogleFonts.outfit(color: textPrimary, fontSize: 16, fontWeight: FontWeight.bold)),
          const SizedBox(height: 16),
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                margin: const EdgeInsets.only(top: 4),
                width: 8, height: 8,
                decoration: BoxDecoration(color: primaryColor, borderRadius: BorderRadius.circular(2)),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(status, style: GoogleFonts.outfit(color: textPrimary, fontSize: 14, fontWeight: FontWeight.bold)),
                    const SizedBox(height: 4),
                    Text('Laporan dalam status $status.', style: GoogleFonts.outfit(color: textSecondary, fontSize: 12)),
                    const SizedBox(height: 4),
                    Text(tanggal, style: GoogleFonts.outfit(color: textTertiary, fontSize: 11)),
                  ],
                ),
              )
            ],
          )
        ],
      )
    );
  }
}