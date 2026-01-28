const API_BASE = '..';

document.addEventListener('DOMContentLoaded', () => {
    loadMyBets();

    const settleBtn = document.getElementById('settle-bets-btn');
    if (settleBtn) {
        settleBtn.addEventListener('click', async () => {
            await handleSettleClick(settleBtn);
        });
    }
});

async function loadMyBets() {
    const container = document.getElementById('bets-list');
    if (!container) return;

    container.innerHTML = '<div class="loading">Loading your bets...</div>';

    try {
        const response = await fetch(`${API_BASE}/parlays.php`);
        const data = await response.json();

        if (!data.success || !Array.isArray(data.parlays) || data.parlays.length === 0) {
            container.innerHTML = '<div class="empty-state">You have not placed any bets yet.</div>';
            return;
        }

        const betsHtml = data.parlays.map((parlay) => createParlayBetCard(parlay)).join('');
        container.innerHTML = betsHtml;
    } catch (error) {
        console.error('Error loading bets:', error);
        container.innerHTML = '<div class="empty-state">Unable to load your bets. Please try again.</div>';
    }
}

async function handleSettleClick(button) {
    button.disabled = true;
    const originalText = button.textContent;
    button.textContent = 'Settling...';

    try {
        const response = await fetch(`${API_BASE}/settle_bets.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ action: 'settle_all' }),
        });

        let alertMessage = '';

        if (!response.ok) {
            const raw = await response.text();
            console.error('Settle bets HTTP error:', raw);
            alertMessage = 'Failed to settle bets. Please try again in a moment.';
        } else {
            const data = await response.json().catch(() => null);
            if (!data || !data.success) {
                console.error('Settle API error:', data);
                alertMessage = (data && data.message) ? data.message : 'Failed to settle bets.';
            } else {
                const stats = data.stats || {};
                const matchesSynced = stats.matches_synced ?? 0;
                const betsProcessed = stats.bets_processed ?? 0;
                const betsWon = stats.bets_won ?? 0;
                const betsLost = stats.bets_lost ?? 0;
                const parlaysSettled = stats.parlays_settled ?? 0;

                alertMessage =
                    `Settlement complete:\n` +
                    `- Matches synced: ${matchesSynced}\n` +
                    `- Bets processed: ${betsProcessed}\n` +
                    `  • Won: ${betsWon}\n` +
                    `  • Lost: ${betsLost}\n` +
                    `- Parlays settled: ${parlaysSettled}`;
            }
        }

        await loadMyBets();

        if (alertMessage) {
            alert(alertMessage);
        }
    } catch (error) {
        console.error('Error calling settle_bets.php:', error);
        alert('Unexpected error while settling bets. Please try again.');
    } finally {
        button.disabled = false;
        button.textContent = originalText || 'Settle Pending Bets';
    }
}

function createParlayBetCard(parlay) {
    const createdDate = new Date(parlay.created_at);
    const formattedDate = createdDate.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });

    const status = (parlay.status || 'pending').toLowerCase();

    const selectionsHtml =
        parlay.selections && parlay.selections.length > 0
            ? parlay.selections
                  .map(
                      (sel) => `
                <div class="parlay-selection">
                    <div class="selection-match">
                        <div class="selection-match-teams">
                            ${sel.home_team} vs ${sel.away_team}
                        </div>
                        <div class="selection-bet-type">
                            ${formatBetType(sel.bet_type)}
                        </div>
                    </div>
                    <div class="selection-meta">
                        <span class="selection-odds">${parseFloat(sel.odds).toFixed(2)}</span>
                        <span class="selection-status ${sel.status || 'pending'}">
                            ${sel.status || 'pending'}
                        </span>
                    </div>
                </div>
            `
                  )
                  .join('')
            : '<div style="color: #9ca3af; padding: 10px;">No selections available</div>';

    return `
        <div class="parlay-card">
            <div class="parlay-header">
                <div class="parlay-info">
                    <h3>Parlay #${parlay.id}</h3>
                    <p>${formattedDate}</p>
                </div>
                <div class="bet-status ${status}">
                    ${status}
                </div>
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
                ${
                    parlay.cashed_out_amount
                        ? `
                <div class="bet-detail-item">
                    <span class="bet-detail-label">Cashed Out</span>
                    <span class="bet-detail-value">$${parseFloat(
                        parlay.cashed_out_amount
                    ).toFixed(2)}</span>
                </div>
                `
                        : ''
                }
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
        home_win: 'Home Win',
        away_win: 'Away Win',
        draw: 'Draw',
        over: 'Over',
        under: 'Under',
        both_teams_score: 'Both Teams Score',
        double_chance: 'Double Chance',
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

