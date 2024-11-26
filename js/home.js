function toggleForm() {
    const formContainer = document.getElementById("subscriptionForm");
    const addSubscriptionCard = document.querySelector('.add-subscription-card');
    const subscriptionList = document.querySelector('.subscription-list');
    const categoryFilterContainer = document.getElementById("categoryFilterContainer");

    // Zobrazí formulář a skryje ostatní
    if (formContainer.style.display === 'block') {
        formContainer.style.display = 'none';
        addSubscriptionCard.style.display = 'flex';
        subscriptionList.style.display = 'block';
        showCategoryFilter();
    } else {
        formContainer.style.display = 'block';
        addSubscriptionCard.style.display = 'none';
        subscriptionList.style.display = 'none';
        hideCategoryFilter();
    }
}

function hideCategoryFilter() {
    const categoryFilterContainer = document.getElementById("categoryFilterContainer");
    categoryFilterContainer.style.display = 'none';
}

function showCategoryFilter() {
    const categoryFilterContainer = document.getElementById("categoryFilterContainer");
    categoryFilterContainer.style.display = 'block';
}

const serviceOptions = {
    "Hudba": ["Spotify", "Apple Music"],
    "Filmy": ["Netflix", "HBO Max", "Voyo", "Apple TV", "Disney", "Amazon Prime Video"],
    "Hry": ["Xbox",],
    "Ostatní": ["Youtube", "Dropbox", "Microsoft 365"],
};

const planOptions = {
    "Spotify": { "Family": 299, "Duo": 199 },
    "Apple Music": { "Family": 269, "Individual": 149 },
    "Netflix": { "Basic": 199, "Standard": 299, "Premium": 399 },
    "HBO Max": { "Standard": 200 },
    "Voyo": { "Standard": 159 },
    "Apple TV": { "Standard": 139 },
    "Amazon Prime Video": { "Standard": 79 },
    "Disney": { "Standard": 199 },
    "Xbox": { "Ultimate Game Pass": 399 },
    "Youtube": { "Family": 269,},
    "Dropbox": { "Family": 459,},
    "Microsoft 365": { "Family": 269,},
};  

const maxSpots = {
    "Spotify": {
        "Family": 5, 
        "Duo": 1    
    },
    "Apple Music": {
        "Family": 5,
        "Individual": 0 
    },
    "Netflix": {
        "Basic": 0,     
        "Standard": 1,  
        "Premium": 3    
    },
    "HBO Max": {
        "Standard": 1   
    },
    "Voyo": {
        "Standard": 4  
    },
    "Apple TV": {
        "Standard": 5  
    },
    "Disney": {
        "Standard": 4  
    },
    "Amazon Prime Video": {
        "Standard": 3  
    },
    "Xbox": {
        "Ultimate Game Pass": 99  
    },
    "Youtube": {
        "Family": 5  
    },
    "Dropbox": {
        "Family": 5  
    },
    "Microsoft 365": {
        "Family": 5  
    },

    
};

function updateServiceOptions() {
    const category = document.getElementById("category").value;
    const serviceNameSelect = document.getElementById("serviceName");
    serviceNameSelect.innerHTML = '';
    serviceOptions[category].forEach(service => {
        const option = document.createElement("option");
        option.value = service;
        option.textContent = service;
        serviceNameSelect.appendChild(option);
    });
    updatePlanOptions();
}

function updatePlanOptions() {
    const service = document.getElementById("serviceName").value;
    const planSelect = document.getElementById("plan");
    const priceInput = document.getElementById("price");
    planSelect.innerHTML = '';
    for (const [plan, price] of Object.entries(planOptions[service])) {
        const option = document.createElement("option");
        option.value = plan;
        option.textContent = `${plan} - ${price} Kč`;
        planSelect.appendChild(option);
    }
    priceInput.value = planOptions[service][Object.keys(planOptions[service])[0]];
    updateAvailableSpots(); // Aktualizuje počet volných míst podle vybraného tarifu
}

// Aktualizace ceny podle vybraného tarifu
document.getElementById("plan").addEventListener("change", function () {
    const service = document.getElementById("serviceName").value;
    const selectedPlan = this.value;
    document.getElementById("price").value = planOptions[service][selectedPlan];
    updateAvailableSpots(); 
});

function updateAvailableSpots() {
    const service = document.getElementById("serviceName").value;
    const plan = document.getElementById("plan").value;
    const availableSpotsInput = document.getElementById("availableSpots");

    const maxAvailableSpots = maxSpots[service]?.[plan] ?? 0;
    availableSpotsInput.max = maxAvailableSpots;
    availableSpotsInput.value = Math.min(availableSpotsInput.value, maxAvailableSpots);

    availableSpotsInput.placeholder = `Maximálně ${maxAvailableSpots} volná místa`;
}

document.getElementById("price").addEventListener("input", function () {
    if (this.value < 0) {
        this.value = 0; 
    }
});

document.getElementById("availableSpots").addEventListener("input", function () {
    if (this.value < 0) {
        this.value = 0; 
    }
});


document.getElementById('categoryFilter').addEventListener('change', function () {
    const selectedCategory = this.value;
    const subscriptionCards = document.querySelectorAll('.subscription-card');

    subscriptionCards.forEach(card => {
        const category = card.getAttribute('data-category');
        if (selectedCategory === 'Všechny' || category === selectedCategory) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});

// Funkce pro zobrazení/skrytí detailů předplatného
function toggleDetails(button) {
    const details = button.nextElementSibling;
    if (details.classList.contains('hidden')) {
        details.classList.remove('hidden');
        button.textContent = 'Skrýt detaily';
    } else {
        details.classList.add('hidden');
        button.textContent = 'Číst více';
    }
}

updateServiceOptions();




