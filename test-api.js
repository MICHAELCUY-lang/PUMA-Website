const https = require('https');
const http = require('http');

const API_BASE = 'http://localhost:8000/api';

async function testEndpoint(name, path) {
    return new Promise((resolve) => {
        const url = `${API_BASE}${path}`;
        console.log(`\n🧪 Testing: ${name}`);
        console.log(`   URL: ${url}`);

        http.get(url, {
            headers: { 'Accept': 'application/json' }
        }, (res) => {
            let data = '';

            res.on('data', (chunk) => {
                data += chunk;
            });

            res.on('end', () => {
                try {
                    const json = JSON.parse(data);
                    const count = Array.isArray(json) ? json.length : (json.data ? json.data.length : 0);
                    console.log(`   ✅ Status: ${res.statusCode}`);
                    console.log(`   📊 Records: ${count}`);
                    if (count > 0) {
                        console.log(`   📝 Sample:`, JSON.stringify(Array.isArray(json) ? json[0] : json.data[0], null, 2).substring(0, 200) + '...');
                    }
                    resolve({ success: true, status: res.statusCode, count });
                } catch (e) {
                    console.log(`   ⚠️  Response: ${data.substring(0, 100)}`);
                    resolve({ success: false, error: e.message });
                }
            });
        }).on('error', (err) => {
            console.log(`   ❌ Error: ${err.message}`);
            resolve({ success: false, error: err.message });
        });
    });
}

async function runTests() {
    console.log('🚀 PUMA API Comprehensive Test\n');
    console.log('='.repeat(60));

    const tests = [
        { name: 'Members API', path: '/members' },
        { name: 'Divisions API', path: '/divisions' },
        { name: 'Cabinets API', path: '/cabinets' },
        { name: 'UI Content API', path: '/ui-content' },
        { name: 'Banners API', path: '/banners' },
    ];

    const results = [];

    for (const test of tests) {
        const result = await testEndpoint(test.name, test.path);
        results.push({ ...test, ...result });
        await new Promise(resolve => setTimeout(resolve, 500));
    }

    console.log('\n' + '='.repeat(60));
    console.log('\n📊 TEST SUMMARY\n');

    let passed = 0;
    let failed = 0;

    results.forEach(r => {
        if (r.success) {
            console.log(`✅ ${r.name.padEnd(20)} - ${r.status} - ${r.count} records`);
            passed++;
        } else {
            console.log(`❌ ${r.name.padEnd(20)} - FAILED: ${r.error}`);
            failed++;
        }
    });

    console.log(`\n📈 Results: ${passed} passed, ${failed} failed out of ${results.length} tests`);
    console.log('\n' + '='.repeat(60));
}

runTests();
