document.addEventListener("DOMContentLoaded", () => {
    const loginButton = document.getElementById("login_button");

    loginButton.addEventListener("click", async (event) => {
        event.preventDefault();

        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value.trim();
        const rememberMe = document.getElementById("remember_me").checked;

        if (!email || !password) {
            return window.showAlert("Error!", "Please fill in all required fields.", "error");
        }

        window.setButtonState(loginButton, true, "Signing in...");
        window.showLoadingAlert("Signing in...");

        try {
            const data = await makeApiRequest("/api/v1/user/login", "POST", { email, password, remember: rememberMe });
            
            window.storeSession("admin", data.user); // Store session data
            window.storeSession("adminToken", data.token); // Store token
            window.showAlert("Login successful!", "Redirecting to dashboard...", "success")//Show Alert
            setTimeout(() => (window.location.href = "/users"), 2000);
        } catch (error) {
            window.showAlert("Login Failed", error.message, "error");
        } finally {
            window.setButtonState(loginButton, false, "Sign In");
        }
    });
});