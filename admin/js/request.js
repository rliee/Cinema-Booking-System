async function request(url, options = {}) {
    try {
        const response = await fetch(url, options);

        const text = await response.text();

        console.log("Raw response:");
        console.log(text);

        const data = JSON.parse(text);

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

// async function request(url, options = {}) {
//     try {
//         const response = await fetch(url, options);
//         const data = await response.json();

//         if (!response.ok) {
//             throw new Error(data.message || "Server request failed.");
//         }
//         return data;
//     } catch (error) {
//         console.error(error);
//         return {
//             success: false,
//             message: error.message || "An unexpected error occurred."
//         };
//     }
// }