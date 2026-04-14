/* ================================================
 * restaurant-details.js — Page-specific logic
 * ================================================ */

// Core Logic
        function switchTab(tabId) {
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active', 'text-gray-900');
                btn.classList.add('text-gray-500');
            });
            document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden-el'));

            const activeBtn = document.getElementById(`tab-${tabId}`);
            activeBtn.classList.add('active', 'text-gray-900');
            activeBtn.classList.remove('text-gray-500');
            
            const activeContent = document.getElementById(`content-${tabId}`);
            activeContent.classList.remove('hidden-el');
        }

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden-el');
            const overlay = document.querySelector(`#${id} .modal-overlay`);
            const content = document.querySelector(`#${id} .modal-content`);
            if(overlay) overlay.style.opacity = '1';
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden-el');
        }

        function openImageModal(src) {
            const modal = document.getElementById('imageModal');
            document.getElementById('enlargedImg').src = src;
            modal.classList.remove('hidden-el');
        }

        function showToast(msg) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.innerHTML = `
                <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div><p class="text-sm font-bold text-gray-900">Action Success</p><p class="text-xs text-gray-500">${msg}</p></div>
            `;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 3500);
        }

        function handleAction(msg) {
            document.querySelectorAll('[id$="Modal"]').forEach(m => m.classList.add('hidden-el'));
            showToast(msg);
        }

        function toggleItem(e) {
            const row = e.target.closest('.bg-white');
            const isDisabling = e.target.innerText.includes('Disable');
            if(isDisabling) {
                row.classList.add('opacity-60');
                e.target.innerText = 'Enable Item';
                e.target.classList.replace('text-red-600', 'text-green-600');
                showToast("Menu item disabled");
            } else {
                row.classList.remove('opacity-60');
                e.target.innerText = 'Disable Item';
                e.target.classList.replace('text-green-600', 'text-red-600');
                showToast("Menu item enabled");
            }
        }

        function passwordReset() { showToast("Password reset link sent to owner email"); }

        // Sidebar Toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        mobileMenuBtn.addEventListener('click', () => {
            sidebar.classList.remove('-translate-x-full');
            sidebarBackdrop.classList.remove('hidden-el');
        });

        sidebarBackdrop.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            sidebarBackdrop.classList.add('hidden-el');
        });
