
// Show a custom alert
 window.showAlert = (title, text, icon)  => {
    return Swal.fire({
        title,
        text,
        icon,
        confirmButtonText: "OK"
    });
}

// Show loading state
 window.showLoadingAlert = (message = "Loading...")  => {
    return Swal.fire({
        title: message,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });
}

// Toggle button state (enable/disable)
 window.setButtonState = (button, isDisabled, text)  => {
    button.disabled = isDisabled;
    button.textContent = text;
}

window.confirmationPrompt = (title, text, confirmButtonText = "Yes", cancelButtonText = "No") => {
    return Swal.fire({
        title,
        text,
        icon: "question",
        showCancelButton: true,
        confirmButtonText,
        cancelButtonText,
        customClass: {
            confirmButton: "btn btn-primary", // Add custom classes for styling
            cancelButton: "btn btn-danger",  // Add custom classes for styling
        },
    }).then((result) => {
        return result.isConfirmed; // Returns `true` if confirmed, `false` if canceled
    });
};