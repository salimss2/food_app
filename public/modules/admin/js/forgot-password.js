/* ================================================
 * forgot-password.js — Page-specific logic
 * ================================================ */

function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMessage').innerText = msg;
            toast.classList.remove('hidden-el');
            setTimeout(() => { toast.classList.add('hidden-el'); }, 3000);
        }

        function handleRecovery(e) {
            e.preventDefault();
            const val = document.getElementById('recovery_identifier').value;

            if(val.length > 3) {
                showToast("OTP sent securely to your device.");
                setTimeout(() => {
                    window.location.href = "otp-verification.html";
                }, 1500);
            }
        }
