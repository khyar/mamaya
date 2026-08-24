const fs = require('fs');
const path = require('path');

function walk(dir) {
    let results = [];
    const list = fs.readdirSync(dir);
    list.forEach(function(file) {
        file = path.join(dir, file);
        const stat = fs.statSync(file);
        if (stat && stat.isDirectory()) { 
            results = results.concat(walk(file));
        } else { 
            if (file.endsWith('.ts') || file.endsWith('.tsx')) {
                results.push(file);
            }
        }
    });
    return results;
}

const files = walk('./app');

files.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    if (content.includes("import prisma from '@/lib/prisma'")) {
        // replace import
        content = content.replace("import prisma from '@/lib/prisma';", "import { getPrisma } from '@/lib/prisma';");
        
        // Find all function declarations that use prisma
        // This is a naive replacement. For Server Actions and Server Components, 
        // they are usually async functions. We'll add `const prisma = await getPrisma();` at the start of the function body.
        
        // Actually, it's safer to just replace `prisma.` with `(await getPrisma()).`
        content = content.replace(/prisma\./g, "(await getPrisma()).");
        
        fs.writeFileSync(file, content);
        console.log("Refactored " + file);
    }
});
