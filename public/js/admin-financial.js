document.addEventListener('DOMContentLoaded', function() {
    
    // --- 1. Revenue Overview Chart ---
    const revenueCanvas = document.getElementById('revenueChart');
    if (revenueCanvas) {
        const revenueLabels = JSON.parse(revenueCanvas.getAttribute('data-labels'));
        const revenueData = JSON.parse(revenueCanvas.getAttribute('data-values'));

        const revenueCtx = revenueCanvas.getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'Revenue (Rp)',
                    data: revenueData,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 4], color: '#f3f4f6' },
                        ticks: {
                            callback: function(value) {
                                if(value >= 1000000000) return 'Rp' + (value/1000000000).toFixed(1) + 'M';
                                if(value >= 1000000) return 'Rp' + (value/1000000).toFixed(1) + 'jt';
                                if(value >= 1000) return 'Rp' + (value/1000).toFixed(0) + 'rb';
                                return 'Rp' + value;
                            },
                            font: { size: 11 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    }

    // --- 2. Course Performance Chart ---
    const courseCanvas = document.getElementById('courseChart');
    if (courseCanvas) {
        const courseLabels = JSON.parse(courseCanvas.getAttribute('data-labels'));
        const courseData = JSON.parse(courseCanvas.getAttribute('data-values'));
        const courseColors = JSON.parse(courseCanvas.getAttribute('data-colors'));

        const courseCtx = courseCanvas.getContext('2d');
        new Chart(courseCtx, {
            type: 'doughnut',
            data: {
                labels: courseLabels,
                datasets: [{
                    data: courseData,
                    backgroundColor: courseColors,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 11 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                let value = context.raw;
                                label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value);
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
});