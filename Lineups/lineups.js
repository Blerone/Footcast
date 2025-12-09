// Mobile navigation & header scroll
const toggleBtn = document.getElementById("menu-toggle");
const mobileNav = document.getElementById("mobile-nav");
const header = document.querySelector(".header");

if (toggleBtn && mobileNav) {
  toggleBtn.addEventListener("click", () => {
    mobileNav.classList.toggle("open");
    toggleBtn.classList.toggle("open");
  });
}

window.addEventListener("scroll", () => {
  if (window.scrollY > 50) {
    header.classList.add("scrolled");
  } else {
    header.classList.remove("scrolled");
  }
});

// Tabs: Lineups / Standings
const tabButtons = document.querySelectorAll(".tab-btn");
const tabPanels = document.querySelectorAll(".tab-panel");

tabButtons.forEach((btn) => {
  btn.addEventListener("click", () => {
    const target = btn.dataset.tabTarget;

    tabButtons.forEach((b) => b.classList.remove("active"));
    tabPanels.forEach((p) => p.classList.remove("active"));

    const targetPanel = document.querySelector(`[data-tab="${target}"]`);
    if (targetPanel) {
      btn.classList.add("active");
      targetPanel.classList.add("active");
    }
  });
});
