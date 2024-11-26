const updateForm = document.getElementById('updateForm');
const errorMessage = document.getElementById('errorMessage');
const successMessage = document.getElementById('successMessage');

updateForm.addEventListener('submit', (e) => {
    e.preventDefault();

    const formData = new FormData(updateForm);
    const type = formData.get('type');
    const newValue = formData.get('newValue');
    const currentPassword = formData.get('currentPassword') || '';

    fetch('php/update_user.php', {
        method: 'POST',
        body: new URLSearchParams(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            successMessage.textContent = 'Údaje byly úspěšně aktualizovány.';
            successMessage.style.display = 'block';
            errorMessage.style.display = 'none';

            // Resetování formuláře a přesměrování
            updateForm.reset();
            setTimeout(() => {
                window.location.href = 'user.php';
            }, 2000);
        } else {
            errorMessage.textContent = data.message;
            errorMessage.style.display = 'block';
            successMessage.style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Chyba:', error);
        errorMessage.textContent = 'Nastala chyba při odesílání požadavku.';
        errorMessage.style.display = 'block';
        successMessage.style.display = 'none';
    });
});
