function togglePassword() {
    const passwordField = document.getElementById('password');
    const toggleIcon = document.querySelector('.toggle-password img'); // Zacílení na <img>

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.src = 'img/password-close-eye.png'; // Změna na zavřené oko
    } else {
        passwordField.type = 'password';
        toggleIcon.src = 'img/password-eye.png'; // Změna na otevřené oko
    }
}
