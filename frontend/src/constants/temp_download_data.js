const fs = require('fs');
const https = require('https');

// Official DIVIPOLA data via Socrata API (JSON)
const url = "https://www.datos.gov.co/resource/gdxc-w37w.json?$limit=5000";

console.log(`Downloading from ${url}...`);

https.get(url, (res) => {
    if (res.statusCode !== 200) {
        console.error(`Request Failed. Status Code: ${res.statusCode}`);
        res.resume(); // Consume response data to free up memory
        return;
    }

    let data = '';
    res.on('data', (chunk) => {
        data += chunk;
    });

    res.on('end', () => {
        try {
            // Basic validation
            const jsonData = JSON.parse(data);
            console.log(`Downloaded ${jsonData.length} records.`);
            fs.writeFileSync('temp_divipola.json', JSON.stringify(jsonData, null, 2));
            console.log('Successfully saved to temp_divipola.json');
        } catch (e) {
            console.error('Error parsing JSON:', e.message);
        }
    });
}).on('error', (err) => {
    console.error('Download error:', err.message);
});
