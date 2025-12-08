// Function to mark notification as read
window.markAsRead = function(notificationId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
        }
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              // Remove notification from dropdown without reloading
              const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
              if (notificationElement) {
                  notificationElement.style.opacity = '0.5';
                  notificationElement.style.textDecoration = 'line-through';
              }
              // Update badge count
              const badge = document.querySelector('#notificationBtn span');
              if (badge) {
                  let count = parseInt(badge.textContent);
                  if (count > 1) {
                      badge.textContent = count - 1;
                  } else {
                      badge.remove();
                  }
              }
          }
      }).catch(error => console.error('Error:', error));
};

document.addEventListener('DOMContentLoaded', () => {
    // Dropdown Toggles
    const profileBtn = document.getElementById('profileBtn');
    const profileDropdown = document.getElementById('profileDropdown');
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationDropdown = document.getElementById('notificationDropdown');
    
    if (profileBtn && profileDropdown) {
        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            profileDropdown.classList.toggle('hidden');
            if (notificationDropdown) notificationDropdown.classList.add('hidden');
        });
    }
    
    if (notificationBtn && notificationDropdown) {
        notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationDropdown.classList.toggle('hidden');
            if (profileDropdown) profileDropdown.classList.add('hidden');
        });
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', () => {
        if (profileDropdown) profileDropdown.classList.add('hidden');
        if (notificationDropdown) notificationDropdown.classList.add('hidden');
    });
});