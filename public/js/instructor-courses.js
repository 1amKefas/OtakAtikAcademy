document.addEventListener('DOMContentLoaded', function() {
    // Flash Message Handler
    const flashMessage = document.querySelector('.fixed.bottom-6');
    if (flashMessage) {
        setTimeout(() => {
            flashMessage.style.transition = 'opacity 0.5s ease-out';
            flashMessage.style.opacity = '0';
            setTimeout(() => {
                flashMessage.remove();
            }, 500);
        }, 4000);
    }
});