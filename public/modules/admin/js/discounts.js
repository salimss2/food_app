/* ================================================
 * discounts.js — Page-specific logic
 * ================================================ */

// Toast Helper
function showToast(msg, isError = false) {
    const toast = document.getElementById('toast');
    if (!toast) return;

    document.getElementById('toastMessage').innerText = msg;

    if (isError) {
        toast.classList.replace('bg-green-50', 'bg-red-50');
        toast.classList.replace('border-green-200', 'border-red-200');
        document.getElementById('toastMessage').classList.replace('text-green-800', 'text-red-800');
    } else {
        toast.classList.replace('bg-red-50', 'bg-green-50');
        toast.classList.replace('border-red-200', 'border-green-200');
        document.getElementById('toastMessage').classList.replace('text-red-800', 'text-green-800');
    }

    toast.classList.remove('hidden-el');
    setTimeout(() => { toast.classList.add('hidden-el'); }, 3000);
}

function openModal() {
    const form = document.getElementById('discountForm');
    if (form) form.reset();

    const modalTitle = document.getElementById('modalTitle');
    if (modalTitle) modalTitle.innerText = "Create Discount Code";

    const modal = document.getElementById('discountModal');
    if (modal) modal.classList.remove('hidden-el');
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.add('hidden-el');
}

// Ensure the page doesn't try to auto-fetch or reload
document.addEventListener('DOMContentLoaded', () => {
    // If you need any initialization, do it safely here.
});
