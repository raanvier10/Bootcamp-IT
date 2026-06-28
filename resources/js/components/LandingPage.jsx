import React, { useEffect, useRef } from 'react';
import { motion, useScroll, useTransform } from 'framer-motion';
import { Leaf, MapPin, ShieldCheck, Camera, Sparkles, ArrowRight, Map, ArrowUpRight, Newspaper, Download } from 'lucide-react';

const LandingPage = ({ stats, mapReports, latestArticles, authRoute, mapRoute, articlesRoute, loginRoute, registerRoute }) => {
    const { scrollYProgress } = useScroll();
    const mapRef = useRef(null);

    useEffect(() => {
        if (typeof window !== 'undefined' && window.L && mapRef.current) {
            // Check if map is already initialized
            let map = mapRef.current._leaflet_map;
            if (map) {
                map.remove();
            }

            map = window.L.map(mapRef.current, {
                center: [-6.32, 107.30], // Karawang default
                zoom: 12,
                zoomControl: true,
                scrollWheelZoom: false, // keep false so it doesn't zoom when scrolling down the page
            });
            
            // Save instance
            mapRef.current._leaflet_map = map;

            window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const validReports = (mapReports || []).filter(r => r.lintang && r.bujur);

            validReports.forEach(report => {
                let color = '#dc2626'; // default red (pending)
                if (report.status === 'processing' || report.status === 'Ditugaskan' || report.status === 'Dalam Perjalanan' || report.status === 'Sedang Dibersihkan' || report.status === 'Menunggu Konfirmasi') color = '#d97706';
                if (report.status === 'completed' || report.status === 'Selesai' || report.status === 'Ditutup') color = '#16a34a';

                const marker = window.L.circleMarker([report.lintang, report.bujur], {
                    radius: 9,
                    fillColor: color,
                    color: '#fff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.85,
                }).addTo(map);

                let imageHtml = '';
                if (report.gambar_sebelum && report.gambar_sebelum.length > 0) {
                    imageHtml = `<div style="margin-bottom:8px; border-radius:8px; overflow:hidden; height:100px; width:100%;"><img src="/storage/${report.gambar_sebelum[0].jalur_gambar}" style="width:100%; height:100%; object-fit:cover;" /></div>`;
                }

                marker.bindPopup(`
                    <div style="font-family: 'Outfit', sans-serif; min-width: 150px;">
                        ${imageHtml}
                        <p style="font-weight:700; margin-bottom:4px; line-height:1.2;">${report.judul || 'Laporan'}</p>
                        <p style="font-size:12px; color:#6b7280; margin:0;">Status: <strong>${report.status || '-'}</strong></p>
                    </div>
                `);
            });

            if (validReports.length > 0) {
                const group = new window.L.featureGroup(
                    validReports.map(r => window.L.marker([r.lintang, r.bujur]))
                );
                map.fitBounds(group.getBounds(), { padding: [50, 50], maxZoom: 14 });
            }

            return () => {
                if (map) map.remove();
            };
        }
    }, [mapReports]);

    const fadeInUp = {
        hidden: { opacity: 0, y: 30 },
        visible: { opacity: 1, y: 0, transition: { duration: 0.6, ease: "easeOut" } }
    };

    const stagger = {
        hidden: { opacity: 0 },
        visible: { opacity: 1, transition: { staggerChildren: 0.15 } }
    };

    return (
        <>
            {/* ── Hero Section (Premium Rich UI - Dominantly Green Illustration) ── */}
            <section id="hero" className="relative overflow-hidden bg-[#F8F9FA]">
                {/* Dot Grid Background */}
                <div className="absolute inset-0 bg-[radial-gradient(#d1d5db_1px,transparent_1px)] [background-size:24px_24px] opacity-40"></div>

                <div className="absolute top-0 right-0 w-[800px] h-[800px] bg-primary/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>
                <div className="absolute bottom-0 left-0 w-[600px] h-[600px] bg-primary-soft/50 rounded-full blur-3xl translate-y-1/3 -translate-x-1/3 pointer-events-none"></div>

                <div className="relative max-w-7xl mx-auto px-6 pt-16 pb-20 lg:pt-20 lg:pb-32 flex flex-col lg:flex-row items-center gap-16">
                    <motion.div initial="hidden" animate="visible" variants={stagger} className="flex-1 text-left">
                        <motion.div variants={fadeInUp} className="inline-flex items-center gap-2 bg-white border border-gray-200 text-primary text-sm font-bold px-4 py-2 rounded-full mb-8 shadow-sm">
                            <span className="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                            Sistem Pelaporan Sampah Komunitas
                        </motion.div>

                        <motion.h1 variants={fadeInUp} className="font-delight font-bold text-5xl lg:text-[64px] text-gray-900 leading-[1.1] tracking-tight mb-8">
                            Laporkan titik <br />
                            <span className="text-primary">sampah liar.</span> <br />
                            Jaga bumi kita.
                        </motion.h1>

                        <motion.p variants={fadeInUp} className="text-lg text-gray-500 leading-relaxed mb-10 max-w-xl font-medium">
                            Bersama TrashReport, setiap orang bisa berkontribusi menjaga kebersihan lingkungan. Laporkan tumpukan sampah liar — tim kami dan komunitas akan menanganinya.
                        </motion.p>

                        <motion.div variants={fadeInUp} className="flex flex-col sm:flex-row flex-wrap gap-3 sm:gap-4 w-full">
                            <a href={authRoute} className="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3.5 sm:px-8 sm:py-4 text-sm sm:text-base font-bold text-white bg-primary rounded-xl hover:bg-primary-deep transition-all shadow-lg shadow-primary/20">
                                Buat Laporan Baru <ArrowRight className="w-5 h-5 ml-2" />
                            </a>
                            <div className="flex flex-row gap-3 w-full sm:w-auto">
                                <a href={mapRoute} className="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-3.5 sm:px-8 sm:py-4 text-sm sm:text-base font-bold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all shadow-sm">
                                    <Map className="w-4 h-4 sm:w-5 sm:h-5 mr-2" /> Peta
                                </a>
                                <a href="/TrashReport.apk" download className="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-3.5 sm:px-8 sm:py-4 text-sm sm:text-base font-bold text-[#0D530E] bg-[#DDF5DF] border border-[#0D530E]/20 rounded-xl hover:bg-[#c2edc4] transition-all shadow-sm">
                                    <Download className="w-4 h-4 sm:w-5 sm:h-5 mr-2" /> Aplikasi
                                </a>
                            </div>
                        </motion.div>
                    </motion.div>

                    {/* Animated Floating Illustration */}
                    <motion.div 
                        initial={{ opacity: 0, x: 50 }}
                        animate={{ opacity: 1, x: 0 }}
                        transition={{ duration: 1, delay: 0.4, ease: [0.22, 1, 0.36, 1] }}
                        className="hidden lg:flex flex-1 relative w-full h-[450px] max-w-none items-center justify-center lg:-mt-12"
                    >
                        {/* Animated Background Rings (More Green) */}
                        <motion.div 
                            animate={{ rotate: 360 }} 
                            transition={{ repeat: Infinity, duration: 40, ease: "linear" }}
                            className="absolute inset-0 m-auto w-[240px] h-[240px] sm:w-[340px] sm:h-[340px] lg:w-[420px] lg:h-[420px] rounded-full border-[1.5px] border-[#0D530E]/20 border-dashed"
                        />
                        <motion.div 
                            animate={{ rotate: -360 }} 
                            transition={{ repeat: Infinity, duration: 30, ease: "linear" }}
                            className="absolute inset-0 m-auto w-[200px] h-[200px] sm:w-[260px] sm:h-[260px] lg:w-[320px] lg:h-[320px] rounded-full border-[3px] border-t-[#0D530E] border-r-[#0D530E] border-b-[#116B13] border-l-transparent opacity-30 shadow-[0_0_30px_rgba(13,83,14,0.2)]"
                        />
                        
                        {/* Soft Glow Behind */}
                        <div className="absolute inset-0 m-auto w-[160px] h-[160px] sm:w-[200px] sm:h-[200px] bg-gradient-to-tr from-[#0D530E]/30 to-[#116B13]/30 rounded-full blur-3xl animate-pulse"></div>

                        {/* Central 3D Illustration Mockup */}
                        <motion.div 
                            animate={{ y: [0, -15, 0] }} 
                            transition={{ repeat: Infinity, duration: 5, ease: "easeInOut" }}
                            className="absolute inset-0 m-auto w-[220px] h-[220px] sm:w-[300px] sm:h-[300px] lg:w-[400px] lg:h-[400px] flex z-10 items-center justify-center pointer-events-none"
                        >
                            <img src="/images/hero-illustration.png" alt="Orang melapor sampah 3D" className="w-full h-full object-contain mix-blend-multiply" />
                        </motion.div>

                        {/* Floating Element 1: Top Right Success Card */}
                        <motion.div 
                            animate={{ y: [0, -20, 0], x: [0, 5, 0] }} 
                            transition={{ repeat: Infinity, duration: 5.5, ease: "easeInOut", delay: 0.5 }}
                            className="absolute top-8 right-4 lg:right-4 z-20 hidden sm:flex"
                        >
                            <div className="bg-white/95 backdrop-blur-md p-3 lg:p-4 rounded-2xl shadow-xl shadow-[#0D530E]/10 border border-[#DDF5DF] flex items-center gap-3">
                                <div className="w-10 h-10 lg:w-12 lg:h-12 rounded-xl bg-[#DDF5DF] flex items-center justify-center">
                                    <ShieldCheck className="w-5 h-5 lg:w-6 lg:h-6 text-[#0D530E]" />
                                </div>
                                <div>
                                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Status</p>
                                    <p className="text-sm font-bold text-[#0D530E]">Terverifikasi</p>
                                </div>
                            </div>
                        </motion.div>

                        {/* Floating Element 2: Bottom Left Action Card */}
                        <motion.div 
                            animate={{ y: [0, 20, 0], x: [0, -5, 0] }} 
                            transition={{ repeat: Infinity, duration: 6, ease: "easeInOut", delay: 1 }}
                            className="absolute bottom-16 left-4 lg:-left-4 z-20 hidden sm:flex"
                        >
                            <div className="bg-white/95 backdrop-blur-md p-3 lg:p-4 rounded-2xl shadow-xl shadow-[#0D530E]/10 border border-[#DDF5DF] flex items-center gap-3">
                                <div className="w-10 h-10 lg:w-12 lg:h-12 rounded-xl bg-[#DDF5DF]/50 flex items-center justify-center">
                                    <Camera className="w-5 h-5 lg:w-6 lg:h-6 text-[#0D530E]" />
                                </div>
                                <div>
                                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Aksi</p>
                                    <p className="text-sm font-bold text-gray-900">Upload Bukti</p>
                                </div>
                            </div>
                        </motion.div>

                        {/* Floating Element 3: Processing Pill */}
                        <motion.div 
                            animate={{ y: [0, 15, 0], rotate: [0, 2, 0] }} 
                            transition={{ repeat: Infinity, duration: 4, ease: "easeInOut", delay: 1.5 }}
                            className="absolute bottom-2 sm:bottom-6 right-0 sm:right-8 lg:right-6 z-30 scale-90 sm:scale-100"
                        >
                            <div className="bg-[#0D530E] px-5 py-2.5 rounded-full shadow-xl shadow-[#0D530E]/30 border border-[#116B13] flex items-center gap-2">
                                <div className="w-2 h-2 bg-green-300 rounded-full animate-pulse shadow-[0_0_8px_rgba(134,239,172,0.8)]"></div>
                                <span className="text-white text-[10px] lg:text-xs font-bold tracking-widest">DIPROSES</span>
                            </div>
                        </motion.div>

                        {/* Floating Element 4: Top Left Leaf */}
                        <motion.div 
                            animate={{ y: [0, 15, 0], rotate: [-10, 15, -10] }} 
                            transition={{ repeat: Infinity, duration: 4.5, ease: "easeInOut", delay: 0.2 }}
                            className="absolute top-16 left-10 lg:left-6 z-20"
                        >
                            <div className="w-12 h-12 bg-white/95 rounded-2xl shadow-lg shadow-[#0D530E]/5 flex items-center justify-center border border-[#DDF5DF]">
                                <Leaf className="w-6 h-6 text-[#0D530E]" />
                            </div>
                        </motion.div>
                    </motion.div>
                </div>
            </section>

            {/* ── Stats Section (Minimalist) ── */}
            <motion.section initial="hidden" whileInView="visible" viewport={{ once: true, margin: "-100px" }} variants={stagger} className="border-y border-gray-100 bg-white relative z-10">
                <div className="max-w-7xl mx-auto px-6 py-10">
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-y-12 gap-x-4 md:gap-8 md:divide-x md:divide-gray-100">
                        <motion.div variants={fadeInUp} className="text-center px-4">
                            <p className="text-4xl font-bold text-gray-900 tracking-tight">{stats.total_reports}</p>
                            <p className="text-sm font-medium text-gray-500 mt-2">Total Laporan Masuk</p>
                        </motion.div>
                        <motion.div variants={fadeInUp} className="text-center px-4">
                            <p className="text-4xl font-bold text-primary tracking-tight">{stats.reports_completed}</p>
                            <p className="text-sm font-medium text-gray-500 mt-2">Selesai Dibersihkan</p>
                        </motion.div>
                        <motion.div variants={fadeInUp} className="text-center px-4">
                            <p className="text-4xl font-bold text-orange-500 tracking-tight">{stats.reports_processing}</p>
                            <p className="text-sm font-medium text-gray-500 mt-2">Sedang Diproses</p>
                        </motion.div>
                        <motion.div variants={fadeInUp} className="text-center px-4">
                            <p className="text-4xl font-bold text-gray-900 tracking-tight">24/7</p>
                            <p className="text-sm font-medium text-gray-500 mt-2">Sistem Aktif</p>
                        </motion.div>
                    </div>
                </div>
            </motion.section>

            {/* ── How It Works (Premium Bento) ── */}
            <section className="py-24 bg-[#F8F9FA] relative overflow-hidden">
                {/* Background Pattern */}
                <div className="absolute inset-0 bg-[radial-gradient(#d1d5db_1px,transparent_1px)] [background-size:24px_24px] opacity-50"></div>
                
                <div className="max-w-7xl mx-auto px-6 relative z-10">
                    <motion.div initial="hidden" whileInView="visible" viewport={{ once: true, margin: "-100px" }} variants={fadeInUp} className="mb-16 text-center max-w-3xl mx-auto">
                        <h2 className="font-delight font-bold text-4xl lg:text-5xl text-gray-900 tracking-tight mb-6">Cara Kerja.</h2>
                        <p className="text-lg text-gray-500 font-medium">Tiga langkah sederhana untuk lingkungan yang lebih sehat. Kami membuat proses pelaporan menjadi sangat transparan dan mudah.</p>
                    </motion.div>

                    <motion.div initial="hidden" whileInView="visible" viewport={{ once: true, margin: "-100px" }} variants={stagger} className="relative grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
                        {/* Vertical Timeline Line (Mobile Only) */}
                        <div className="absolute top-10 bottom-10 left-[64px] w-[3px] bg-gradient-to-b from-transparent via-[#0D530E]/20 to-transparent md:hidden z-0"></div>

                        {/* Step 1 */}
                        <motion.div variants={fadeInUp} className="bg-white h-full flex flex-col rounded-[32px] p-8 border-2 border-gray-200 group hover:border-[#0D530E] hover:shadow-xl hover:shadow-[#0D530E]/10 transition-all duration-300 relative overflow-hidden">
                            <div className="absolute -bottom-10 -right-6 text-[180px] font-black text-gray-50/80 pointer-events-none group-hover:text-[#0D530E]/5 transition-colors duration-500">1</div>
                            <div className="relative z-10 flex-1 flex flex-col">
                                <div className="w-16 h-16 bg-[#DDF5DF] rounded-2xl flex items-center justify-center mb-8 shadow-sm border border-[#DDF5DF]/50 group-hover:-translate-y-2 transition-transform duration-300">
                                    <Camera className="w-8 h-8 text-[#0D530E]" />
                                </div>
                                <div>
                                    <div className="text-[11px] font-bold text-[#0D530E] bg-[#DDF5DF] inline-block px-3 py-1 rounded-full uppercase tracking-widest mb-4">Lapor</div>
                                    <h3 className="text-2xl font-bold text-gray-900 mb-4">Foto & Kirim</h3>
                                    <p className="text-gray-500 font-medium leading-relaxed">Ambil foto titik tumpukan sampah liar menggunakan ponsel Anda. Sistem kami akan otomatis mencatat lokasi GPS.</p>
                                </div>
                            </div>
                        </motion.div>

                        {/* Step 2 */}
                        <motion.div variants={fadeInUp} className="bg-white h-full flex flex-col rounded-[32px] p-8 border-2 border-gray-200 group hover:border-[#0D530E] hover:shadow-xl hover:shadow-[#0D530E]/10 transition-all duration-300 relative overflow-hidden">
                            <div className="absolute -bottom-10 -right-6 text-[180px] font-black text-gray-50/80 pointer-events-none group-hover:text-[#0D530E]/5 transition-colors duration-500">2</div>
                            <div className="relative z-10 flex-1 flex flex-col">
                                <div className="w-16 h-16 bg-[#DDF5DF] rounded-2xl flex items-center justify-center mb-8 shadow-sm border border-[#DDF5DF]/50 group-hover:-translate-y-2 transition-transform duration-300">
                                    <ShieldCheck className="w-8 h-8 text-[#0D530E]" />
                                </div>
                                <div>
                                    <div className="text-[11px] font-bold text-[#0D530E] bg-[#DDF5DF] inline-block px-3 py-1 rounded-full uppercase tracking-widest mb-4">Verifikasi</div>
                                    <h3 className="text-2xl font-bold text-gray-900 mb-4">Tindak Lanjut</h3>
                                    <p className="text-gray-500 font-medium leading-relaxed">Admin akan memverifikasi laporan Anda dengan cepat dan langsung menugaskan petugas lapangan terdekat.</p>
                                </div>
                            </div>
                        </motion.div>

                        {/* Step 3 */}
                        <motion.div variants={fadeInUp} className="bg-white h-full flex flex-col rounded-[32px] p-8 border-2 border-gray-200 group hover:border-[#0D530E] hover:shadow-xl hover:shadow-[#0D530E]/10 transition-all duration-300 relative overflow-hidden">
                            <div className="absolute -bottom-10 -right-6 text-[180px] font-black text-gray-50/80 pointer-events-none group-hover:text-[#0D530E]/5 transition-colors duration-500">3</div>
                            <div className="relative z-10 flex-1 flex flex-col">
                                <div className="w-16 h-16 bg-[#DDF5DF] rounded-2xl flex items-center justify-center mb-8 shadow-sm border border-[#DDF5DF]/50 group-hover:-translate-y-2 transition-transform duration-300">
                                    <Sparkles className="w-8 h-8 text-[#0D530E]" />
                                </div>
                                <div>
                                    <div className="text-[11px] font-bold text-[#0D530E] bg-[#DDF5DF] inline-block px-3 py-1 rounded-full uppercase tracking-widest mb-4">Selesai</div>
                                    <h3 className="text-2xl font-bold text-gray-900 mb-4">Bersih & Tuntas</h3>
                                    <p className="text-gray-500 font-medium leading-relaxed">Petugas membersihkan lokasi dan mengunggah foto bukti selesai. Anda bisa melihat perubahannya secara real-time.</p>
                                </div>
                            </div>
                        </motion.div>
                    </motion.div>
                </div>
            </section>

            {/* ── Map Preview ── */}
            <motion.section initial="hidden" whileInView="visible" viewport={{ once: true }} variants={fadeInUp} className="py-24 bg-[#F8F9FA] border-y border-gray-100 relative overflow-hidden">
                {/* Moving Background Blobs */}
                <motion.div 
                    animate={{ x: [0, 100, 0], y: [0, -50, 0] }} 
                    transition={{ repeat: Infinity, duration: 15, ease: "easeInOut" }}
                    className="absolute top-0 right-0 w-[500px] h-[500px] bg-[#DDF5DF] rounded-full mix-blend-multiply filter blur-[100px] opacity-70 pointer-events-none"
                />
                <motion.div 
                    animate={{ x: [0, -100, 0], y: [0, 50, 0] }} 
                    transition={{ repeat: Infinity, duration: 18, ease: "easeInOut" }}
                    className="absolute bottom-0 left-0 w-[600px] h-[600px] bg-green-50 rounded-full mix-blend-multiply filter blur-[100px] opacity-60 pointer-events-none"
                />
                <motion.div 
                    animate={{ scale: [1, 1.2, 1], rotate: [0, 90, 0] }} 
                    transition={{ repeat: Infinity, duration: 20, ease: "easeInOut" }}
                    className="absolute top-1/2 left-1/4 w-[400px] h-[400px] bg-orange-50 rounded-full mix-blend-multiply filter blur-[100px] opacity-50 pointer-events-none"
                />

                <div className="max-w-7xl mx-auto px-6 relative z-10">
                    <div className="flex flex-col lg:flex-row items-center gap-16">
                        <div className="lg:w-1/3">
                            <h2 className="font-delight font-bold text-4xl lg:text-5xl text-gray-900 tracking-tight mb-6">Peta Transparansi.</h2>
                            <p className="text-lg text-gray-500 font-medium leading-relaxed mb-8">
                                Setiap titik sampah yang dilaporkan dipetakan secara publik. Kami mengedepankan transparansi agar seluruh komunitas dapat memantau wilayah mana yang butuh perhatian ekstra.
                            </p>

                            <ul className="space-y-4 mb-10">
                                <li className="flex items-center gap-4 bg-white p-3 rounded-xl border border-gray-100 shadow-sm">
                                    <span className="w-3 h-3 rounded-full bg-[#dc2626] ring-4 ring-red-50"></span>
                                    <span className="text-sm font-bold text-gray-700">Laporan Baru (Belum Ditangani)</span>
                                </li>
                                <li className="flex items-center gap-4 bg-white p-3 rounded-xl border border-gray-100 shadow-sm">
                                    <span className="w-3 h-3 rounded-full bg-[#d97706] ring-4 ring-orange-50"></span>
                                    <span className="text-sm font-bold text-gray-700">Sedang Ditindaklanjuti</span>
                                </li>
                                <li className="flex items-center gap-4 bg-white p-3 rounded-xl border border-gray-100 shadow-sm">
                                    <span className="w-3 h-3 rounded-full bg-[#16a34a] ring-4 ring-green-50"></span>
                                    <span className="text-sm font-bold text-gray-700">Selesai Dibersihkan</span>
                                </li>
                            </ul>

                            <a href={mapRoute} className="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-gray-900 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                                Buka Peta Interaktif <ArrowUpRight className="w-4 h-4 ml-2" />
                            </a>
                        </div>

                        <div className="lg:w-2/3 w-full">
                            <div className="relative w-full h-[500px] rounded-3xl overflow-hidden shadow-2xl shadow-primary/10 border-4 border-white">
                                <div ref={mapRef} className="w-full h-full z-10 relative"></div>
                                <div className="absolute inset-0 bg-gradient-to-t from-[#F8F9FA]/40 to-transparent z-20 pointer-events-none"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </motion.section>

            {/* ── Latest Articles ── */}
            {latestArticles && latestArticles.length > 0 && (
                <section className="py-24 bg-white">
                    <div className="max-w-7xl mx-auto px-6">
                        <div className="flex items-end justify-between mb-12">
                            <div>
                                <h2 className="font-delight font-bold text-4xl lg:text-5xl text-gray-900 tracking-tight mb-4">Edukasi & Info.</h2>
                                <p className="text-xl text-gray-500 font-medium">Baca artikel terbaru seputar pengelolaan limbah dan inisiatif hijau.</p>
                            </div>
                            <a href={articlesRoute} className="hidden md:inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-gray-900 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                                Lihat Semua
                            </a>
                        </div>

                        <motion.div initial="hidden" whileInView="visible" viewport={{ once: true, margin: "-100px" }} variants={stagger} className="grid grid-cols-1 md:grid-cols-3 gap-8">
                            {latestArticles.map(article => (
                                <motion.a variants={fadeInUp} key={article.id} href={`/artikel/${article.slug}`} className="group flex flex-col bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-xl hover:shadow-[#0D530E]/5 transition-all duration-300 overflow-hidden">
                                    <div className="h-48 md:h-auto md:aspect-video w-full bg-[#E8F5E9] relative overflow-hidden shrink-0">
                                        {article.gambar_sampul ? (
                                            <img src={article.gambar_sampul.startsWith('http') || article.gambar_sampul.startsWith('/storage') ? article.gambar_sampul : `/storage/${article.gambar_sampul}`} alt={article.judul} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                                        ) : (
                                            <div className="w-full h-full flex items-center justify-center">
                                                <Newspaper className="w-12 h-12 md:w-16 md:h-16 text-[#0D530E]/10" />
                                            </div>
                                        )}
                                        <div className="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors duration-300"></div>
                                    </div>
                                    <div className="p-6 md:p-8 flex flex-col flex-1">
                                        <div className="flex items-center gap-2 text-xs font-medium text-gray-500 mb-4">
                                            <span>{article.created_at ? new Date(article.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : 'Hari ini'}</span>
                                            <span className="text-gray-300">•</span>
                                            <span>Admin TrashReport</span>
                                        </div>
                                        <h3 className="text-xl font-bold text-gray-900 tracking-tight mb-3 group-hover:text-[#0D530E] transition-colors leading-snug line-clamp-2">{article.judul}</h3>
                                        <p className="text-sm text-gray-500 font-medium line-clamp-3 leading-relaxed mt-auto">{article.kutipan}</p>
                                    </div>
                                </motion.a>
                            ))}
                        </motion.div>
                    </div>
                </section>
            )}

            {/* ── CTA Section ── */}
            <motion.section initial="hidden" whileInView="visible" viewport={{ once: true }} variants={fadeInUp} className="py-24 bg-primary relative overflow-hidden">
                <div className="absolute top-0 right-0 w-[500px] h-[500px] bg-white rounded-full blur-3xl opacity-10 translate-x-1/3 -translate-y-1/2"></div>

                <div className="relative max-w-4xl mx-auto px-6 text-center">
                    <h2 className="font-delight font-bold text-5xl lg:text-6xl text-white tracking-tight mb-6">Siap mengambil aksi?</h2>
                    <p className="text-xl text-primary-soft font-medium mb-12 max-w-2xl mx-auto">Satu laporan dari Anda bisa mencegah kerusakan lingkungan yang lebih besar. Jadilah bagian dari solusi hari ini.</p>

                    <div className="flex flex-col sm:flex-row flex-wrap gap-4 justify-center w-full px-2 sm:px-0">
                        <a href={authRoute} className="w-full sm:w-auto inline-flex items-center justify-center px-6 py-4 sm:px-8 text-base font-bold text-primary bg-white rounded-xl hover:bg-gray-50 transition-all shadow-xl">
                            Buat Laporan Sekarang
                        </a>
                        <a href="/TrashReport.apk" download className="w-full sm:w-auto inline-flex items-center justify-center px-6 py-4 sm:px-8 text-base font-bold text-white bg-transparent border-2 border-white/80 hover:border-white rounded-xl hover:bg-white/10 transition-all shadow-xl">
                            <Download className="w-5 h-5 mr-2" /> Unduh Aplikasi Mobile
                        </a>
                    </div>
                </div>
            </motion.section>
        </>
    );
};

export default LandingPage;
