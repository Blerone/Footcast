const betSlipModal = document.getElementById('betSlipModal');
const betSlipSelections = document.getElementById('betSlipSelections');
const betSlipEmpty = document.getElementById('betSlipEmpty');
const combinedOddsEl = document.getElementById('combinedOdds');
const estimatedPayoutEl = document.getElementById('estimatedPayout');
const stakeInput = document.getElementById('betStake');
const betSlipError = document.getElementById('betSlipError');
const placeBetBtn = document.getElementById('placeBetBtn');
const clearBetSlipBtn = document.getElementById('clearBetSlip');
const closeModalBtn = document.querySelector('[data-modal-close]');
const betSlipToast = document.getElementById('betSlipToast');
const modalHomeLogo = document.getElementById('modal-home-logo');
const modalAwayLogo = document.getElementById('modal-away-logo');
const modalHomeName = document.getElementById('modal-home-name');
const modalAwayName = document.getElementById('modal-away-name');
const modalHomeNameDisplay = document.getElementById('modal-home-name-display');
const modalAwayNameDisplay = document.getElementById('modal-away-name-display');
const modalHomeNameDisplay1h = document.getElementById('modal-home-name-display-1h');
const modalAwayNameDisplay1h = document.getElementById('modal-away-name-display-1h');
const modalHomeNameDisplay2h = document.getElementById('modal-home-name-display-2h');
const modalAwayNameDisplay2h = document.getElementById('modal-away-name-display-2h');

const BET_SLIP_KEY = 'footcastBetSlip';
const LOGIN_KEY = 'footcastLoggedIn';
const RETURN_URL_KEY = 'footcastReturnUrl';
const BASE_PATH = (() => {
  const parts = window.location.pathname.split('/').filter(Boolean);
  if (parts.length && parts[0].toLowerCase() === 'footcast') {
    return '/Footcast';
  }
  return '';
})();
const withBasePath = (path) => (BASE_PATH ? `${BASE_PATH}/${path}` : path);

const MIN_STAKE = 1;
const MAX_STAKE = 10000;

let selections = [];
let stakeValue = '';
let currentMatch = null;

const formatOdds = (value) => (typeof value === 'number' ? value.toFixed(2) : '—');
const formatCurrency = (value) => (typeof value === 'number' ? value.toFixed(2) : '—');

const loadBetSlip = () => {
  try {
    const stored = JSON.parse(localStorage.getItem(BET_SLIP_KEY));
    if (stored && Array.isArray(stored.selections)) {
      selections = stored.selections;
      stakeValue = stored.stakeValue || '';
    }
  } catch (error) {
    selections = [];
    stakeValue = '';
  }
};

const saveBetSlip = () => {
  localStorage.setItem(
    BET_SLIP_KEY,
    JSON.stringify({
      selections,
      stakeValue,
    })
  );
};

const getCombinedOdds = () => {
  if (!selections.length) {
    return null;
  }
  return selections.reduce((product, item) => product * item.odds, 1);
};

const setError = (message, isError = true) => {
  if (!betSlipError) {
    return;
  }
  if (!message) {
    betSlipError.textContent = '';
    betSlipError.classList.remove('is-error', 'is-success');
    return;
  }
  betSlipError.textContent = message;
  betSlipError.classList.toggle('is-error', isError);
  betSlipError.classList.toggle('is-success', !isError);
};

const showToast = (message) => {
  if (!betSlipToast) {
    return;
  }
  betSlipToast.textContent = message;
  betSlipToast.classList.add('show');
  window.clearTimeout(showToast.timeoutId);
  showToast.timeoutId = window.setTimeout(() => {
    betSlipToast.classList.remove('show');
  }, 2500);
};

const validateStake = () => {
  if (!stakeInput) {
    return { valid: false, value: null };
  }
  const raw = stakeInput.value.trim();
  if (!raw) {
    return { valid: false, value: null };
  }
  const value = Number(raw);
  if (Number.isNaN(value)) {
    return { valid: false, value: null };
  }
  if (value < MIN_STAKE || value > MAX_STAKE) {
    return { valid: false, value };
  }
  return { valid: true, value };
};

