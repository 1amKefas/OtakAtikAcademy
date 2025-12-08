// Expose functions to global window object so HTML onclick attributes can find them
window.switchTab = function(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('text-purple-600', 'border-purple-600');
        btn.classList.add('text-gray-600', 'border-transparent');
    });
    
    // Show selected tab
    const selectedTab = document.getElementById(tabName + '-tab');
    if (selectedTab) {
        selectedTab.classList.remove('hidden');
    }
    
    // Add active class to clicked button
    const activeBtn = document.getElementById('tab-' + tabName);
    if (activeBtn) {
        activeBtn.classList.remove('text-gray-600', 'border-transparent');
        activeBtn.classList.add('text-purple-600', 'border-purple-600');
    }
};

window.updateLocalePreview = function() {
    const enLabel = document.getElementById('locale-en');
    const idLabel = document.getElementById('locale-id');
    const selectedLocale = document.querySelector('input[name="locale"]:checked').value;

    // Reset styles
    document.querySelectorAll('input[name="locale"]').forEach(radio => {
        const label = radio.closest('label');
        const icon = label.querySelector('i.fa-check');
        if (icon) icon.style.display = 'none';
    });

    // Update selected style
    if (selectedLocale === 'en' && enLabel) {
        enLabel.style.borderColor = '#9333EA';
        enLabel.style.backgroundColor = '#F3E8FF';
        enLabel.querySelector('i.fa-check').style.display = 'block';
        
        if (idLabel) {
            idLabel.style.borderColor = '#E5E7EB';
            idLabel.style.backgroundColor = 'white';
        }
    } else if (selectedLocale === 'id' && idLabel) {
        idLabel.style.borderColor = '#9333EA';
        idLabel.style.backgroundColor = '#F3E8FF';
        idLabel.querySelector('i.fa-check').style.display = 'block';

        if (enLabel) {
            enLabel.style.borderColor = '#E5E7EB';
            enLabel.style.backgroundColor = 'white';
        }
    }
};