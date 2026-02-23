const axios = require('axios');

async function testActiveBanners() {
    try {
        console.log("Fetching active banners...");
        const response = await axios.get('http://localhost:8000/api/banners?active_only=true');

        console.log("Success:", response.data.success);
        console.log("Count:", response.data.data.length);
        console.log("Data:", JSON.stringify(response.data.data, null, 2));
    } catch (error) {
        console.error("Error fetching banners:", error.message);
        if (error.response) {
            console.error("Response data:", error.response.data);
        }
    }
}

testActiveBanners();
