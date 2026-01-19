const API_BASE = '..';

document.addEventListener('DOMContentLoaded', () => {
    setupProfileTabs();
    loadProfileStats();
    loadRecentBets();
});

function setupProfileTabs() {
    const tabButtons = document.querySelectorAll('.profile-tab-btn');
    const panels = document.querySelectorAll('.profile-tab-panel');

    tabButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const target = button.getAttribute('data-tab');
            tabButtons.forEach((btn) => {
                btn.classList.remove('active');
                btn.setAttribute('aria-selected', 'false');
            });
            panels.forEach((panel) => panel.classList.remove('active'));

            button.classList.add('active');
            button.setAttribute('aria-selected', 'true');
            document.getElementById(`${target}-panel`).classList.add('active');
        });
    });
}

async function loadProfileStats() {
    try {
        const response = await fetch(`${API_BASE}/parlays.php`);
        const data = await response.json();
        const parlays = data.success ? data.parlays : [];

        const totalBets = parlays.length;
        const wonBets = parlays.filter((p) => p.status === 'won').length;
        const lostBets = parlays.filter((p) => p.status === 'lost').length;
        const winRate = totalBets > 0 ? ((wonBets / Math.max(wonBets + lostBets, 1)) * 100).toFixed(1) : 0;

        const totalWagered = parlays.reduce((sum, p) => sum + parseFloat(p.stake || 0), 0);
        const totalWon = parlays
            .filter((p) => p.status === 'won')
            .reduce((sum, p) => sum + parseFloat(p.potential_payout || 0), 0);

        setText('profile-total-bets', totalBets);
        setText('profile-won-bets', wonBets);
        setText('profile-lost-bets', lostBets);
        setText('profile-win-rate', `${winRate}%`);
        setText('profile-total-wagered', `$${totalWagered.toFixed(2)}`);
        setText('profile-total-won', `$${totalWon.toFixed(2)}`);
    } catch (error) {
        console.error('Error loading profile stats:', error);
    }
}

async function loadRecentBets() {
    const list = document.getElementById('recent-bets-list');
    if (!list) {
        return;
    }

    list.innerHTML = '<div class="profile-empty">Loading recent bets...</div>';

    try {
        const response = await fetch(`${API_BASE}/parlays.php`);
        const data = await response.json();
        const parlays = data.success ? data.parlays : [];

        if (parlays.length === 0) {
            list.innerHTML = '<div class="profile-empty">No recent bets yet.</div>';
            return;
        }

        const recent = parlays
            .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
            .slice(0, 5);

        list.innerHTML = recent
            .map((parlay) => {
                const created = new Date(parlay.created_at).toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                });
                const status = (parlay.status || 'pending').toLowerCase();

                return `
                    <div class="profile-table-row">
                        <span>Parlay #${parlay.id}</span>
                        <span>${created}</span>
                        <span class="status-pill status-${status}">${status}</span>
                    </div>
                `;
            })
            .join('');
    } catch (error) {
        console.error('Error loading recent bets:', error);
        list.innerHTML = '<div class="profile-empty">Unable to load recent bets.</div>';
    }
}

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) {
        el.textContent = value;
    }
}
