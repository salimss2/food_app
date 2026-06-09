/* ================================================
 * reports.js — Page-specific logic
 * ================================================ */

/* ================================================
 * reports.js — Page-specific logic
 * ================================================ */

// Search logic (Client-side filtering of rendered DOM rows)
const searchInput = document.getElementById('reportSearch');
if (searchInput) {
    searchInput.addEventListener('input', function () {
        const filter = this.value.toLowerCase();
        const tbody = document.getElementById('reportsTable');
        if (!tbody) return;

        const rows = tbody.querySelectorAll('tr');

        rows.forEach(row => {
            // Skip the "No data" row
            if (row.cells.length === 1) return;

            // Assume Report ID is in the first column
            const idCell = row.cells[0];
            if (idCell) {
                const text = idCell.textContent || idCell.innerText;
                if (text.toLowerCase().indexOf(filter) > -1) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            }
        });
    });
}

// Toast Helper
function showToast(msg) {
    const toast = document.getElementById('toast');
    if (!toast) return;
    const msgEl = document.getElementById('toastMessage');
    if (msgEl) msgEl.innerText = msg;
    toast.classList.remove('hidden-el');
    setTimeout(() => { toast.classList.add('hidden-el'); }, 3000);
}
