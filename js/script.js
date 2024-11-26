let subscriptions = [];

const subscriptionForm = document.getElementById('subscriptionForm');
const noSubscriptionsMessage = document.getElementById('noSubscriptionsMessage');
const subscriptionFormContainer = document.getElementById('subscriptionFormContainer');
const subscriptionsContainer = document.getElementById('subscriptionsContainer');
const subscriptionsList = document.getElementById('subscriptions');

function showSubscriptionForm() {
    subscriptionFormContainer.classList.remove('hidden'); 
    noSubscriptionsMessage.classList.add('hidden'); 
    subscriptionsContainer.classList.add('hidden');
}

function hideSubscriptionForm() {
    subscriptionFormContainer.classList.add('hidden'); 
    if (subscriptions.length === 0) {
        noSubscriptionsMessage.classList.remove('hidden'); 
    } else {
        subscriptionsContainer.classList.remove('hidden'); 
    }
}

// Handle form submission for adding or updating a subscription
subscriptionForm.addEventListener('submit', (e) => {
    e.preventDefault();

    const name = document.getElementById('serviceName').value;
    const price = parseFloat(document.getElementById('price').value);
    const frequency = document.getElementById('paymentFrequency').value;
    const nextPayment = document.getElementById('nextPayment').value;
    const category = document.getElementById('category').value;
    const editingIndex = subscriptionForm.dataset.editingIndex;

    const data = {
        name,
        price,
        frequency,
        nextPayment,
        category,
        action: editingIndex !== undefined ? 'update' : 'add'
    };

    if (editingIndex !== undefined) {
        data.id = subscriptions[editingIndex].id;
    }

    fetch('subscription_actions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                loadSubscriptions();
                subscriptionForm.reset();
                hideSubscriptionForm();
            } else {
                alert("Chyba při ukládání předplatného.");
            }
        });
});

// Load subscriptions from the server
function loadSubscriptions() {
    fetch('subscription_actions.php?action=load')
        .then(response => response.json())
        .then(data => {
            subscriptions = data;
            updateSubscriptions();
        });
}

// Update the subscriptions list dynamically
function updateSubscriptions() {
    subscriptionsList.innerHTML = '';

    if (subscriptions.length === 0) {
        noSubscriptionsMessage.classList.remove('hidden');
        subscriptionsContainer.classList.add('hidden');
    } else {
        noSubscriptionsMessage.classList.add('hidden');
        subscriptionsContainer.classList.remove('hidden');
        subscriptions.forEach((sub, index) => {
            const subCard = document.createElement('div');
            subCard.classList.add('subscription-card');

            const today = new Date();
            const nextPaymentDate = new Date(sub.next_payment);
            const daysLeft = Math.ceil((nextPaymentDate - today) / (1000 * 60 * 60 * 24));
            let paymentWarning = '';

            if (daysLeft <= 7 && daysLeft > 4) {
                paymentWarning = `<p style="color: red;"><b>Platba za ${daysLeft} dní!</b></p>`;
            } else if (daysLeft <= 4 && daysLeft > 0) {
                paymentWarning = `<p style="color: red;"><b>Platba za ${daysLeft} dny!</b></p>`;
            } else if (daysLeft === 0) {
                paymentWarning = `<p style="color: red;"><b>Platba dnes!</b></p>`;
            } else if (daysLeft < 0) {
                paymentWarning = `<p style="color: red;"><b>Platba po splatnosti!</b></p>`;
            }

            subCard.innerHTML = `
                <h4>${sub.name} (${sub.category})</h4>
                <p>Cena: <b>${sub.price} Kč</b></p>
                <p>Frekvence: <b>${sub.frequency}</b></p>
                <p>Další platba: <b>${sub.next_payment}</b></p>
                ${paymentWarning}
                <button onclick="editSubscription(${index})">Upravit</button>
                <button onclick="deleteSubscription(${sub.id})">Odstranit</button>
            `;
            subscriptionsList.appendChild(subCard);
        });
    }
}

// Delete a subscription
function deleteSubscription(id) {
    fetch('subscription_actions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ action: 'delete', id })
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                loadSubscriptions();
            } else {
                alert("Chyba při mazání předplatného.");
            }
        });
}

// Edit a subscription
function editSubscription(index) {
    const subscription = subscriptions[index];

    document.getElementById('serviceName').value = subscription.name;
    document.getElementById('price').value = subscription.price;
    document.getElementById('paymentFrequency').value = subscription.frequency;
    document.getElementById('nextPayment').value = subscription.next_payment;
    document.getElementById('category').value = subscription.category;

    subscriptionForm.dataset.editingIndex = index;
    document.querySelector('button[type="submit"]').textContent = 'Upravit předplatné';

    showSubscriptionForm();
}

// Load subscriptions when the page loads
loadSubscriptions();
