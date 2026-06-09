/* ================================================
 * login.js — Page-specific logic
 * ================================================ */

function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMessage').innerText = msg;
            toast.classList.remove('hidden-el');
            setTimeout(() => { toast.classList.add('hidden-el'); }, 3000);
        }

        function handleLogin(e) {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const pass = document.getElementById('password').value;

            // Mock validation
            if(pass.length < 5) {
                showToast("Invalid credentials. Please try again.");
            } else {
                // In production, backend replaces this routing logic
                if(email.includes('admin')) {
                    window.location.href = "index.html"; // Route to dashboard mock
                } else {
                    showToast("System currently offline for maintenance.");
                }
            }
        }
