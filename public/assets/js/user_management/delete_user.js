document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.delete-user');

    deleteButtons.forEach(button => {
        button.addEventListener('click', async function (e) {
            e.preventDefault();
            const userId = this.getAttribute('data-id');

            const isConfirmed = await confirmationPrompt(
                "Are you sure?",
                "You are about to delete this user. This action cannot be undone.",
                "Yes, delete it!",
                "No, cancel"
            );

            if (isConfirmed) {
                // User confirmed, proceed with deletion
                window.showLoadingAlert("Deleting user...");

                fetch(`/api/v1/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => {
                    if (response.ok) { // Check if the response status is 200 OK
                        return response.json(); // Parse the JSON response
                    } else {
                        throw new Error('Failed to delete user'); // Throw an error for non-200 responses
                    }
                })
                .then(data => {
                    Swal.close(); // Close the loading alert
                    window.showAlert("Success!", "The user has been deleted.", "success");
                    window.location.reload(); // Reload the page to reflect changes
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.close(); // Close the loading alert
                    window.showAlert("Error", "Failed to delete user.", "error");
                });
            } else {
                // User canceled
                window.showAlert("Cancelled", "The user was not deleted.", "info");
            }
        });
    });
});