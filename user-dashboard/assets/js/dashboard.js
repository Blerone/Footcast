const API_BASE = '..';

document.addEventListener('DOMContentLoaded', () => {
    loadUserInfo();
    loadStatistics();
    if (document.querySelector('.tab-btn')) {
        loadPendingBets();
        setupTabs();
    }

    const settleBtn = document.getElementById('settle-bets-btn');
    if (settleBtn) {
        settleBtn.addEventListener('click', async () => {
            settleBtn.disabled = true;
            const originalText = settleBtn.textContent;
            settleBtn.textContent = 'Settling...';

            const pendingContainer = document.getElementById('pending-bets-list');
            if (pendingContainer) {
                pendingContainer.innerHTML = '<div class="loading">Settling pending bets...</div>';
            }

            try {
                const response = await fetch(`${API_BASE}/settle_bets.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ action: 'settle_all' }),
                });

                if (!response.ok) {
                    console.error('Failed to settle bets', await response.text());
                } else {
                    const data = await response.json().catch(() => null);
                    if (!data || !data.success) {
                        console.error('Settle API error', data);
                    }
                }
            } catch (err) {
                console.error('Error calling settle_bets.php', err);
            }

            
            await loadPendingBets();
            await loadBetHistory();
            await loadParlays();
            await loadStatistics();

            settleBtn.disabled = false;
            settleBtn.textContent = originalText || 'Settle Pending Bets';
        });
    }
});

function setupTabs() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetTab = btn.getAttribute('data-tab');
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));

            btn.classList.add('active');
            document.getElementById(`${targetTab}-tab`).classList.add('active');

            if (targetTab === 'pending') {
                loadPendingBets();
            } else if (targetTab === 'history') {
                loadBetHistory();
            } else if (targetTab === 'parlays') {
                loadParlays();
            }
        });
    });
}

async function loadUserInfo() {
    try {
        const response = await fetch(`${API_BASE}/user.php`);
        const data = await response.json();

        if (data.success && data.user) {
            setText('balance-amount', `$${parseFloat(data.user.balance).toFixed(2)}`);
        }
    } catch (error) {
        console.error('Error loading user info:', error);
    }
}

async function loadStatistics() {
    try {
        const response = await fetch(`${API_BASE}/parlays.php`);
        const parlaysData = await response.json();
        const allParlays = parlaysData.success ? parlaysData.parlays : [];

        const totalBets = allParlays.length;
        const wonBets = allParlays.filter(p => p.status === 'won').length;
        const lostBets = allParlays.filter(p => p.status === 'lost').length;
        const pendingBets = allParlays.filter(p => p.status === 'pending').length;

        const winRate = totalBets > 0 ? ((wonBets / (wonBets + lostBets)) * 100).toFixed(1) : 0;

        const totalWinnings = allParlays
            .filter(p => p.status === 'won')
            .reduce((sum, p) => sum + parseFloat(p.potential_payout || 0), 0);

        setText('total-bets', totalBets);
        setText('won-bets', wonBets);
        setText('lost-bets', lostBets);
        setText('pending-bets', pendingBets);
        setText('win-rate', `${winRate}%`);
        setText('total-winnings', `$${totalWinnings.toFixed(2)}`);
    } catch (error) {
        console.error('Error loading statistics:', error);
    }
}

async function loadPendingBets() {
    const container = document.getElementById('pending-bets-list');
    if (!container) {
        return;
    }
    container.innerHTML = '<div class="loading">Loading pending bets...</div>';

    try {
        const response = await fetch(`${API_BASE}/parlays.php?status=pending`);
        const data = await response.json();

        if (data.success && data.parlays.length > 0) {
            container.innerHTML = data.parlays.map(parlay => createParlayCard(parlay)).join('');
        } else {
            container.innerHTML = '<div class="empty-state">No pending bets</div>';
        }
    } catch (error) {
        container.innerHTML = '<div class="empty-state">Error loading pending bets</div>';
        console.error('Error loading pending bets:', error);
    }
}

async function loadBetHistory() {
    const container = document.getElementById('history-bets-list');
    if (!container) {
        return;
    }
    container.innerHTML = '<div class="loading">Loading bet history...</div>';

    try {
        const response = await fetch(`${API_BASE}/parlays.php`);
        const data = await response.json();

        if (data.success && data.parlays.length > 0) {
            const sortedParlays = data.parlays.sort((a, b) => 
                new Date(b.created_at) - new Date(a.created_at)
            );
            container.innerHTML = sortedParlays.map(parlay => createParlayCard(parlay)).join('');
        } else {
            container.innerHTML = '<div class="empty-state">No bet history</div>';
        }
    } catch (error) {
        container.innerHTML = '<div class="empty-state">Error loading bet history</div>';
        console.error('Error loading bet history:', error);
    }
}

async function loadParlays() {
    const container = document.getElementById('parlays-list');
    if (!container) {
        return;
    }
    container.innerHTML = '<div class="loading">Loading parlays...</div>';

    try {
        const response = await fetch(`${API_BASE}/parlays.php`);
        const data = await response.json();

        if (data.success && data.parlays.length > 0) {
            container.innerHTML = data.parlays.map(parlay => createParlayCard(parlay)).join('');
        } else {
            container.innerHTML = '<div class="empty-state">No parlays found</div>';
        }
    } catch (error) {
        container.innerHTML = '<div class="empty-state">Error loading parlays</div>';
        console.error('Error loading parlays:', error);
    }
}

function createBetCard(bet) {
    const matchDate = new Date(bet.match_date);
    const formattedDate = matchDate.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });

    const betTypeLabel = formatBetType(bet.bet_type, bet.bet_value, bet.bet_category);
    const scoreDisplay = bet.home_score !== null && bet.away_score !== null
        ? `${bet.home_score} - ${bet.away_score}`
        : 'TBD';

    return `
        <div class="bet-card">
            <div class="bet-header">
                <div class="bet-match">
                    <div class="bet-match-teams">${bet.home_team} vs ${bet.away_team}</div>
                    <div class="bet-match-date">${formattedDate}</div>
                    ${bet.match_status === 'finished' ? `<div style="color: #9ca3af; font-size: 0.9rem; margin-top: 5px;">Score: ${scoreDisplay}</div>` : ''}
                </div>
                <div class="bet-status ${bet.status}">${bet.status}</div>
            </div>
            <div class="bet-details">
                <div class="bet-detail-item">
                    <span class="bet-detail-label">Bet Type</span>
                    <span class="bet-detail-value">
                        ${betTypeLabel}
                        <span class="bet-type-badge">${bet.bet_type}</span>
                    </span>
                </div>
                <div class="bet-detail-item">
                    <span class="bet-detail-label">Stake</span>
                    <span class="bet-detail-value">$${parseFloat(bet.amount).toFixed(2)}</span>
                </div>
                <div class="bet-detail-item">
                    <span class="bet-detail-label">Odds</span>
                    <span class="bet-detail-value">${parseFloat(bet.odds).toFixed(2)}</span>
                </div>
                <div class="bet-detail-item">
                    <span class="bet-detail-label">Potential Payout</span>
                    <span class="bet-detail-value">$${parseFloat(bet.potential_payout).toFixed(2)}</span>
                </div>
                ${bet.cashed_out_amount ? `
                <div class="bet-detail-item">
                    <span class="bet-detail-label">Cashed Out</span>
                    <span class="bet-detail-value">$${parseFloat(bet.cashed_out_amount).toFixed(2)}</span>
                </div>
                ` : ''}
            </div>
        </div>
    `;
}

function createParlayCard(parlay) {
    const createdDate = new Date(parlay.created_at);
    const formattedDate = createdDate.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });

    const selectionsHtml = parlay.selections && parlay.selections.length > 0
        ? parlay.selections.map(sel => `
            <div class="parlay-selection">
                <div class="selection-match">
                    <div class="selection-match-teams">${sel.home_team} vs ${sel.away_team}</div>
                    <div class="selection-bet-type">${formatBetType(sel.bet_type)}</div>
                </div>
                <div class="selection-odds">${parseFloat(sel.odds).toFixed(2)}</div>
            </div>
        `).join('')
        : '<div style="color: #9ca3af; padding: 10px;">No selections available</div>';

    return `
        <div class="parlay-card">
            <div class="parlay-header">
                <div class="parlay-info">
                    <h3>Parlay #${parlay.id}</h3>
                    <p>${formattedDate}</p>
                </div>
                <div class="bet-status ${parlay.status}">${parlay.status}</div>
            </div>
            <div class="bet-details">
                <div class="bet-detail-item">
                    <span class="bet-detail-label">Stake</span>
                    <span class="bet-detail-value">$${parseFloat(parlay.stake).toFixed(2)}</span>
                </div>
                <div class="bet-detail-item">
                    <span class="bet-detail-label">Total Odds</span>
                    <span class="bet-detail-value">${parseFloat(parlay.total_odds).toFixed(2)}</span>
                </div>
                <div class="bet-detail-item">
                    <span class="bet-detail-label">Potential Payout</span>
                    <span class="bet-detail-value">$${parseFloat(parlay.potential_payout).toFixed(2)}</span>
                </div>
                ${parlay.cashed_out_amount ? `
                <div class="bet-detail-item">
                    <span class="bet-detail-label">Cashed Out</span>
                    <span class="bet-detail-value">$${parseFloat(parlay.cashed_out_amount).toFixed(2)}</span>
                </div>
                ` : ''}
            </div>
            <div class="parlay-selections">
                <h4 style="color: #9ca3af; font-size: 0.9rem; margin-bottom: 10px;">Selections:</h4>
                ${selectionsHtml}
            </div>
        </div>
    `;
}

function formatBetType(betType, betValue = null, betCategory = null) {
    const typeMap = {
        'home_win': 'Home Win',
        'away_win': 'Away Win',
        'draw': 'Draw',
        'over': 'Over',
        'under': 'Under',
        'both_teams_score': 'Both Teams Score',
        'double_chance': 'Double Chance'
    };

    let label = typeMap[betType] || betType;

    if (betValue) {
        label += ` ${betValue}`;
    }

    if (betCategory) {
        label += ` (${betCategory})`;
    }

    return label;
}

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) {
        el.textContent = value;
    }
}
