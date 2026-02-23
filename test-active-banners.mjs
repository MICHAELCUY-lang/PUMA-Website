async function testActiveBanners() {
    try {
        console.log("Fetching active banners...");
        const response = await fetch('http://localhost:8000/api/banners?active_only=true');
        const data = await response.json();

        console.log("Success:", data.success);
        if (data.data) {
            console.log("Count:", data.data.length);
            console.log("Data:", JSON.stringify(data.data, null, 2));
        } else {
            console.log("No data field in response");
            console.log("Response:", JSON.stringify(data, null, 2));
        }
    } catch (error) {
        console.error("Error fetching banners:", error.message);
    }
}

testActiveBanners();
