// Scroll progress bar
if ('fonts' in document) {
    document.fonts
        .load('24px "Material Symbols Outlined"')
        .then(() => document.documentElement.classList.add('material-icons-ready'))
        .catch(() => {});
} else {
    document.documentElement.classList.add('material-icons-ready');
}

const progress = document.querySelector('.scroll-progress');
if (progress) {
    const setProgress = () => {
        const h = document.documentElement;
        const scrolled = h.scrollTop / (h.scrollHeight - h.clientHeight || 1);
        progress.style.transform = `scaleX(${Math.min(Math.max(scrolled, 0), 1)})`;
    };
    document.addEventListener('scroll', setProgress, { passive: true });
    window.addEventListener('resize', setProgress);
    setProgress();
}

// Scroll-reveal — adds .is-visible when element enters the viewport
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
if (!reduceMotion) {
    const io = new IntersectionObserver(
        (entries) => {
            entries.forEach((e) => {
                if (e.isIntersecting) {
                    e.target.classList.add('is-visible');
                    io.unobserve(e.target);
                }
            });
        },
        { threshold: 0.15 }
    );
    document.querySelectorAll('.reveal').forEach((el) => io.observe(el));
} else {
    document.querySelectorAll('.reveal').forEach((el) => el.classList.add('is-visible'));
}

// Mobile sidebar toggle
const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebar-toggle');
const sidebarBackdrop = document.getElementById('sidebar-backdrop');
const closeSidebar = () => {
    sidebar?.classList.add('-translate-x-full');
    sidebarBackdrop?.classList.add('hidden');
};
sidebarToggle?.addEventListener('click', () => {
    sidebar?.classList.toggle('-translate-x-full');
    sidebarBackdrop?.classList.toggle('hidden');
});
sidebarBackdrop?.addEventListener('click', closeSidebar);
