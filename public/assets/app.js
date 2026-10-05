const sidebar = document.querySelector('.sidebar');
const sidebarToggle = document.querySelector('.sidebar-toggle');
const sidebarClose = document.querySelector('.sidebar-close');
const sidebarBackdrop = document.querySelector('.sidebar-backdrop');
const mobileSidebar = window.matchMedia('(max-width: 720px)');
const desktopSidebarPreference = 'tendajatim-sidebar-hidden';

if (sidebar && sidebarToggle && sidebarClose && sidebarBackdrop) {
    const closeMobileSidebar = () => {
        sidebar.classList.remove('is-open');
        sidebarBackdrop.classList.remove('is-visible');
        document.body.classList.remove('sidebar-open');
        sidebarToggle.setAttribute('aria-expanded', 'false');
        sidebarToggle.setAttribute('aria-label', 'Buka menu navigasi');
    };

    if (!mobileSidebar.matches && localStorage.getItem(desktopSidebarPreference) === 'true') {
        document.body.classList.add('sidebar-collapsed');
        sidebarToggle.setAttribute('aria-expanded', 'false');
        sidebarToggle.setAttribute('aria-label', 'Tampilkan menu navigasi');
    }

    sidebarToggle.addEventListener('click', () => {
        if (mobileSidebar.matches) {
            const isOpen = sidebar.classList.toggle('is-open');
            sidebarBackdrop.classList.toggle('is-visible', isOpen);
            document.body.classList.toggle('sidebar-open', isOpen);
            sidebarToggle.setAttribute('aria-expanded', String(isOpen));
            sidebarToggle.setAttribute('aria-label', isOpen ? 'Tutup menu navigasi' : 'Buka menu navigasi');
            return;
        }

        const isHidden = document.body.classList.toggle('sidebar-collapsed');
        localStorage.setItem(desktopSidebarPreference, String(isHidden));
        sidebarToggle.setAttribute('aria-expanded', String(!isHidden));
        sidebarToggle.setAttribute('aria-label', isHidden ? 'Tampilkan menu navigasi' : 'Sembunyikan menu navigasi');
    });

    sidebarClose.addEventListener('click', closeMobileSidebar);
    sidebarBackdrop.addEventListener('click', closeMobileSidebar);
    sidebar.querySelectorAll('nav a').forEach((link) => link.addEventListener('click', closeMobileSidebar));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && mobileSidebar.matches) {
            closeMobileSidebar();
        }
    });

    mobileSidebar.addEventListener('change', (event) => {
        closeMobileSidebar();
        if (!event.matches) {
            const isHidden = localStorage.getItem(desktopSidebarPreference) === 'true';
            document.body.classList.toggle('sidebar-collapsed', isHidden);
            sidebarToggle.setAttribute('aria-expanded', String(!isHidden));
            sidebarToggle.setAttribute('aria-label', isHidden ? 'Tampilkan menu navigasi' : 'Sembunyikan menu navigasi');
        } else {
            document.body.classList.remove('sidebar-collapsed');
        }
    });
}

document.querySelectorAll('[data-slideshow]').forEach((slideshow) => {
    const slides = [...slideshow.querySelectorAll('.gallery-slide')];
    const indicators = [...slideshow.querySelectorAll('.gallery-indicator')];
    const previousButton = slideshow.querySelector('.gallery-previous');
    const nextButton = slideshow.querySelector('.gallery-next');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let currentIndex = 0;
    let timer;

    if (slides.length < 2 || indicators.length !== slides.length || !previousButton || !nextButton) {
        return;
    }

    const showSlide = (index) => {
        currentIndex = (index + slides.length) % slides.length;
        slides.forEach((slide, slideIndex) => {
            const isActive = slideIndex === currentIndex;
            slide.hidden = !isActive;
            slide.classList.toggle('is-active', isActive);
            indicators[slideIndex].classList.toggle('is-active', isActive);
            indicators[slideIndex].setAttribute('aria-pressed', String(isActive));
        });
    };

    const stopSlideshow = () => window.clearInterval(timer);
    const startSlideshow = () => {
        stopSlideshow();
        if (!reduceMotion.matches) {
            timer = window.setInterval(() => showSlide(currentIndex + 1), 5000);
        }
    };

    previousButton.addEventListener('click', () => {
        showSlide(currentIndex - 1);
        startSlideshow();
    });
    nextButton.addEventListener('click', () => {
        showSlide(currentIndex + 1);
        startSlideshow();
    });
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            showSlide(index);
            startSlideshow();
        });
    });
    slideshow.addEventListener('mouseenter', stopSlideshow);
    slideshow.addEventListener('mouseleave', startSlideshow);
    slideshow.addEventListener('focusin', stopSlideshow);
    slideshow.addEventListener('focusout', (event) => {
        if (!slideshow.contains(event.relatedTarget)) {
            startSlideshow();
        }
    });
    reduceMotion.addEventListener('change', startSlideshow);

    startSlideshow();
});
