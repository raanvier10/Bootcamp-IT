const fs = require('fs');
const path = require('path');
const brainDir = 'C:/Users/MyBook Hype AMD/.gemini/antigravity/brain/';
const dirs = fs.readdirSync(brainDir);
const fileMap = {};

for (const d of dirs) {
    const p = path.join(brainDir, d, '.system_generated/logs/overview.txt');
    if (fs.existsSync(p)) {
        const lines = fs.readFileSync(p, 'utf8').split('\n');
        for (const line of lines) {
            if (!line.startsWith('{')) continue;
            try {
                const obj = JSON.parse(line);
                if (obj.type === 'VIEW_FILE' && obj.status === 'DONE') {
                    const content = obj.content;
                    const m = content.match(/File Path: `file:\/\/\/(.+?)`/);
                    if (m) {
                        const fpath = m[1].replace(/\//g, '\\');
                        const codeStart = content.indexOf('\n1: ');
                        if (codeStart > -1 && !content.includes('The above content does NOT show the entire file contents')) {
                            let cleanCode = content.substring(codeStart + 1).split('\n');
                            const lastLine = cleanCode.findIndex(l => l.startsWith('The above content shows the entire'));
                            if (lastLine > -1) cleanCode = cleanCode.slice(0, lastLine);
                            fileMap[fpath.toLowerCase()] = {
                                fpath,
                                code: cleanCode.map(l => l.replace(/^\d+:\s/, '')).join('\n')
                            };
                        }
                    }
                }
                if (obj.type === 'PLANNER_RESPONSE' && obj.tool_calls) {
                    for (const tc of obj.tool_calls) {
                        if (tc.name === 'write_to_file' && tc.args.CodeContent) {
                            let target = tc.args.TargetFile.replace(/"/g, '').replace(/\//g, '\\');
                            fileMap[target.toLowerCase()] = { fpath: target, code: tc.args.CodeContent };
                        }
                    }
                }
            } catch (e) {}
        }
    }
}

for (const k in fileMap) {
    if (k.includes('trashreport_mobile') && !k.endsWith('onboarding_temp.dart')) {
        const { fpath, code } = fileMap[k];
        const stat = fs.existsSync(fpath) ? fs.statSync(fpath) : null;
        if (!stat || stat.size < 200) {
            // Write it
            fs.writeFileSync(fpath, code);
            console.log('Restored: ' + fpath);
        }
    }
}