const updateSummary = () => {
  const combined = getCombinedOdds();
  combinedOddsEl.textContent = combined ? formatOdds(combined) : '—';

  const stakeCheck = validateStake();
  const payout = stakeCheck.valid && combined ? stakeCheck.value * combined : null;
  estimatedPayoutEl.textContent = payout ? formatCurrency(payout) : '—';

  const hasSelections = selections.length > 0;
  const canPlace = hasSelections && stakeCheck.valid;
  placeBetBtn.disabled = !canPlace;

  if (!hasSelections) {
    setError('Add at least one selection to place a bet.');
  } else if (!stakeCheck.valid && stakeInput.value.trim()) {
    setError(`Stake must be between ${MIN_STAKE} and ${MAX_STAKE}.`);
  } else {
    setError('');
  }
};

const renderSelections = () => {
  betSlipSelections.innerHTML = '';
  if (!selections.length) {
    betSlipEmpty.style.display = 'block';
  } else {
    betSlipEmpty.style.display = 'none';
  }

  selections.forEach((selection) => {
    const item = document.createElement('div');
    item.className = 'bet-slip-item';
    item.dataset.selectionId = selection.id;

    const summary = document.createElement('div');
    summary.className = 'bet-slip-summary';
    summary.textContent = selection.summary;

    const odds = document.createElement('div');
    odds.className = 'bet-slip-odds';
    odds.textContent = formatOdds(selection.odds);

    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'bet-slip-remove';
    removeBtn.textContent = 'Remove';
    removeBtn.addEventListener('click', () => {
      selections = selections.filter((item) => item.id !== selection.id);
      saveBetSlip();
      renderSelections();
      updateSummary();
    });

    const actions = document.createElement('div');
    actions.className = 'bet-slip-actions';
    actions.appendChild(odds);
    actions.appendChild(removeBtn);

    item.appendChild(summary);
    item.appendChild(actions);
    betSlipSelections.appendChild(item);
  });
};

const openModal = () => {
  if (!betSlipModal) {
    return;
  }
  betSlipModal.style.display = 'block';
  document.body.classList.add('modal-open');
};

const closeModal = () => {
  if (!betSlipModal) {
    return;
  }
  betSlipModal.style.display = 'none';
  document.body.classList.remove('modal-open');
};

const buildSummary = (data) => {
  const parts = [`${data.home} vs ${data.away}`];
  if (data.league) {
    parts.push(data.league);
  }
  if (data.start) {
    parts.push(data.start);
  }
  parts.push(data.market);
  parts.push(data.outcome);
  return parts.join(' • ');
};

const updateModalMatchInfo = () => {
  if (!currentMatch) {
    return;
  }
  if (modalHomeLogo) {
    modalHomeLogo.src = currentMatch.homeLogo || '';
  }
  if (modalAwayLogo) {
    modalAwayLogo.src = currentMatch.awayLogo || '';
  }
  if (modalHomeName) {
    modalHomeName.textContent = currentMatch.home || '';
  }
  if (modalAwayName) {
    modalAwayName.textContent = currentMatch.away || '';
  }
  if (modalHomeNameDisplay) {
    modalHomeNameDisplay.textContent = currentMatch.home || '';
  }
  if (modalAwayNameDisplay) {
    modalAwayNameDisplay.textContent = currentMatch.away || '';
  }
  if (modalHomeNameDisplay1h) {
    modalHomeNameDisplay1h.textContent = currentMatch.home || '';
  }
  if (modalAwayNameDisplay1h) {
    modalAwayNameDisplay1h.textContent = currentMatch.away || '';
  }
  if (modalHomeNameDisplay2h) {
    modalHomeNameDisplay2h.textContent = currentMatch.home || '';
  }
  if (modalAwayNameDisplay2h) {
    modalAwayNameDisplay2h.textContent = currentMatch.away || '';
  }
};

const updateModalOdds = () => {
  if (!currentMatch || !currentMatch.odds) {
    return;
  }
  
  const odds = currentMatch.odds;
  const betButtons = document.querySelectorAll('.bet-option-btn[data-bet-type]');
  
  betButtons.forEach(button => {
    const betType = button.dataset.betType;
    if (betType && odds[betType]) {
      const oddsValue = odds[betType];
      button.dataset.odds = oddsValue;
      const oddsDisplay = button.querySelector('.option-odds');
      if (oddsDisplay) {
        oddsDisplay.textContent = Number(oddsValue).toFixed(2) + 'x';
      }
    }
  });
};

