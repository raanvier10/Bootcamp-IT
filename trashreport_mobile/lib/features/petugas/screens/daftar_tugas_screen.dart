import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'task_detail_screen.dart';

class DaftarTugasScreen extends StatelessWidget {
  final List<dynamic> tasks;
  final VoidCallback onRefresh;

  const DaftarTugasScreen({Key? key, required this.tasks, required this.onRefresh}) : super(key: key);

  // Warna persis Tailwind TrashReport
  final Color primaryColor = const Color(0xFF0D530E);
  final Color infoColor = const Color(0xFF2563eb);
  final Color bgColor = const Color(0xFFF9FAFB);
  final Color inkColor = const Color(0xFF111827);
  final Color muteColor = const Color(0xFF6B7280);
  final Color hairlineColor = const Color(0xFFE5E7EB);
  final Color warningColor = const Color(0xFFd97706);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: bgColor,
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        title: Text(
          'Daftar Semua Tugas',
          style: GoogleFonts.outfit(
            color: inkColor,
            fontWeight: FontWeight.bold,
            fontSize: 16,
          ),
        ),
      ),
      body: tasks.isEmpty
          ? Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Container(
                    padding: const EdgeInsets.all(24),
                    decoration: BoxDecoration(color: hairlineColor, shape: BoxShape.circle),
                    child: Icon(Icons.coffee_outlined, size: 48, color: muteColor),
                  ),
                  const SizedBox(height: 16),
                  Text('Belum ada tugas', style: GoogleFonts.outfit(fontSize: 16, fontWeight: FontWeight.bold, color: inkColor)),
                  const SizedBox(height: 8),
                  Text('Saat ini belum ada tugas untuk Anda.', style: GoogleFonts.outfit(color: muteColor)),
                ],
              ),
            )
          : RefreshIndicator(
              onRefresh: () async => onRefresh(),
              child: ListView.separated(
                padding: const EdgeInsets.all(20),
                itemCount: tasks.length,
                separatorBuilder: (c, i) => const SizedBox(height: 12),
                itemBuilder: (context, index) {
                  final tugas = tasks[index];
                  final laporan = tugas['laporan'] ?? {};
                  final wilayah = laporan['wilayah'] ?? {};
                  
                  return GestureDetector(
                    onTap: () async {
                      final result = await Navigator.push(context, MaterialPageRoute(builder: (_) => TaskDetailScreen(tugas: tugas)));
                      if (result == true) onRefresh();
                    },
                    child: Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(color: hairlineColor),
                      ),
                      child: Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Container(
                            width: 48, height: 48,
                            decoration: BoxDecoration(color: bgColor, borderRadius: BorderRadius.circular(12)),
                            child: Icon(Icons.location_on_outlined, color: muteColor),
                          ),
                          const SizedBox(width: 16),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(laporan['judul'] ?? 'Tugas Pembersihan', style: GoogleFonts.outfit(fontWeight: FontWeight.bold, color: inkColor, fontSize: 14)),
                                const SizedBox(height: 6),
                                Wrap(
                                  spacing: 6, runSpacing: 6,
                                  crossAxisAlignment: WrapCrossAlignment.center,
                                  children: [
                                    Container(
                                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                      decoration: BoxDecoration(border: Border.all(color: hairlineColor), borderRadius: BorderRadius.circular(4)),
                                      child: Text(laporan['kode_laporan'] ?? '-', style: GoogleFonts.outfit(fontWeight: FontWeight.w800, color: inkColor, fontSize: 9)),
                                    ),
                                    Icon(Icons.circle, size: 4, color: muteColor),
                                    Text(
                                      tugas['ditugaskan_pada'] != null ? DateFormat('dd MMM yyyy, HH:mm').format(DateTime.parse(tugas['ditugaskan_pada'])) : '-', 
                                      style: GoogleFonts.outfit(color: muteColor, fontSize: 10, fontWeight: FontWeight.w500)
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 6),
                                Text(wilayah['nama'] ?? 'Wilayah Tidak Diketahui', style: GoogleFonts.outfit(color: muteColor, fontSize: 11, fontWeight: FontWeight.w500)),
                              ],
                            ),
                          ),
                          // Status Badge
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                            decoration: BoxDecoration(
                              color: (laporan['status'] ?? '').toString().toLowerCase() == 'selesai' ? primaryColor.withOpacity(0.1) : warningColor.withOpacity(0.1),
                              borderRadius: BorderRadius.circular(100),
                              border: Border.all(color: hairlineColor)
                            ),
                            child: Text(
                              (laporan['status'] ?? 'MENUNGGU').toString().toUpperCase(), 
                              style: GoogleFonts.outfit(
                                color: (laporan['status'] ?? '').toString().toLowerCase() == 'selesai' ? primaryColor : inkColor, 
                                fontSize: 9, 
                                fontWeight: FontWeight.bold,
                                letterSpacing: 0.5
                              )
                            ),
                          )
                        ],
                      ),
                    ),
                  );
                },
              ),
            ),
    );
  }
}
