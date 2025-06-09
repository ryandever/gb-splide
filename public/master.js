document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.splide').forEach((el) => {
        const options = el.dataset.splide ? JSON.parse(el.dataset.splide) : {};
        new Splide(el, options).mount();
    });
});