const setCurrentMatchFromBox = (matchBox) => {
  if (!matchBox) {
    return;
  }
  const teamLogos = matchBox.querySelectorAll('.team-logo img');
  
  // Parse odds from data attribute
  let odds = {};
  try {
    const oddsData = matchBox.dataset.odds;
    if (oddsData) {
      odds = JSON.parse(oddsData);
    }
  } catch (e) {
    console.warn('Failed to parse odds data', e);
  }
  
  currentMatch = {
    matchId: matchBox.dataset.apiFixtureId || matchBox.dataset.matchId || '',
    apiFixtureId: matchBox.dataset.apiFixtureId || '',
    league: matchBox.dataset.league || '',
    home: matchBox.dataset.home || 'Home',
    away: matchBox.dataset.away || 'Away',
    start: matchBox.dataset.start || '',
    matchDate: matchBox.dataset.matchDate || '',
    homeLogo: matchBox.dataset.homeLogo || (teamLogos[0] ? teamLogos[0].src : ''),
    awayLogo: matchBox.dataset.awayLogo || (teamLogos[1] ? teamLogos[1].src : ''),
    odds: odds,
  };
  updateModalMatchInfo();
  updateModalOdds();
};

const addSelection = (selection) => {
  const exists = selections.some((item) => item.id === selection.id);
  if (exists) {
    setError('Selection already added.');
    return;
  }
  selections.push(selection);
  saveBetSlip();
  renderSelections();
  updateSummary();
};

const handlePickClick = (event) => {
  const pickNode = event.target.closest('.bet-pick, .bets-buttons');
  if (!pickNode) {
    return;
  }
  const button = pickNode.classList.contains('bet-pick')
    ? pickNode
    : pickNode.querySelector('.bet-pick');
  if (!button) {
    return;
  }
  const matchBox = button.closest('.match-box');
  if (!matchBox) {
    return;
  }

  setCurrentMatchFromBox(matchBox);
  openModal();
  const odds = Number(button.dataset.odds);
  if (Number.isNaN(odds) || odds <= 0) {
    return;
  }

  const matchId = matchBox.dataset.apiFixtureId || matchBox.dataset.matchId || '';
  const betType = button.dataset.betType || 'home_win';
  const market = button.dataset.market || 'Match Result';
  const outcome = button.dataset.outcome || 'Outcome';

  const selectionData = {
    id: `${matchId}-${betType}`,
    matchId,
    match_id: matchId,
    apiFixtureId: matchBox.dataset.apiFixtureId || '',
    matchDate: matchBox.dataset.matchDate || '',
    bet_type: betType,
    league: matchBox.dataset.league || '',
    home: matchBox.dataset.home || 'Home',
    away: matchBox.dataset.away || 'Away',
    start: matchBox.dataset.start || '',
    market,
    outcome,
    odds,
  };

  selectionData.summary = buildSummary(selectionData);
  addSelection(selectionData);
};

document.addEventListener('click', handlePickClick);

document.addEventListener('click', (event) => {
  const infoButton = event.target.closest('.more-info-btn');
  if (!infoButton) {
    return;
  }
  const matchBox = infoButton.closest('.match-box');
  if (!matchBox) {
    return;
  }
  setCurrentMatchFromBox(matchBox);
  openModal();
});

document.addEventListener('click', (event) => {
  const button = event.target.closest('.bet-option-btn');
  if (!button) {
    return;
  }
  if (!currentMatch) {
    setError('Select a match first.');
    openModal();
    return;
  }

  const betType = button.dataset.betType;
  if (!betType) {
    return;
  }
  const odds = Number(button.dataset.odds);
  if (Number.isNaN(odds) || odds <= 0) {
    const matchOdds = currentMatch.odds || {};
    const matchOddsValue = matchOdds[betType];
    if (!matchOddsValue || Number.isNaN(Number(matchOddsValue))) {
      setError('Odds not available for this bet type.');
      return;
    }
    button.dataset.odds = matchOddsValue;
    button.querySelector('.option-odds').textContent = Number(matchOddsValue).toFixed(2) + 'x';
  }

  const finalOdds = Number(button.dataset.odds);
  if (Number.isNaN(finalOdds) || finalOdds <= 0) {
    return;
  }

  const market =
    button.closest('.bet-category')?.querySelector('.category-name')?.textContent?.trim() || 'Market';
  const outcome =
    button.querySelector('.option-label')?.textContent?.trim() || button.textContent.trim();

  const selectionData = {
    id: `${currentMatch.matchId}-${betType}`,
    matchId: currentMatch.matchId,
    match_id: currentMatch.matchId,
    apiFixtureId: currentMatch.apiFixtureId || '',
    matchDate: currentMatch.matchDate || '',
    bet_type: betType,
    league: currentMatch.league,
    home: currentMatch.home,
    away: currentMatch.away,
    start: currentMatch.start,
    market,
    outcome,
    odds: finalOdds,
  };

  selectionData.summary = buildSummary(selectionData);
  addSelection(selectionData);
  openModal();
});

