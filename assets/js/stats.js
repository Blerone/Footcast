const leagueSelect = document.getElementById('league-select');
const searchInput = document.getElementById('team-search');
const teamCards = document.querySelectorAll('.team-card');

if (leagueSelect) {
  leagueSelect.addEventListener('change', () => {
    const league = leagueSelect.value;
    const url = new URL(window.location.href);
    url.searchParams.set('league', league);
    window.location.href = url.toString();
  });
}

if (searchInput) {
  searchInput.addEventListener('input', () => {
    const term = searchInput.value.trim().toLowerCase();
    teamCards.forEach((card) => {
      const name = card.dataset.teamName || '';
      card.style.display = name.includes(term) ? 'flex' : 'none';
    });
  });
}
