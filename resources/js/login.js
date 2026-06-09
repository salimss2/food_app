/* ================================================
   login.js — Works with Laravel (NO preventDefault)
================================================ */

document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("loginForm");

    form.addEventListener("submit", function (e) {

        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value.trim();

        // Validation بسيط
        if (email === "" || password === "") {
            e.preventDefault(); // نوقف الإرسال فقط لو في خطأ
            showToast("Please fill in all fields");
            return;
        }

        if (password.length < 5) {
            e.preventDefault();
            showToast("Password must be at least 5 characters");
            return;
        }

        // ✔ إذا كل شيء تمام → الفورم يروح للـ Laravel Controller
        // ❌ لا نعمل redirect هنا
    });

});


/* Toast Notification */
function showToast(msg) {
    const toast = document.getElementById('toast');
    const message = document.getElementById('toastMessage');

    if (!toast || !message) return;

    message.innerText = msg;
    toast.classList.remove('hidden-el');

    setTimeout(() => {
        toast.classList.add('hidden-el');
    }, 3000);
}