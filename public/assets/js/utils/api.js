window.makeApiRequest = async (url, method, data = {}) => {
    try {
        const response = await fetch(url, {
            method: method,
            headers: getHeaders(),
            body: method !== "GET" ? JSON.stringify(data) : undefined,
        });

        const responseData = await response.json();
        console.log(responseData);
        if (!response.ok) {
            let errorMessages;
            // Extract and format error messages
            if (responseData.errors) {
                errorMessages = Object.keys(responseData.errors)
                    .map((key) => responseData.errors[key].join("\n")) // Join messages for each field
                    .join("\n\n"); // Separate errors for different fields
                // Display API validation errors
            } else {
                errorMessages = responseData.message;
            }
            throw new Error(
                errorMessages || "Something went wrong, try again."
            );
        }
        return responseData; // Return response if successful
    } catch (error) {
        throw error; // Forward the error to be handled by the caller
    }
};

function getHeaders() {
    const headers = {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
    };

    // Add the adminToken from localStorage to the Authorization header
    const adminToken = localStorage.getItem("adminToken")
    
    // window.getSession("adminToken");
    console.log(adminToken);
    if (adminToken) {
        headers["Authorization"] = `Bearer ${adminToken}`;
    }

    return headers;
}