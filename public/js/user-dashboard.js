document.addEventListener('DOMContentLoaded', () => {
    const sliderContainer = document.getElementById('cat-scroll');
    const scrollLeftBtn = document.getElementById('scroll-left');
    const scrollRightBtn = document.getElementById('scroll-right');

    if (sliderContainer && scrollLeftBtn && scrollRightBtn) {
        scrollLeftBtn.addEventListener('click', () => {
            sliderContainer.scrollBy({ left: -320, behavior: 'smooth' });
        });

        scrollRightBtn.addEventListener('click', () => {
            sliderContainer.scrollBy({ left: 320, behavior: 'smooth' });
        });
    }
});