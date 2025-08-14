document.addEventListener("DOMContentLoaded", () => {
    // function updateWalletAddress() {
    //     const walletAddress = document
    //         .getElementById("walletAddress")
    //         .value.trim();

    //     if (!walletAddress) {
    //         return window.showAlert(
    //             "Error!",
    //             "Wallet address cannot be empty.",
    //             "error"
    //         );
    //     }

    //     window.setButtonState(updateWalletAddressButton, true, "Updating...");
    //     window.showLoadingAlert("Updating wallet address...");

    //     makeApiRequest("/user/update-wallet", "PATCH", {
    //         wallet_address: walletAddress,
    //     })
    //         .then(() => {
    //             window.showAlert(
    //                 "Success!",
    //                 "Wallet address updated successfully!",
    //                 "success"
    //             );
    //         })
    //         .catch((error) => {
    //             window.showAlert("Error!", error.message, "error");
    //         })
    //         .finally(() => {
    //             window.setButtonState(
    //                 updateWalletAddressButton,
    //                 false,
    //                 "Update Wallet Address"
    //             );
    //         });
    // }

    function fundWalletBalance() {
        const fundAmount = document.getElementById("fundAmount").value.trim();

        if (!fundAmount || isNaN(fundAmount)) {
            return window.showAlert(
                "Error!",
                "Please enter a valid amount.",
                "error"
            );
        }

        window.setButtonState(fundWalletBalanceButton, true, "Updating...");
        window.showLoadingAlert("funding wallet...");

        makeApiRequest(`/users/${userId}/fund-wallet`, "PATCH", {
            amount: fundAmount,
        })
            .then(() => {
                window.showAlert(
                    "Success!",
                    "Wallet funded  successfully!",
                    "success"
                );
                window.location.reload(); // Reload the page to reflect changes
            })
            .catch((error) => {
                console.error(error);
                window.showAlert("Error!", error.message, "error");
            })
            .finally(() => {
                window.setButtonState(
                    fundWalletBalanceButton,
                    false,
                    "fund wallet"
                );
            });
    }

    function deductWalletBalance() {
        const deductAmount = document
            .getElementById("deductAmount")
            .value.trim();

        if (!deductAmount || isNaN(deductAmount)) {
            return window.showAlert(
                "Error!",
                "Please enter a valid amount.",
                "error"
            );
        }

        window.setButtonState(deductWalletBalanceButton, true, "Updating...");
        window.showLoadingAlert("deducting wallet...");

        makeApiRequest(`/users/${userId}/deduct-wallet`, "PATCH", {
            amount: deductAmount,
        })
            .then(() => {
                window.showAlert(
                    "Success!",
                    "wallet deducted successfully!",
                    "success"
                );
                window.location.reload(); // Reload the page to reflect changes

            })
            .catch((error) => {
                window.showAlert("Error!", error.message, "error");
            })
            .finally(() => {
                window.setButtonState(
                    deductWalletBalanceButton,
                    false,
                    "deduct wallet"
                );
            });
    }

    function updateUserStatus() {
        const userStatus = document.getElementById("userStatus").value.trim();
        console.log(userStatus);
        if (!userStatus) {
            return window.showAlert(
                "Error!",
                "Please select a valid status.",
                "error"
            );
        }

        window.setButtonState(updateUserStatusButton, true, "Updating...");
        window.showLoadingAlert("Updating user status...");

        makeApiRequest(`/users/${userId}/${userStatus == "active" ? "activate" : "suspend"}`, "PATCH", {
            status: userStatus
        })
            .then(() => {
                window.showAlert(
                    "Success!",
                    "User status updated successfully!",
                    "success"
                );
            })
            .catch((error) => {
                window.showAlert("Error!", error.message, "error");
            })
            .finally(() => {
                window.setButtonState(
                    updateUserStatusButton,
                    false,
                    "Update Status"
                );
            });
    }

    async function deleteUser() {
        const isConfirmed = await confirmationPrompt(
            "Are you sure?",
            "You are about to delete this user. This action cannot be undone.",
            "Yes, delete it!",
            "No, cancel"
        );

        if (isConfirmed) {
            // User confirmed, proceed with deletion
            window.showLoadingAlert("Deleting user...");
            fetch(`/users/${userId}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                    "Content-Type": "application/json",
                },
            })
                .then((response) => {
                    if (response.ok) {
                        // Check if the response status is 200 OK
                        return response.json(); // Parse the JSON response
                    } else {
                        throw new Error("Failed to delete user"); // Throw an error for non-200 responses
                    }
                })
                .then((data) => {
                    Swal.close(); // Close the loading alert
                    window.showAlert(
                        "Success!",
                        "The user has been deleted.",
                        "success"
                    );
                    setTimeout(() => (window.location.href = "/users"), 2000);
                })
                .catch((error) => {
                    Swal.close(); // Close the loading alert
                    window.showAlert(
                        "Error",
                        "Failed to delete user.",
                        "error"
                    );
                });
        } else {
            // User canceled
            window.showAlert("Cancelled", "The user was not deleted.", "info");
        }
    }

    function suspendUser() {
        if (!confirm("Are you sure you want to suspend this user?")) return;

        window.setButtonState(suspendUserButton, true, "Suspending...");
        window.showLoadingAlert("Suspending user...");

        makeApiRequest("/user/suspend", "PATCH", {})
            .then(() => {
                window.showAlert(
                    "Success!",
                    "User suspended successfully!",
                    "success"
                );
            })
            .catch((error) => {
                window.showAlert("Error!", error.message, "error");
            })
            .finally(() => {
                window.setButtonState(suspendUserButton, false, "Suspend User");
            });
    }

    function resetPassword() {
        if (!confirm("Are you sure you want to reset this user's password?"))
            return;

        window.setButtonState(resetPasswordButton, true, "Resetting...");
        window.showLoadingAlert("Resetting password...");

        makeApiRequest("/user/reset-password", "PATCH", {})
            .then(() => {
                window.showAlert(
                    "Success!",
                    "Password reset successfully!",
                    "success"
                );
            })
            .catch((error) => {
                window.showAlert("Error!", error.message, "error");
            })
            .finally(() => {
                window.setButtonState(
                    resetPasswordButton,
                    false,
                    "Reset Password"
                );
            });
    }

    function updateUserPassword() {
        const password = document.getElementById("password").value.trim();
        const confirm_password = document.getElementById("confirm-password").value.trim();
    
        // Validate password length
        if (password.length < 8) {
            return window.showAlert(
                "Error!",
                "Password must be at least 8 characters long.",
                "error"
            );
        }
    
        // Validate password match
        if (password !== confirm_password) {
            return window.showAlert(
                "Error!",
                "Password and confirm password do not match.",
                "error"
            );
        }
    
        // Disable the button and show loading state
        const updateUserStatusButton = document.getElementById("updateUserStatusButton"); // Ensure this ID matches your button's ID
        window.setButtonState(updateUserStatusButton, true, "Updating...");
        window.showLoadingAlert("Updating password...");
    
        // Make API request
        makeApiRequest(`/users/${userId}/update-password`, "PATCH", { new_password: password })
            .then(() => {
                // Show success message
                window.showAlert(
                    "Success!",
                    "User password updated successfully!",
                    "success"
                );
    
                // Empty the password and confirm password fields
                document.getElementById("password").value = "";
                document.getElementById("confirm-password").value = "";
            })
            .catch((error) => {
                // Show error message
                window.showAlert("Error!", error.message, "error");
            })
            .finally(() => {
                // Re-enable the button and reset its text
                window.setButtonState(
                    updateUserStatusButton,
                    false,
                    "Update Password"
                );
            });
    }

    let userId = document.querySelector("[data-id]").getAttribute("data-id");
    // document
    //     .getElementById("updateWalletAddressButton")
    //     ?.addEventListener("click", updateWalletAddress);
    const fundWalletBalanceButton = document.getElementById(
        "fundWalletBalanceButton"
    );
    const deductWalletBalanceButton = document.getElementById(
        "deductWalletBalanceButton"
    );
    const updateUserStatusButton = document.getElementById(
        "updateUserStatusButton"
    );
    const deleteUserButton = document.getElementById("deleteUserButton");
    const suspendUserButton = document.getElementById("suspendUserButton");
    const resetPasswordButton = document.getElementById("resetPasswordButton");
    const updateUserPasswordButton = document.getElementById("updateUserPasswordButton");

    fundWalletBalanceButton.addEventListener("click", fundWalletBalance);
    deductWalletBalanceButton.addEventListener("click", deductWalletBalance);
    updateUserStatusButton.addEventListener("click", updateUserStatus);
    deleteUserButton.addEventListener("click", deleteUser);
    updateUserPasswordButton.addEventListener("click", updateUserPassword);

    // suspendUserButton.addEventListener("click", suspendUser);
    // resetPasswordButton.addEventListener("click", resetPassword);
});