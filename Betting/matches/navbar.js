// Mobile Menu Toggle
const mobileMenuToggle = document.getElementById('mobileMenuToggle');
const navbarMenu = document.querySelector('.navbar-menu');

mobileMenuToggle.addEventListener('click', () => {
    navbarMenu.classList.toggle('active');
    mobileMenuToggle.classList.toggle('active');
});

// Close mobile menu when clicking on a link
const navbarLinks = document.querySelectorAll('.navbar-link');
navbarLinks.forEach(link => {
    link.addEventListener('click', () => {
        navbarMenu.classList.remove('active');
        mobileMenuToggle.classList.remove('active');
    });
});

// Profile Dropdown
const profileButton = document.getElementById('profileButton');
const profileDropdown = document.getElementById('profileDropdown');

profileButton.addEventListener('click', (e) => {
    e.stopPropagation();
    profileDropdown.classList.toggle('active');
});

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
    if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
        profileDropdown.classList.remove('active');
    }
});

// Search Functionality
const searchInput = document.getElementById('searchInput');
const searchButton = document.getElementById('searchButton');

searchButton.addEventListener('click', () => {
    performSearch();
});

searchInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        performSearch();
    }
});

function performSearch() {
    const searchTerm = searchInput.value.trim();
    if (searchTerm) {
        console.log('Searching for:', searchTerm);
        // Add your search functionality here
        alert(`Searching for: ${searchTerm}`);
    }
}

// Money Button Functionality
const moneyButton = document.getElementById('moneyButton');
const moneyAmount = document.getElementById('moneyAmount');

// Initialize money amount (you can load this from localStorage or API)
let currentBalance = parseFloat(localStorage.getItem('userBalance')) || 0.00;
updateMoneyDisplay();

moneyButton.addEventListener('click', () => {
    // Add your deposit/withdraw functionality here
    console.log('Money button clicked');
    // Example: Open deposit modal or navigate to deposit page
    alert('Deposit/Withdraw functionality');
});

function updateMoneyDisplay() {
    moneyAmount.textContent = currentBalance.toFixed(2);
}

// Function to update balance (can be called from other parts of your app)
function updateBalance(amount) {
    currentBalance = amount;
    localStorage.setItem('userBalance', currentBalance.toString());
    updateMoneyDisplay();
}

// Example: Add some demo balance on load (remove this in production)
// updateBalance(1000.00);

// Active Link Highlighting
const currentPath = window.location.pathname;
navbarLinks.forEach(link => {
    if (link.getAttribute('href') === currentPath) {
        link.classList.add('active');
    }
});

// Smooth scroll for anchor links
navbarLinks.forEach(link => {
    link.addEventListener('click', (e) => {
        const href = link.getAttribute('href');
        if (href.startsWith('#')) {
            e.preventDefault();
            const targetId = href.substring(1);
            const targetElement = document.getElementById(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
});

// Close mobile menu on window resize
window.addEventListener('resize', () => {
    if (window.innerWidth > 968) {
        navbarMenu.classList.remove('active');
        mobileMenuToggle.classList.remove('active');
    }
});

