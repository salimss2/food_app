/* ================================================
 * settings.js — Page-specific logic
 * ================================================ */

// Tab Switching Logic
        const tabs = ['tab-app', 'tab-notif', 'tab-terms', 'tab-privacy'];
        
        function switchTab(target) {
            tabs.forEach(t => {
                document.getElementById(t).classList.add('hidden-el');
                const btn = document.getElementById('btn-' + t);
                btn.className = "border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors focus:outline-none";
            });
            
            document.getElementById(target).classList.remove('hidden-el');
            const tBtn = document.getElementById('btn-' + target);
            tBtn.className = "border-primary text-primary whitespace-nowrap border-b-2 py-4 px-1 text-sm font-bold focus:outline-none";
        }

        // Modal Logic
        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMessage').innerText = msg;
            toast.classList.remove('hidden-el');
            setTimeout(() => { toast.classList.add('hidden-el'); }, 3000);
        }

        function closeModals() {
            document.getElementById('editStringModal').classList.add('hidden-el');
            document.getElementById('editTextModal').classList.add('hidden-el');
        }

        function openEditModal(label, targetId) {
            document.getElementById('modalStringTitle').innerText = `Editing: ${label}`;
            document.getElementById('targetIdRef').value = targetId;
            document.getElementById('stringValInput').value = document.getElementById(targetId).innerText.trim();
            document.getElementById('editStringModal').classList.remove('hidden-el');
        }

        function handleStringSave(e) {
            e.preventDefault();
            const val = document.getElementById('stringValInput').value;
            const targetId = document.getElementById('targetIdRef').value;
            document.getElementById(targetId).innerText = val;
            closeModals();
            showToast("Configuration value deployed.");
        }

        function openTextModal(label, targetId) {
            document.getElementById('modalTextTitle').innerText = `Editing: ${label}`;
            document.getElementById('targetTextRef').value = targetId;
            document.getElementById('textValInput').value = document.getElementById(targetId).innerText.trim();
            document.getElementById('editTextModal').classList.remove('hidden-el');
        }

        function handleTextSave(e) {
            e.preventDefault();
            const val = document.getElementById('textValInput').value;
            const targetId = document.getElementById('targetTextRef').value;
            document.getElementById(targetId).innerText = val;
            closeModals();
            showToast("Legal document updated successfully.");
        }
