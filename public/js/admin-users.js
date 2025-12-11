document.addEventListener('DOMContentLoaded', function() {
    // Safety check
    if (typeof window.chartData === 'undefined') return;

    // 1. Age Chart (Doughnut)
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
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });
    }

    // 2. Education Chart (Bar)
    const eduCtx = document.getElementById('educationChart');
    if (eduCtx) {
        new Chart(eduCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: window.chartData.education.labels,
                datasets: [{
                    label: 'Users',
                    data: window.chartData.education.data,
                    backgroundColor: window.chartData.education.colors,
                    borderRadius: 4
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });
    }

    // 3. LOCATION CHART (DRILL-DOWN)
    const locCanvas = document.getElementById('locationChart');
    if (locCanvas) {
        const locCtx = locCanvas.getContext('2d');
        const btnReset = document.getElementById('btnResetLocation');
        const title = document.getElementById('locChartTitle');
        
        const masterData = window.chartData.location;
        const baseColors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'];

        // Cek jika data kosong
        if (!masterData.labels || masterData.labels.length === 0) {
            // Tampilkan placeholder kalau database kosong
            masterData.labels = ['No Data'];
            masterData.data = [1]; 
            baseColors[0] = '#e5e7eb'; // Abu-abu
        }

        let locationChart = new Chart(locCtx, {
            type: 'pie',
            data: {
                labels: masterData.labels,
                datasets: [{
                    data: masterData.data,
                    backgroundColor: baseColors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 12, font: { size: 10 } } }
                },
                onClick: (evt, elements) => {
                    // Cek ada klik di slice?
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const label = locationChart.data.labels[index];
                        
                        // Cek apakah ada data detail (kota) untuk label ini
                        if (masterData.details && masterData.details[label]) {
                            updateChart(label, masterData.details[label]);
                        }
                    }
                }
            }
        });

        function updateChart(provinceName, cityData) {
            const cityLabels = Object.keys(cityData);
            const cityCounts = Object.values(cityData);
            
            locationChart.data.labels = cityLabels;
            locationChart.data.datasets[0].data = cityCounts;
            locationChart.update();

            title.textContent = `${provinceName} (Cities)`;
            btnReset.classList.remove('hidden');
        }

        btnReset.addEventListener('click', () => {
            locationChart.data.labels = masterData.labels;
            locationChart.data.datasets[0].data = masterData.data;
            locationChart.update();

            title.textContent = 'Top Provinces';
            btnReset.classList.add('hidden');
        });
    }
});