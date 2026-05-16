document.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.querySelector('[data-menu-toggle]');
    const menu = document.querySelector('[data-menu]');

    if (menuToggle && menu) {
        menuToggle.addEventListener('click', function () {
            menu.classList.toggle('open');
        });
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('show');
            }
        });
    }, { threshold: 0.12 });

    document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));

    const lightbox = document.createElement('div');
    lightbox.className = 'lightbox';
    lightbox.innerHTML = '<img alt="Preview">';
    document.body.appendChild(lightbox);

    const lbImg = lightbox.querySelector('img');
    document.querySelectorAll('[data-lightbox]').forEach((link) => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            lbImg.src = this.getAttribute('href');
            lightbox.classList.add('open');
        });
    });

    lightbox.addEventListener('click', function () {
        lightbox.classList.remove('open');
        lbImg.src = '';
    });
});
