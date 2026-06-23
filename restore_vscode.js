const fs = require('fs');
const path = require('path');

const historyDir = 'C:/Users/MyBook Hype AMD/AppData/Roaming/Code/User/History';
const targetFiles = [
    'api_client.dart',
    'auth_service.dart',
    'laporan_service.dart',
    'tugas_service.dart',
    'artikel_service.dart',
    'create_report_screen.dart',
    'riwayat_screen.dart',
    'notifikasi_screen.dart',
    'artikel_screen.dart',
    'artikel_detail_screen.dart',
    'laporan_detail_screen.dart',
    'profile_screen.dart',
    'profile_feature_screens.dart',
    'peta_rute_screen.dart',
    'register_screen.dart',
    'pelapor_dashboard.dart',
    'petugas_dashboard.dart'
];

if (!fs.existsSync(historyDir)) {
    console.log('VS Code history dir not found');
    process.exit();
}

const folders = fs.readdirSync(historyDir);
let foundAny = false;

for (const folder of folders) {
    const entriesFile = path.join(historyDir, folder, 'entries.json');
    if (!fs.existsSync(entriesFile)) continue;
    
    try {
        const data = JSON.parse(fs.readFileSync(entriesFile, 'utf8'));
        const resource = data.resource || '';
        const decodedResource = decodeURIComponent(resource).toLowerCase();
        
        // Cek apakah file ini adalah salah satu dari targetFiles
        const isTarget = targetFiles.some(tf => decodedResource.endsWith(tf.toLowerCase()) && decodedResource.includes('trashreport_mobile'));
        if (isTarget) {
            // Urutkan entries dari yang paling baru
            const entries = data.entries.sort((a, b) => b.timestamp - a.timestamp);
            
            for (const entry of entries) {
                const versionFile = path.join(historyDir, folder, entry.id);
                if (fs.existsSync(versionFile)) {
                    const stat = fs.statSync(versionFile);
                    if (stat.size > 500) { // Pastikan ukurannya cukup besar (bukan scaffold/truncated)
                        const content = fs.readFileSync(versionFile, 'utf8');
                        // Cari versi yang TIDAK memiliki `<truncated` dan memiliki `import`
                        if (!content.includes('<truncated') && content.includes('import')) {
                            // Extract path as is from original resource
                            let originalPath = decodeURIComponent(resource).replace('file:///', '').replace(/\//g, '\\');
                            fs.writeFileSync(originalPath, content);
                            console.log('Restored ' + path.basename(originalPath) + ' from VS Code History!');
                            foundAny = true;
                            break; // Pindah ke file selanjutnya karena sudah ketemu yang valid
                        }
                    }
                }
            }
        }
    } catch (e) {
        // Abaikan error parse json
    }
}
if (!foundAny) console.log('No files matched criteria');
