document.addEventListener("DOMContentLoaded", () => {


// Toggle Status via AJAX
document.querySelectorAll(".toggle-status").forEach((button) => {
    button.addEventListener("click", async function () {
        const giftCardId = this.dataset.id;
        const isEnabled = this.dataset.enabled === "1";
        const newStatus = isEnabled ? "disable" : "enable";

        const isConfirmed = await confirmationPrompt(
            "Are you sure?",
            `You want to ${newStatus} this gift card?`,
            `Yes, ${newStatus}!`,
            "No, cancel"
        );

        if (isConfirmed) {
            fetch(`/gift-cards/${giftCardId}/toggle`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ is_enabled: !isEnabled }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.data) {
                        this.textContent = isEnabled ? "Disabled" : "Enabled";
                        this.classList.toggle("btn-success", !isEnabled);
                        this.classList.toggle("btn-danger", isEnabled);
                        this.dataset.enabled = isEnabled ? "0" : "1";
                    } else {
                        window.showAlert(
                            "Error!",
                            "Failed to toggle status",
                            "error"
                        );
                    }
                })
                .catch((error) => console.error("Error:", error));
        }
    });
});

// Real-time Rate Preview
const giftCardSelect = document.getElementById("giftCardSelect");
const currentBuyRate = document.getElementById("currentBuyRate");
const currentSellRate = document.getElementById("currentSellRate");
giftCardSelect.addEventListener("change", function () {
    const selectedOption = this.options[this.selectedIndex];
    currentBuyRate.textContent = selectedOption.dataset.buyRate;
    currentSellRate.textContent = selectedOption.dataset.sellRate;
});

// Initialize Tooltips
var tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
);
tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

});
