// Simple state (equivalent to React useState)

const state = {
  bets: [],
  activeMainTab: "Live",
  activeUserTab: "Dashboard",
  isLoggedIn: false,
  user: null,
};

// Dummy user data generator (same as React example)
function createUser(name, email) {
  return {
    name,
    email,
    balance: 1250.0,
    totalBets: 47,
    wonBets: 28,
    totalWinnings: 3456.78,
  };
}

// --- TAB HANDLING --------------------------------------------------------

function setMainTab(tab) {
  state.activeMainTab = tab;
  updateView();
}

function setUserTab(tab) {
  state.activeUserTab = tab;
  updateView();
}

function initTabs() {
  // Main tabs
  document.querySelectorAll("[data-main-tab]").forEach((btn) => {
    btn.addEventListener("click", () => setMainTab(btn.dataset.mainTab));
  });

  // User tabs
  document.querySelectorAll("[data-user-tab]").forEach((btn) => {
    btn.addEventListener("click", () => setUserTab(btn.dataset.userTab));
  });

  // Logo -> Live
  const logo = document.getElementById("logo-home");
  if (logo) {
    logo.addEventListener("click", () => setMainTab("Live"));
  }
}

// --- LOGIN / LOGOUT ------------------------------------------------------

function openLoginModal() {
  document.getElementById("login-modal").classList.remove("hidden");
}

function closeLoginModal() {
  document.getElementById("login-modal").classList.add("hidden");
}

function login(email, password) {
  // no real auth, just dummy
  state.user = createUser("John Doe", email);
  state.isLoggedIn = true;
  state.activeUserTab = "Dashboard";
  closeLoginModal();
  updateView();
}

function logout() {
  state.user = null;
  state.isLoggedIn = false;
  state.activeMainTab = "Live";
  updateView();
}

function initAuth() {
  const openBtn = document.getElementById("login-open-btn");
  const modal = document.getElementById("login-modal");
  const closeBtn = document.getElementById("login-close");
  const form = document.getElementById("login-form");
  const logoutBtn = document.getElementById("logout-btn");

  openBtn.addEventListener("click", openLoginModal);
  closeBtn.addEventListener("click", closeLoginModal);
  modal.addEventListener("click", (e) => {
    if (e.target === modal) closeLoginModal();
  });

  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const email = document.getElementById("login-email").value.trim();
    const password = document.getElementById("login-password").value.trim();
    if (!email || !password) return;
    login(email, password);
    form.reset();
  });

  logoutBtn.addEventListener("click", logout);
}

// --- WALLET --------------------------------------------------------------

function updateWalletDom() {
  const balance = state.user ? state.user.balance : 0;
  const walletButtonBalance = document.getElementById("wallet-balance");
  const walletBig = document.getElementById("wallet-balance-big");
  const statBalance = document.getElementById("stat-balance");

  const formatted = `$${balance.toFixed(2)}`;
  [walletButtonBalance, walletBig, statBalance].forEach((el) => {
    if (el) el.textContent = formatted;
  });
}

function initWalletForm() {
  const amountInput = document.getElementById("wallet-amount");
  const depositBtn = document.getElementById("deposit-btn");
  const withdrawBtn = document.getElementById("withdraw-btn");

  function getAmount() {
    const value = parseFloat(amountInput.value);
    if (!value || value <= 0) return 0;
    return value;
  }

  depositBtn.addEventListener("click", () => {
    if (!state.user) return;
    const amount = getAmount();
    if (!amount) return;
    state.user.balance += amount;
    state.user.totalBets += 0; // just keep same
    updateWalletDom();
    amountInput.value = "";
  });

  withdrawBtn.addEventListener("click", () => {
    if (!state.user) return;
    const amount = getAmount();
    if (!amount) return;
    if (state.user.balance >= amount) {
      state.user.balance -= amount;
      updateWalletDom();
    }
    amountInput.value = "";
  });
}

// --- BETS -----------------------------------------------------------------

function toggleBet(matchId, matchName, betType, odds) {
  const id = `${matchId}-${betType}`;
  const existingIndex = state.bets.findIndex((b) => b.id === id);

  if (existingIndex !== -1) {
    state.bets.splice(existingIndex, 1);
  } else {
    state.bets.push({
      id,
      matchId,
      matchName,
      betType,
      odds: parseFloat(odds),
      stake: 0,
    });
  }
  renderBetSlip();
}

function initOddsButtons() {
  document.querySelectorAll(".match-card").forEach((card) => {
    const matchId = card.dataset.matchId;
    const matchName = card.dataset.matchName;
    card.querySelectorAll(".odd-btn").forEach((btn) => {
      btn.addEventListener("click", () => {
        const betType = btn.dataset.betType;
        const odds = btn.dataset.odds;
        toggleBet(matchId, matchName, betType, odds);
      });
    });
  });

  document
    .getElementById("clear-bets-btn")
    .addEventListener("click", () => clearBets());
}

