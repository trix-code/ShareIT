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

// Funkce pro zpracování akce (Potvrdit, Zrušit)
function handleNotificationAction(notificationId, action) {
    fetch('../php/update_notification_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: notificationId, action: action }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                alert(data.message);
                location.reload(); // Refresh the page to reflect changes
            } else {
                alert('Chyba: ' + data.error);
            }
        })
        .catch((error) => {
            console.error('Chyba:', error);
            alert('Nastala chyba při zpracování požadavku.');
        });
}



function sendInterest(subscriptionId, subscriptionName, recipientId) {
    fetch('../php/add_notification.php', {
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

    


function updateNotificationCount() {
    fetch('../php/fetch_notifications.php') 
        .then(response => response.json())
        .then(data => {
            const notificationCount = document.getElementById('notificationCount');
            
            if (data.success) {
                if (data.unreadCount > 0) {
                    notificationCount.textContent = data.unreadCount;
                    notificationCount.style.display = 'inline';  // Zobrazí číslo
                } else {
                    notificationCount.style.display = 'none';  // Skryje číslo, pokud je 0
                }
            }
        })
        .catch(error => {
            console.error('Chyba při načítání počtu notifikací:', error);
        });
}

setInterval(updateNotificationCount, 30000);
updateNotificationCount();  
