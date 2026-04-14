/* ================================================
 * Admin Dashboard · Shared JavaScript  (js/app.js)
 * ================================================ */

// ----- Sidebar & Mobile Menu -----
(function () {
    var mobileMenuBtn   = document.getElementById('mobileMenuBtn');
    var sidebar         = document.getElementById('sidebar');
    var sidebarBackdrop = document.getElementById('sidebarBackdrop');

    if (mobileMenuBtn && sidebar && sidebarBackdrop) {
        mobileMenuBtn.addEventListener('click', function () {
            sidebar.classList.remove('-translate-x-full');
            sidebarBackdrop.classList.remove('hidden-el');
        });
        sidebarBackdrop.addEventListener('click', function () {
            sidebar.classList.add('-translate-x-full');
            sidebarBackdrop.classList.add('hidden-el');
        });
    }

    // ----- Profile Dropdown -----
    var profileDropdownBtn  = document.getElementById('profileDropdownBtn');
    var profileDropdownMenu = document.getElementById('profileDropdownMenu');

    if (profileDropdownBtn && profileDropdownMenu) {
        profileDropdownBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            profileDropdownMenu.classList.toggle('hidden-el');
        });
        document.addEventListener('click', function () {
            if (!profileDropdownMenu.classList.contains('hidden-el')) {
                profileDropdownMenu.classList.add('hidden-el');
            }
        });
    }
})();

// ----- Generic Modal Helpers -----
function closeModal(modalId) {
    var el = document.getElementById(modalId);
    if (el) el.classList.add('hidden-el');
}
