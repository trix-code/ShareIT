document.addEventListener('DOMContentLoaded', () => {
    const emailInput = document.getElementById('email');
    const userEmail = document.body.getAttribute('data-email');

    // Automatické předvyplnění e-mailového pole, pokud je e-mail k dispozici
    if (userEmail) {
        emailInput.value = userEmail;
    }

    // Logika pro zobrazení/skrytí pole zprávy podle výběru tématu
    const topicSelect = document.getElementById('topic');
    const messageContainer = document.getElementById('messageContainer');

    topicSelect.addEventListener('change', () => {
        if (topicSelect.value === 'passwordChange' || topicSelect.value === 'other') {
            messageContainer.style.display = 'block';
        } else {
            messageContainer.style.display = 'none';
        }
    });
});
