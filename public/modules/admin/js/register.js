/* ================================================
 * register.js — Page-specific logic
 * ================================================ */

function switchTab(type) {
            const btnC = document.getElementById('btn-customer');
            const btnV = document.getElementById('btn-vendor');
            const frmC = document.getElementById('form-customer');
            const frmV = document.getElementById('form-vendor');

            if(type === 'customer') {
                btnC.className = "flex-1 py-4 text-sm font-bold border-b-2 border-primary text-primary focus:outline-none transition-colors";
                btnV.className = "flex-1 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none transition-colors";
                frmC.classList.remove('hidden-el');
                frmV.classList.add('hidden-el');
            } else {
                btnV.className = "flex-1 py-4 text-sm font-bold border-b-2 border-gray-900 text-gray-900 focus:outline-none transition-colors";
                btnC.className = "flex-1 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none transition-colors";
                frmV.classList.remove('hidden-el');
                frmC.classList.add('hidden-el');
            }
        }

        function showToast(msg) {
            const toast = document.getElementById('toastError');
            document.getElementById('toastMessage').innerText = msg;
            toast.classList.remove('hidden-el');
            setTimeout(() => { toast.classList.add('hidden-el'); }, 3000);
        }

        function handleRegistration(e, type) {
            e.preventDefault();
            
            // Validate mock backend requirement
            const passId = type === 'customer' ? 'c_password' : 'v_password';
            const val = document.getElementById(passId).value;

            if(val.length < 6) {
                showToast("Password must be at least 6 characters.");
            } else {
                // Success - redirect to Welcome Splash
                window.location.href = "welcome.html";
            }
        }
