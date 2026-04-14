/* ================================================
 * reset-password.js — Page-specific logic
 * ================================================ */

const newPwd = document.getElementById('newPwd');
        const confPwd = document.getElementById('confirmPwd');
        const bSubmit = document.getElementById('btnSubmit');
        const pE = document.getElementById('pwdMatchError');

        function checkStrength(value) {
            let score = 0;
            if(value.length > 5) score++;
            if(value.length > 8 && /[A-Z]/.test(value) && /[0-9]/.test(value)) score++;
            if(value.length > 8 && /[^A-Za-z0-9]/.test(value)) score++;
            return score;
        }

        function updatePwds() {
            const s = checkStrength(newPwd.value);
            const s1 = document.getElementById('pwdStr1');
            const s2 = document.getElementById('pwdStr2');
            const s3 = document.getElementById('pwdStr3');
            const fb = document.getElementById('pwdFeedback');

            // Reset
            s1.className = "h-full w-1/3 transition-colors bg-gray-200";
            s2.className = "h-full w-1/3 transition-colors bg-gray-200 border-l border-white";
            s3.className = "h-full w-1/3 transition-colors bg-gray-200 border-l border-white";

            if(newPwd.value.length === 0) {
                fb.innerText = "Include letters, numbers and symbols.";
                fb.className = "text-xs text-gray-500 mt-1";
            } else if(s === 0 || s === 1) {
                s1.classList.replace('bg-gray-200', 'bg-red-500');
                fb.innerText = "Weak password.";
                fb.className = "text-xs text-red-500 mt-1";
            } else if(s === 2) {
                s1.classList.replace('bg-gray-200', 'bg-yellow-500');
                s2.classList.replace('bg-gray-200', 'bg-yellow-500');
                fb.innerText = "Medium password.";
                fb.className = "text-xs text-yellow-500 mt-1";
            } else {
                s1.classList.replace('bg-gray-200', 'bg-green-500');
                s2.classList.replace('bg-gray-200', 'bg-green-500');
                s3.classList.replace('bg-gray-200', 'bg-green-500');
                fb.innerText = "Strong password!";
                fb.className = "text-xs text-green-500 mt-1";
            }

            const match = confPwd.value === newPwd.value;
            if(confPwd.value.length > 0 && !match) {
                pE.classList.remove('hidden-el');
            } else {
                pE.classList.add('hidden-el');
            }

            if(s > 0 && match && newPwd.value.length > 0) {
                bSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
                bSubmit.removeAttribute('disabled');
            } else {
                bSubmit.classList.add('opacity-50', 'cursor-not-allowed');
                bSubmit.setAttribute('disabled', 'true');
            }
        }

        newPwd.addEventListener('input', updatePwds);
        confPwd.addEventListener('input', updatePwds);

        function handleReset(e) {
            e.preventDefault();
            const toast = document.getElementById('toast');
            toast.classList.remove('hidden-el');
            
            setTimeout(() => { 
                window.location.href = "login.html"; 
            }, 2000);
        }
