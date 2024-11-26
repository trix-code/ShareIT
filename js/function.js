const emailInput = document.getElementById('email');
const emailError = document.createElement('div');
emailError.className = 'error';
emailError.style.display = 'none';
emailError.textContent = 'Invalid email domain. Allowed domains are gmail.com, seznam.cz, yahoo.com.';
emailInput.parentNode.insertBefore(emailError, emailInput.nextSibling);

const passwordInput = document.getElementById('password');
const passwordError = document.getElementById('passwordError');
const submitBtn = document.getElementById('submitBtn');
const registrationForm = document.getElementById('registrationForm');

// Funkce pro kontrolu hesla
function validatePassword() {
    const password = passwordInput.value;
    const isValid = password.length >= 8 && /[A-Z]/.test(password);

    if (!isValid) {
        passwordError.style.display = 'block';
        submitBtn.disabled = true;
    } else {
        passwordError.style.display = 'none';
        submitBtn.disabled = false;
    }
}

// Funkce pro kontrolu emailu
function validateEmail() {
    const email = emailInput.value;
    const validDomains = ['@gmail.com', '@seznam.cz', '@yahoo.com'];
    const isValid = validDomains.some(domain => email.endsWith(domain));

    if (!isValid) {
        emailError.style.display = 'block';
        submitBtn.disabled = true;
    } else {
        emailError.style.display = 'none';
        if (!passwordError.style.display || passwordError.style.display === 'none') {
            submitBtn.disabled = false;
        }
    }
}

// Volání funkcí validatePassword a validateEmail při změně vstupu
passwordInput.addEventListener('input', validatePassword);
emailInput.addEventListener('input', validateEmail);

// Zabraň odeslání formuláře, pokud je tlačítko stále zakázané
registrationForm.addEventListener('submit', function(event) {
    if (submitBtn.disabled) {
        event.preventDefault();
    }
});
