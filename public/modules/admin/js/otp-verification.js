/* ================================================
 * otp-verification.js — Page-specific logic
 * ================================================ */

// OTP Input Auto-Tab Logic
        const inputs = document.querySelectorAll('#otp-container input');
        inputs.forEach((input, index) => {
            input.addEventListener('keyup', (e) => {
                if(e.key >= 0 && e.key <= 9) {
                    if(index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                } else if(e.key === 'Backspace') {
                    if(index > 0) {
                        inputs[index - 1].focus();
                    }
                }
            });
            input.addEventListener('input', (e) => {
                // Ensure only numbers
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
            });
        });

        // Toast
        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMessage').innerText = msg;
            toast.classList.remove('hidden-el');
            setTimeout(() => { toast.classList.add('hidden-el'); }, 3000);
        }

        // Form Submit
        function handleVerification(e) {
            e.preventDefault();
            let code = "";
            inputs.forEach(i => code += i.value);

            if(code.length !== 6) {
                showToast("Please enter all 6 digits.");
            } else if(code === "000000") {
                showToast("Invalid security code.");
            } else {
                // Mock Success
                window.location.href = "reset-password.html";
            }
        }

        // Timer Logic
        let timeLeft = 120; // 2 mins
        const timerSpan = document.getElementById('timerSpan');
        const resendBtn = document.getElementById('resendBtn');
        const hook = document.getElementById('resendHook');

        function updateTimerDisplay() {
            let m = Math.floor(timeLeft / 60);
            let s = timeLeft % 60;
            timerSpan.innerText = `${m < 10 ? '0'+m : m}:${s < 10 ? '0'+s : s}`;
        }

        let interval = setInterval(() => {
            timeLeft--;
            updateTimerDisplay();
            
            if(timeLeft <= 0) {
                clearInterval(interval);
                timerSpan.innerText = "";
                resendBtn.innerText = "Resend Code";
                resendBtn.className = "font-bold text-primary hover:text-primary_dark underline transition-colors cursor-pointer ml-1";
                resendBtn.disabled = false;
            }
        }, 1000);

        resendBtn.addEventListener('click', () => {
            if(!resendBtn.disabled) {
                hook.classList.remove('hidden-el');
                resendBtn.innerText = "Resend Dispatched";
                resendBtn.className = "font-bold text-gray-400 cursor-not-allowed ml-1";
                resendBtn.disabled = true;
                setTimeout(() => hook.classList.add('hidden-el'), 4000);
            }
        });
