async function request(url, options = {}) {
    try {
        const response = await fetch(url, options);
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || "Server request failed.");
        }
        return data;

    } catch (error) {
        console.error(error);

        return {
            success: false,
            message: error.message || "An unexpected error occurred."
        };
    }
}