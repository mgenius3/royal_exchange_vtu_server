document.addEventListener("DOMContentLoaded", () => {
    const createUserButton = document.getElementById("createUserButton");
    const form = document.getElementById("createUserForm");

    if (!form || !createUserButton) return;

    createUserButton.addEventListener("click", async (event) => {
        event.preventDefault();

        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value.trim();
        const password_confirmation = document
            .getElementById("confirm_password")
            .value.trim();
        const phone = document.getElementById("phone").value.trim();
        const name = document.getElementById("name").value.trim();

        // Client-side validation
        if (!email || !password || !phone || !name) {
            return window.showAlert(
                "Error!",
                "Please fill in all required fields.",
                "error"
            );
        }
        if (password.length < 8) {
            return window.showAlert(
                "Error!",
                "Password must be at least 8 characters long.",
                "error"
            );
        }
        if (password !== password_confirmation) {
            return window.showAlert(
                "Error!",
                "Passwords do not match.",
                "error"
            );
        }

        window.setButtonState(createUserButton, true, "Creating...");
        window.showLoadingAlert("Creating user...");

        try {
            const response = await makeApiRequest(
                "/api/v1/user/register",
                "POST",
                {
                    email,
                    password,
                    phone,
                    name,
                    password_confirmation,
                }
            );

            window.showAlert(
                "Success!",
                "User created successfully!",
                "success"
            );
            form.reset();
        } catch (error) {
            window.showAlert("Error!", error.message, "error");
        } finally {
            window.setButtonState(createUserButton, false, "Create User");
        }
    });
});