if (closeModalBtn) {
  closeModalBtn.addEventListener('click', closeModal);
}

if (betSlipModal) {
  betSlipModal.addEventListener('click', (event) => {
    if (event.target === betSlipModal) {
      closeModal();
    }
  });
}

if (stakeInput) {
  stakeInput.addEventListener('input', () => {
    stakeValue = stakeInput.value;
    saveBetSlip();
    updateSummary();
  });
}

if (clearBetSlipBtn) {
  clearBetSlipBtn.addEventListener('click', () => {
    selections = [];
    stakeValue = '';
    if (stakeInput) {
      stakeInput.value = '';
    }
    saveBetSlip();
    renderSelections();
    updateSummary();
  });
}

const sectionToggles = document.querySelectorAll('.section-toggle');
sectionToggles.forEach((toggle) => {
  toggle.addEventListener('click', () => {
    const targetId = toggle.dataset.target;
    const section = document.getElementById(targetId);
    if (!section) {
      return;
    }
    const isCollapsed = section.classList.toggle('collapsed');
    toggle.setAttribute('aria-expanded', String(!isCollapsed));
  });
});

if (placeBetBtn) {
  placeBetBtn.addEventListener('click', async () => {
    const stakeCheck = validateStake();
    if (!selections.length) {
      setError('Add at least one selection to place a bet.');
      return;
    }
    if (!stakeCheck.valid) {
      setError(`Stake must be between ${MIN_STAKE} and ${MAX_STAKE}.`);
      return;
    }

    const isLoggedIn =
      (document.body && document.body.dataset.loggedIn === '1') ||
      localStorage.getItem(LOGIN_KEY) === '1';
    if (!isLoggedIn) {
      saveBetSlip();
      localStorage.setItem(RETURN_URL_KEY, window.location.pathname + window.location.search);
      window.location.href = withBasePath('login.php');
      return;
    }

    placeBetBtn.disabled = true;
    setError('Placing bet...', false);

    const combinedOdds = getCombinedOdds();
    
    const apiEndpoint = 'bets.php';
    const payload = {
      selections: selections.map((sel) => ({
        matchId: sel.matchId || sel.match_id || '',
        apiFixtureId: sel.apiFixtureId || '',
        matchDate: sel.matchDate || '',
        home: sel.home || '',
        away: sel.away || '',
        market: sel.market || '',
        outcome: sel.outcome || '',
        league: sel.league || '',
        odds: sel.odds,
      })),
      combinedOdds,
      stake: stakeCheck.value,
    };

    try {
      const response = await fetch(withBasePath(apiEndpoint), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });
      
      const rawText = await response.text();
      let data = null;
      try {
        data = rawText ? JSON.parse(rawText) : null;
      } catch (parseError) {
        console.error('Failed to parse response:', rawText);
        throw new Error('Server returned invalid response. Please try again.');
      }
      
      if (!response.ok) {
        const errorMsg = data.message || data.error || `HTTP ${response.status}: Bet placement failed.`;
        console.error('Bet placement error:', errorMsg, data);
        throw new Error(errorMsg);
      }
      
      if (!data.success) {
        const errorMsg = data.message || data.error || 'Bet placement failed.';
        console.error('Bet placement failed:', errorMsg, data);
        throw new Error(errorMsg);
      }
      
      setError('Bet placed successfully!', false);
      showToast('Bet placed successfully!');
      
      if (data.bet && data.bet.new_balance !== undefined) {
        console.log('New balance:', data.bet.new_balance);
      }
      
      selections = [];
      stakeValue = '';
      if (stakeInput) {
        stakeInput.value = '';
      }
      saveBetSlip();
      renderSelections();
      updateSummary();
    } catch (error) {
      console.error('Bet placement error:', error);
      const errorMsg = error.message || 'Bet placement failed. Please check your connection and try again.';
      setError(errorMsg);
      showToast(errorMsg);
    } finally {
      placeBetBtn.disabled = false;
    }
  });
}

loadBetSlip();
if (stakeInput && stakeValue) {
  stakeInput.value = stakeValue;
}
renderSelections();
updateSummary();
