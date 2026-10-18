(function () {
    const t = localStorage.getItem("theme");
    if (t === "dark") document.documentElement.classList.add("dark-mode");
})();

document.addEventListener("DOMContentLoaded", function () {

    /* ---------- Sidebar ---------- */
    const sidebar = document.getElementById("sidebar");
    const mainWrapper = document.getElementById("mainWrapper");
    const sidebarToggleBtn = document.getElementById("sidebarToggleBtn");

    if (window.innerWidth > 768 && sidebar && mainWrapper) {
        if (localStorage.getItem("sidebarCollapsed") === "true") {
            sidebar.classList.add("collapsed");
            mainWrapper.classList.add("expanded");
        }
    }
    if (sidebarToggleBtn && sidebar && mainWrapper) {
        sidebarToggleBtn.addEventListener("click", function (e) {
            e.preventDefault(); e.stopPropagation();
            sidebar.classList.toggle("collapsed");
            mainWrapper.classList.toggle("expanded");
            localStorage.setItem("sidebarCollapsed",
                sidebar.classList.contains("collapsed") ? "true" : "false");
        });
    }

    /* ---------- Mobile menu ---------- */
    const mobileMenuBtn  = document.getElementById("mobileMenuBtn");
    const sidebarOverlay = document.getElementById("sidebarOverlay");
    if (mobileMenuBtn && sidebar && sidebarOverlay) {
        mobileMenuBtn.addEventListener("click", () => {
            sidebar.classList.add("mobile-open");
            sidebarOverlay.classList.add("show");
            document.body.style.overflow = "hidden";
        });
        const closeMobile = () => {
            sidebar.classList.remove("mobile-open");
            sidebarOverlay.classList.remove("show");
            document.body.style.overflow = "";
        };
        sidebarOverlay.addEventListener("click", closeMobile);
        sidebar.querySelectorAll(".sidebar-nav a").forEach(a => {
            a.addEventListener("click", () => {
                if (window.innerWidth <= 768) closeMobile();
            });
        });
    }

        /* ---------------- Theme toggle ---------------- */
const themeToggle = document.getElementById("themeToggle");

function syncToggleUI() {
    if (!themeToggle) return;
    const isDark = document.documentElement.classList.contains("dark-mode");
    themeToggle.setAttribute("aria-pressed", isDark ? "true" : "false");
    themeToggle.setAttribute("title",
        isDark ? "Switch to light mode" : "Switch to dark mode");
}
syncToggleUI();

if (themeToggle) {
    themeToggle.addEventListener("click", function () {
        // 1) Flip theme
        document.documentElement.classList.toggle("dark-mode");
        const isDark = document.documentElement.classList.contains("dark-mode");

        // 2) Persist
        try { localStorage.setItem("theme", isDark ? "dark" : "light"); } catch (err) {}

        // 3) Sync ARIA + title
        syncToggleUI();

        // 4) Slight 3D tilt + halo pulse burst
        themeToggle.classList.remove("tt-tilt", "tt-pulse");
        void themeToggle.offsetWidth;           // force reflow to restart both animations
        themeToggle.classList.add("tt-tilt", "tt-pulse");
        setTimeout(() => themeToggle.classList.remove("tt-tilt", "tt-pulse"), 800);

        // 5) Page-wide colour crossfade
        document.documentElement.classList.add("theme-transition");
        setTimeout(() => document.documentElement.classList.remove("theme-transition"), 500);
    });
}

    /* ---------- Profile dropdown ---------- */
    const profileBtn      = document.getElementById("profileBtn");
    const profileDropdown = document.getElementById("profileDropdown");
    if (profileBtn && profileDropdown) {
        profileBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            profileDropdown.classList.toggle("show");
        });
        document.addEventListener("click", (e) => {
            if (!profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.remove("show");
            }
        });
    }

    /* ---------- Confirm dialogs ---------- */
    document.querySelectorAll("[data-confirm]").forEach(el => {
        el.addEventListener("click", (e) => {
            if (!confirm(el.getAttribute("data-confirm"))) e.preventDefault();
        });
    });

    /* ---------- Search on Enter ---------- */
    const searchInput = document.getElementById("searchInput");
    if (searchInput) {
        searchInput.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
                e.preventDefault();
                searchInput.closest("form").submit();
            }
        });
    }

    /* ---------- Ripple (skip icon buttons) ---------- */
    document.querySelectorAll(".btn:not(.icon-btn)").forEach(btn => {
        btn.addEventListener("click", function (e) {
            const rect = this.getBoundingClientRect();
            const ripple = document.createElement("span");
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top  - size / 2;
            ripple.style.cssText = `position:absolute;width:${size}px;height:${size}px;left:${x}px;top:${y}px;background:rgba(255,255,255,0.4);border-radius:50%;transform:scale(0);animation:rippleAnim .6s ease-out;pointer-events:none;`;
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    });

    /* ---------- Scroll animations ---------- */
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("animate-up");
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll(".section-card, .stat-card, .recent-card").forEach(el => observer.observe(el));
});

/* ripple keyframes */
const style = document.createElement("style");
style.textContent = `@keyframes rippleAnim { to { transform: scale(4); opacity: 0; } }`;
document.head.appendChild(style);