function clearBets() {
  state.bets = [];
  renderBetSlip();
}

function renderBetSlip() {
  const listEl = document.getElementById("bet-slip-list");
  const emptyEl = document.getElementById("bet-slip-empty");
  const totalStakeEl = document.getElementById("total-stake");
  const totalReturnsEl = document.getElementById("total-returns");

  listEl.innerHTML = "";

  if (state.bets.length === 0) {
    emptyEl.classList.remove("hidden");
    totalStakeEl.textContent = "$0.00";
    totalReturnsEl.textContent = "$0.00";
    return;
  }

  emptyEl.classList.add("hidden");

  let totalStake = 0;
  let totalReturns = 0;

  state.bets.forEach((bet) => {
    totalStake += bet.stake || 0;
    totalReturns += (bet.stake || 0) * bet.odds;

    const item = document.createElement("div");
    item.className = "bet-item";

    item.innerHTML = `
      <div class="bet-item-header">
        <span>${bet.matchName}</span>
        <span>@ ${bet.odds.toFixed(2)}</span>
      </div>
      <div class="bet-item-sub">Bet: ${bet.betType}</div>
      <div class="bet-bottom">
        <span>Stake</span>
        <input type="number" min="0" step="1" value="${bet.stake || ""}" />
        <button class="bet-remove" title="Remove">✕</button>
      </div>
    `;

    const stakeInput = item.querySelector("input");
    stakeInput.addEventListener("input", (e) => {
      const value = parseFloat(e.target.value);
      bet.stake = !value || value < 0 ? 0 : value;
      renderBetSlip();
    });

    const removeBtn = item.querySelector(".bet-remove");
    removeBtn.addEventListener("click", () => {
      state.bets = state.bets.filter((b) => b.id !== bet.id);
      renderBetSlip();
    });

    listEl.appendChild(item);
  });

  totalStakeEl.textContent = `$${totalStake.toFixed(2)}`;
  totalReturnsEl.textContent = `$${totalReturns.toFixed(2)}`;
}

// --- GENERAL VIEW UPDATE --------------------------------------------------

function updateView() {
  // Main tabs visual
  document.querySelectorAll("[data-main-tab]").forEach((btn) => {
    btn.classList.toggle(
      "active",
      btn.dataset.mainTab === state.activeMainTab
    );
  });

  // Show main sections
  const sections = {
    Live: "section-live",
    Sports: "section-sports",
    Casino: "section-casino",
    Promotions: "section-promotions",
  };

  Object.keys(sections).forEach((tab) => {
    const id = sections[tab];
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.toggle("active", state.activeMainTab === tab);
  });

  // User nav + sections
  const userTabsEl = document.getElementById("user-tabs");
  const walletButton = document.getElementById("wallet-button");
  const loginButton = document.getElementById("login-open-btn");
  const userActionsLogged = document.getElementById("user-actions-logged");
  const sidebar = document.getElementById("sidebar");
  const betSlip = document.getElementById("bet-slip");

  if (state.isLoggedIn && state.user) {
    userTabsEl.classList.remove("hidden");
    walletButton.classList.remove("hidden");
    userActionsLogged.classList.remove("hidden");
    loginButton.classList.add("hidden");
    document.getElementById("user-name-display").textContent = state.user.name;

    // Update stats
    document.getElementById("stat-total-bets").textContent =
      state.user.totalBets;
    document.getElementById("stat-won-bets").textContent =
      state.user.wonBets;
    document.getElementById(
      "stat-total-winnings"
    ).textContent = `$${state.user.totalWinnings.toFixed(2)}`;

    updateWalletDom();
  } else {
    userTabsEl.classList.add("hidden");
    walletButton.classList.add("hidden");
    userActionsLogged.classList.add("hidden");
    loginButton.classList.remove("hidden");
  }

  // User tab buttons
  document.querySelectorAll("[data-user-tab]").forEach((btn) => {
    btn.classList.toggle(
      "active",
      btn.dataset.userTab === state.activeUserTab
    );
  });

  // User sections
  const userSections = {
    Dashboard: "section-dashboard",
    MyBets: "section-mybets",
    Wallet: "section-wallet",
  };
  Object.keys(userSections).forEach((tab) => {
    const id = userSections[tab];
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.toggle(
      "active",
      state.isLoggedIn && state.activeUserTab === tab
    );
  });

  // Show/hide sidebar and bet slip like React logic
  const showSidebar =
    !state.isLoggedIn &&
    (state.activeMainTab === "Live" || state.activeMainTab === "Sports");
  sidebar.classList.toggle("hidden", !showSidebar);

  const showBetSlip =
    state.activeMainTab === "Live" || state.activeMainTab === "Sports";
  betSlip.classList.toggle("hidden", !showBetSlip);
}

// --- INIT -----------------------------------------------------------------

document.addEventListener("DOMContentLoaded", () => {
  initTabs();
  initAuth();
  initWalletForm();
  initOddsButtons();
  renderBetSlip();
  updateView();
});
