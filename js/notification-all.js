function loadNotifications() {
    fetch('../php/fetch_notifications.php')
        .then(response => response.json())
        .then(data => {
            const notificationsList = document.getElementById('notificationsList');
            notificationsList.innerHTML = '';

            if (data.success && data.notifications.length > 0) {
                data.notifications.forEach(notification => {
                    const listItem = document.createElement('li');
                    listItem.textContent = `${notification.sender_name} má zájem o ${notification.subscription_name} - ${notification.created_at}`;
                    listItem.innerHTML += `
                        <button onclick="handleNotificationAction(${notification.id}, 'confirm')">Potvrdit</button>
                        <button onclick="handleNotificationAction(${notification.id}, 'reject')">Odmítnout</button>
                    `;
                    notificationsList.appendChild(listItem);
                });
            } else {
                notificationsList.innerHTML = '<li>Nemáte žádné notifikace.</li>';
            }
        })
        .catch(error => {
            console.error('Chyba při načítání notifikací:', error);
        });
}

function handleNotificationAction(notificationId, action) {
    console.log("Sending data:", { id: notificationId, action }); // Ladění dat

    fetch('../php/update_notification_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: notificationId, action: action }),
    })
        .then(response => response.json())
        .then(data => {
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');

            if (data.success) {
                if (action === 'confirm') {
                    successMessage.textContent = 'Žádost byla úspěšně přijata.';
                } else if (action === 'reject') {
                    successMessage.textContent = 'Žádost byla úspěšně odstraněna.';
                }
                successMessage.style.display = 'block';
                errorMessage.style.display = 'none';

                // Zobrazení zprávy na 5 sekund
                setTimeout(() => {
                    successMessage.style.display = 'none';
                    location.reload(); // Aktualizace stránky po 5 sekundách
                }, 5000);
            } else {
                successMessage.style.display = 'none';
                errorMessage.textContent = data.error || "Chyba při zpracování požadavku.";
                errorMessage.style.display = 'block';

                // Zobrazení chybové zprávy na 5 sekund
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 5000);
            }
        })
        .catch(error => {
            console.error("Chyba při komunikaci se serverem:", error);

            const errorMessage = document.getElementById('errorMessage');
            errorMessage.textContent = "Chyba při připojení k serveru.";
            errorMessage.style.display = 'block';

            // Zobrazení chybové zprávy na 5 sekund
            setTimeout(() => {
                errorMessage.style.display = 'none';
            }, 5000);
        });
}


function sendInterest(subscriptionId, subscriptionName, recipientId) {
    fetch('php/add_notification.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            subscription_id: subscriptionId,
            subscription_name: subscriptionName,
            recipient_id: recipientId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Vaše žádost byla odeslána!");
        } else {
            alert("Chyba při odesílání žádosti: " + (data.error || 'Neznámá chyba.'));
        }
    })
    .catch(err => {
        console.error("Error:", err);
        alert("Došlo k chybě.");
    });
}
