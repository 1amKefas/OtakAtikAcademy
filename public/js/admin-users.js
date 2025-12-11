document.addEventListener('DOMContentLoaded', function() {
    // Pastikan data tersedia sebelum render chart
    if (typeof window.chartData === 'undefined') return;

    // 1. Age Distribution Chart
    const ageCtx = document.getElementById('ageChart');
    if (ageCtx) {
        new Chart(ageCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: window.chartData.age.labels,
                datasets: [{
                    data: window.chartData.age.data,
                    backgroundColor: window.chartData.age.colors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // 2. Education Level Chart
    const educationCtx = document.getElementById('educationChart');
    if (educationCtx) {
        new Chart(educationCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: window.chartData.education.labels,
                datasets: [{
                    label: 'Users',
                    data: window.chartData.education.data,
                    backgroundColor: window.chartData.education.colors,
                    borderWidth: 0,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // 3. Location Distribution Chart
    // --- LOCATION CHART (DRILL DOWN) ---
    const locCtx = document.getElementById('locationChart').getContext('2d');
    const btnReset = document.getElementById('btnResetLocation');
    const title = document.getElementById('locChartTitle');
    
    // Data Master dari Controller
    const masterData = window.chartData.location; 
    const baseColors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'];

    let locationChart = new Chart(locCtx, {
        type: 'pie', // Pie chart lebih enak buat drill-down
        data: {
            labels: masterData.labels,
            datasets: [{
                data: masterData.data,
                backgroundColor: baseColors,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right', labels: { boxWidth: 12, font: { size: 10 } } },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ` ${context.label}: ${context.raw} Users`;
                        }
                    }
                }
            },
            onClick: (evt, elements) => {
                // Logic Drill Down: Kalau klik slice provinsi
                if (elements.length > 0) {
                    const index = elements[0].index;
                    const provinceName = locationChart.data.labels[index];
                    
                    // Cek apakah ada detail kotanya
                    if (masterData.details[provinceName]) {
                        showCities(provinceName, masterData.details[provinceName]);
                    }
                }
            }
        }
    });

    // Fungsi Masuk ke Level Kota
    function showCities(provinceName, cityData) {
        // cityData bentuknya: {'Bandung': 10, 'Cimahi': 2}
        const cityLabels = Object.keys(cityData);
        const cityCounts = Object.values(cityData);
        
        // Update Chart
        locationChart.data.labels = cityLabels;
        locationChart.data.datasets[0].data = cityCounts;
        locationChart.update();

        // Update UI
        title.textContent = `Cities in ${provinceName}`;
        btnReset.classList.remove('hidden');
    }

    // Fungsi Balik ke Level Provinsi
    btnReset.addEventListener('click', () => {
        locationChart.data.labels = masterData.labels;
        locationChart.data.datasets[0].data = masterData.data;
        locationChart.update();

        title.textContent = 'Top Provinces';
        btnReset.classList.add('hidden');
    });
});

// Expose fungsi ke window agar bisa dipanggil oleh onclick attribute di HTML
window.editUser = function(userId) {
    // Implementasi logika edit, misalnya redirect atau buka modal
    console.log('Editing user:', userId);
    alert('Edit user functionality for user ID: ' + userId);
};

window.addUser = function() {
    console.log('Adding new user');
    alert('Add new user functionality');
};