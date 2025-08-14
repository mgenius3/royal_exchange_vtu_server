document.addEventListener("DOMContentLoaded", () => {
    const updateUserButton = document.getElementById("updateUserButton");
    let userId = document.querySelector("[data-id]").getAttribute("data-id");

    updateUserButton.addEventListener("click", async (event) => {
        event.preventDefault();

        const email = document.getElementById("email").value.trim();
        const phone = document.getElementById("phone").value.trim();
        const name = document.getElementById("name").value.trim();

        window.setButtonState(updateUserButton, true, "Updating...");
        window.showLoadingAlert("Updating user...");

        console.log(name);
        try {
            const response = await makeApiRequest(
                `/api/v1/users/${userId}`,
                "PATCH",
                { email, phone, name }
            );
        console.log(name);


            window.showAlert(
                "Success!",
                "User updated successfully!",
                "success"
            );
            window.location.reload();
        } catch (error) {
            window.showAlert("Error!", error.message, "error");
        } finally {
            window.setButtonState(updateUserButton, false, "update user");
        }
    });
});