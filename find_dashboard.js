const fs = require('fs');
const path = require('path');

const historyDir = 'C:/Users/MyBook Hype AMD/AppData/Roaming/Code/User/History';

if (!fs.existsSync(historyDir)) {
    console.log('VS Code history dir not found');
    process.exit();
}

const folders = fs.readdirSync(historyDir);
let largestContent = '';
let largestSize = 0;

for (const folder of folders) {
    const folderPath = path.join(historyDir, folder);
    const files = fs.readdirSync(folderPath);
    for (const file of files) {
        if (file === 'entries.json') continue;
        const filePath = path.join(folderPath, file);
        try {
            const stat = fs.statSync(filePath);
            if (stat.size > largestSize) {
                const content = fs.readFileSync(filePath, 'utf8');
                // The dashboard had the bento grid, map controller, Cara Kerja Eco-Cam deleted, etc.
                if (content.includes('class PelaporDashboard') && content.includes('import')) {
                    largestSize = stat.size;
                    largestContent = content;
                }
            }
        } catch (e) { }
    }
}

if (largestContent && largestSize > 500) {
    fs.writeFileSync('restored_pelapor_dashboard.dart', largestContent);
    console.log('Saved to restored_pelapor_dashboard.dart. Size:', largestSize);
} else {
    console.log('Could not find a valid version. Largest size was:', largestSize);
}
