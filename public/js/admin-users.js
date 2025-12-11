document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.chartData === 'undefined') return;

    // 1. Age & Education (Sama kayak sebelumnya, copy paste aja yang lama atau pake ini)
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
                    borderRadius: 5
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });
    }

    // --- 3. LOCATION CHART (DRILL-DOWN LOGIC) ---
    const locCanvas = document.getElementById('locationChart');
    if (locCanvas) {
        const locCtx = locCanvas.getContext('2d');
        const btnReset = document.getElementById('btnResetLocation');
        const title = document.getElementById('locChartTitle');
        
        const masterData = window.chartData.location; // Data Hierarki
        const baseColors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'];

        let locationChart = new Chart(locCtx, {
            type: 'pie',
            data: {
                labels: masterData.labels, // Awalnya Nama Provinsi
                datasets: [{
                    data: masterData.data, // Awalnya Total per Provinsi
                    backgroundColor: baseColors,
                    borderWidth: 2,
                    borderColor: '#fff'
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
                // [FITUR UTAMA] Klik Slice -> Drill Down ke Kota
                onClick: (evt, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const provinceName = locationChart.data.labels[index];
                        
                        // Cek apakah ada data kota untuk provinsi ini
                        if (masterData.details && masterData.details[provinceName]) {
                            showCities(provinceName, masterData.details[provinceName]);
                        }
                    }
                }
            }
        });

        // Tampilkan Chart Kota
        function showCities(provinceName, cityData) {
            const cityLabels = Object.keys(cityData);
            const cityCounts = Object.values(cityData);
            
            locationChart.data.labels = cityLabels;
            locationChart.data.datasets[0].data = cityCounts;
            locationChart.update();

            title.textContent = `Kota di ${provinceName}`;
            btnReset.classList.remove('hidden');
        }

        // Kembali ke Chart Provinsi
        if(btnReset){
            btnReset.addEventListener('click', () => {
                locationChart.data.labels = masterData.labels;
                locationChart.data.datasets[0].data = masterData.data;
                locationChart.update();

                title.textContent = 'Top Provinces';
                btnReset.classList.add('hidden');
            });
        }
    }
});