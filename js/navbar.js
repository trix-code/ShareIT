function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.classList.toggle('hidden');
    if (!dropdown.classList.contains('hidden')) {
        fetchNotifications();
    }
}

function updateNotificationIcon() {
    fetch('php/fetch_notifications.php')
        .then(response => response.json())
        .then(data => {
            const notificationCount = data.notifications.filter(n => n.is_read === 0).length;
            const notificationBadge = document.getElementById('notificationBadge');

            if (notificationCount > 0) {
                notificationBadge.textContent = notificationCount;
                notificationBadge.classList.remove('hidden');
            } else {
                notificationBadge.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Chyba při aktualizaci notifikační ikony:', error);
        });
}

// Aktualizace notifikační ikony každých 30 sekund
setInterval(updateNotificationIcon, 30000);
updateNotificationIcon();
