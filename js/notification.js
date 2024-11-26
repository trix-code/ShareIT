function loadNotifications() {
    fetch('php/fetch_notifications.php')
        .then(response => response.json())
        .then(data => {
            const notificationsList = document.getElementById('notificationsList');
            notificationsList.innerHTML = '';

            if (data.success && data.notifications.length > 0) {
                data.notifications.forEach(notification => {
                    const listItem = document.createElement('li');
                    listItem.textContent = `${notification.sender_name} má zájem o ${notification.subscription_name} - ${notification.created_at}`;
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
