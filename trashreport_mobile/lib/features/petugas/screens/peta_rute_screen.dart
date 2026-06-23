import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class PetaRuteScreen extends StatelessWidget {
  final dynamic tugas;

  const PetaRuteScreen({Key? key, required this.tugas}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Peta Rute', style: GoogleFonts.outfit(color: Colors.black)),
        backgroundColor: Colors.white,
        iconTheme: const IconThemeData(color: Colors.black),
      ),
      body: const Center(
        child: Text('Map View - Navigasi Rute'),
      ),
    );
  }
}