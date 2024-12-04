
function loadSubscriptionsForFinance() {
    return fetch('subscription_actions.php?action=loadSubscriptionsForFinance')
        .then(response => response.json())
        .then(data => {
            console.log("Data načtená ze serveru:", data);
            return data;
        });
}

function getGraphDataFromSubscriptions() {
    return loadSubscriptionsForFinance().then(subscriptions => {
        let monthlyExpenses = 0;
        let yearlyExpenses = 0;
        const categoryExpenses = {};

        subscriptions.forEach(sub => {
            if (sub.frequency === 'měsíčně') {
                monthlyExpenses += parseFloat(sub.price);
            } else if (sub.frequency === 'roční') {
                yearlyExpenses += parseFloat(sub.price);
            }

            if (categoryExpenses[sub.category]) {
                categoryExpenses[sub.category] += parseFloat(sub.price);
            } else {
                categoryExpenses[sub.category] = parseFloat(sub.price);
            }
        });

        return { monthlyExpenses, yearlyExpenses, categoryExpenses };
    });
}

const chartOptions = {
    responsive: true,
    maintainAspectRatio: true,
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                font: {
                    size: 20
                }
            }
        },
        x: {
            ticks: {
                display: false,
                font: {
                    size: 20
                }
            },
            grid: {
                display: false
            }
        }
    },
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                font: {
                    size: 20
                }
            }
        },
        tooltip: {
            titleFont: {
                size: 18
            },
            bodyFont: {
                size: 16
            }
        }
    }
};

// Vytvoření grafů
const monthlyExpensesChart = new Chart(document.getElementById('monthlyExpensesChart'), {
    type: 'bar',
    data: {
        labels: ['Měsíční Výdaje'],
        datasets: [{
            label: 'Výdaje v Kč',
            data: [0],
            backgroundColor: 'rgba(75, 192, 192, 0.7)',
            borderColor: 'rgba(75, 192, 192, 10)',
            borderWidth: 1
        }]
    },
    options: chartOptions
});

const yearlyExpensesChart = new Chart(document.getElementById('yearlyExpensesChart'), {
    type: 'bar',
    data: {
        labels: ['Roční Výdaje'],
        datasets: [{
            label: 'Výdaje v Kč',
            data: [0],
            backgroundColor: 'rgba(153, 102, 255, 0.7)',
            borderColor: 'rgba(153, 102, 255, 10)',
            borderWidth: 1
        }]
    },
    options: chartOptions
});

const categoryChart = new Chart(document.getElementById('categoryChart'), {
    type: 'doughnut',
    data: {
        labels: [],
        datasets: [{
            label: 'Výdaje podle kategorií',
            data: [],
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(60, 179, 113, 0.5)',
                'rgba(153, 10, 55, 0.5)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(205, 254, 194, 1)',
                'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: chartOptions
});

// Aktualizace grafů
function updateCharts() {
    getGraphDataFromSubscriptions().then(({ monthlyExpenses, yearlyExpenses, categoryExpenses }) => {
        console.log("Měsíční výdaje:", monthlyExpenses);
        console.log("Roční výdaje:", yearlyExpenses);
        console.log("Výdaje podle kategorií:", categoryExpenses);

        monthlyExpensesChart.data.datasets[0].data = [monthlyExpenses];
        monthlyExpensesChart.update();
        document.getElementById('monthlyExpenseAmount').textContent = `Měsíční výdaje jsou: ${monthlyExpenses} Kč`;

        yearlyExpensesChart.data.datasets[0].data = [yearlyExpenses];
        yearlyExpensesChart.update();
        document.getElementById('yearlyExpenseAmount').textContent = `Roční výdaje jsou: ${yearlyExpenses} Kč`;

        categoryChart.data.labels = Object.keys(categoryExpenses);
        categoryChart.data.datasets[0].data = Object.values(categoryExpenses);
        categoryChart.update();
    });
}

updateCharts();
