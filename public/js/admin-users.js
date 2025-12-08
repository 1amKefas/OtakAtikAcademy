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
    const locationCtx = document.getElementById('locationChart');
    if (locationCtx) {
        new Chart(locationCtx.getContext('2d'), {
            type: 'pie',
            data: {
                labels: window.chartData.location.labels,
                datasets: [{
                    data: window.chartData.location.data,
                    backgroundColor: window.chartData.location.colors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right' } }
            }
        });
    }
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