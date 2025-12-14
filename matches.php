<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matches</title>
    <link rel="stylesheet" href="./css/matches.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>
<body>
    <header class="header">
        <div class="container">
          <div class="header-inner">
            <div class="left-section">
              <h1 class="logo-text">FOOTCAST</h1>
              <nav class="">
                <button class="nav-link active"><a href="index.php" class="nav-link active">Home</a></button>
                <button class="nav-link"><a href="matches.php">Matches</a></button>
                <button class="nav-link"><a href="">Results</a></button>
                <button class="nav-link"><a href="sports.php">Sports</a></button>
                <button class="nav-link"><a href="promotions.php">Promotions</a></button>
              </nav>
            </div>
            <div class="right-section">
              <div class="search-box">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                  viewBox="0 0 24 24">
                  <path
                    d="M10,18a8,8,0,1,1,5.29-13.71A8,8,0,0,1,10,18Zm9.71,1.29-4.1-4.1A9.94,9.94,0,0,0,20,10a10,10,0,1,0-10,10,9.94,9.94,0,0,0,5.19-1.39l4.1,4.1a1,1,0,0,0,1.42-1.42Z" />
                </svg>
                <input type="text" placeholder="Search matches..." />
              </div>
              <button class="btn outline-btn">
                <svg xmlns="http://www.w3.org/2000/svg" class="user-icon" width="16" height="16" fill="currentColor"
                  viewBox="0 0 24 24">
                  <path d="M12 12c2.67 0 8 1.34 8 4v4H4v-4c0-2.66 5.33-4 8-4zm0-2a4 4 0 1 1 0-8 4 4 0 0 1 0 8z" />
                </svg>
                <a href="login.php" class="login-link">Login</a>
              </button>
              <button class="menu-toggle" id="menu-toggle">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
              </button>
            </div>
          </div>
        </div>
    
        <div class="mobile-nav" id="mobile-nav">
          <button class="nav-link active">Home</button>
          <button class="nav-link">Matches</button>
          <button class="nav-link">Results</button>
          <button class="nav-link">Stats</button>
          <button class="nav-link">Promotions</button>
        </div>
      </header>

    <section class="matches-container">
        <button class="matches-button">Matches</button>
        <button class="live-button">Live</button>
        <button class="matches-button">Results</button>
        <div class="matches-grid">
            <div class="match-box">
                <div class="match-header">
                    <span class="league-name">UEFA Champions League</span>
                    <div class="league-logo">
                        <img src="./assets/footlogos/leagues/champions.webp" alt="" style="width: 50px;">
                    </div>
                </div>

                <div class="match-score">
                    <div class="team">
                        <div class="team-logo">
                            <img src="./assets/footlogos/colorfullogos/chels.png" alt="">
                        </div>
                        <span class="team-name">Chelsea</span>
                    </div>
                    <div class="score">
                        <span class="score-value">0</span>
                        <span class="score-separator">-</span>
                        <span class="score-value">0</span>
                    </div>
                    <div class="team">
                        <div class="team-logo">
                            <img src="./assets/footlogos/colorfullogos/fcbarca.png" alt="">
                        </div>
                        <span class="team-name">Barcelona</span>
                    </div>
                </div>

                <div class="match-info">
                    <button class="more-info-btn">More Info</button>
                    <div class="match-details">
                        <span class="date-icon material-symbols-outlined">calendar_month</span>
                        <span class="match-date">Nov 25, 9:00PM</span>
                        <span class="match-stadium">Stamford Bridge</span>
                        <span class="stadium-icon material-symbols-outlined">stadium</span>
                    </div>
                </div>

                <div class="match-bottom">
                    <div class="jersey jersey-home">
                        <img src="./assets/jersy/chelsea.png" alt="">
                    </div>
                    <div class="bets-container">
                        <div class="bets-buttons">
                            <p>1</p>
                            <button class="draw-btn">2.10</button>
                        </div>
                        <div class="bets-buttons">
                            <p>X</p>
                            <button class="draw-btn">3.10</button>
                        </div>
                        <div class="bets-buttons">
                            <p>2</p>
                            <button class="draw-btn">3.80</button>
                        </div>
                    </div>
                    <div class="jersey jersey-away">
                        <img src="./assets/jersy/barca.png" alt="">
                    </div>
                </div>
            </div>

            <div class="match-box">
                <div class="match-header">
                    <span class="league-name">La Liga</span>
                    <div class="league-logo">
                        <img src="./assets/footlogos/leagues/laliga.png" alt="" style="width: 37px;">
                    </div>
                </div>

                <div class="match-score">
                    <div class="team">
                        <div class="team-logo">
                            <img src="./assets/footlogos/colorfullogos/elche.png" alt="">
                        </div>
                        <span class="team-name">Elche</span>
                    </div>
                    <div class="score">
                        <span class="score-value">0</span>
                        <span class="score-separator">-</span>
                        <span class="score-value">0</span>
                    </div>
                    <div class="team">
                        <div class="team-logo">
                            <img src="./assets/footlogos/colorfullogos/madrid.png" alt="">
                        </div>
                        <span class="team-name">Real Madrid</span>
                    </div>
                </div>

                <div class="match-info">
                    <button class="more-info-btn">More Info</button>
                    <div class="match-details">
                        <span class="date-icon material-symbols-outlined">calendar_month</span>
                        <span class="match-date">Nov 25, 9:00PM</span>
                        <span class="match-stadium">Santiago Bernabeu</span>
                        <span class="stadium-icon material-symbols-outlined">stadium</span>
                    </div>
                </div>

                <div class="match-bottom">
                    <div class="jersey jersey-home">
                        <img src="./assets/jersy/elche.png" alt="">
                    </div>
                    <div class="bets-container">
                        <div class="bets-buttons">
                            <p>1</p>
                            <button class="draw-btn">1.80</button>
                        </div>
                        <div class="bets-buttons">
                            <p>X</p>
                            <button class="draw-btn">2.10</button>
                        </div>
                        <div class="bets-buttons">
                            <p>2</p>
                            <button class="draw-btn">3.40</button>
                        </div>
                    </div>
                    <div class="jersey jersey-away">
                        <img src="./assets/jersy/real.png" alt="">
                    </div>
                </div>
            </div>

            <div class="match-box">
                <div class="match-header">
                    <span class="league-name">Premier League</span>
                    <div class="league-logo">
                        <img src="./assets/footlogos/leagues/premier.png" alt="" style="width: 60px;">
                    </div>
                </div>

                <div class="match-score">
                    <div class="team">
                        <div class="team-logo">
                            <img src="./assets/footlogos/colorfullogos/newcastle.svg" alt="">
                        </div>
                        <span class="team-name">Newcastle</span>
                    </div>
                    <div class="score">
                        <span class="score-value">0</span>
                        <span class="score-separator">-</span>
                        <span class="score-value">0</span>
                    </div>
                    <div class="team">
                        <div class="team-logo">
                            <img src="./assets/footlogos/colorfullogos/mancity.png" alt="">
                        </div>
                        <span class="team-name">Man City</span>
                    </div>
                </div>

                <div class="match-info">
                    <button class="more-info-btn">More Info</button>
                    <div class="match-details">
                        <span class="date-icon material-symbols-outlined">calendar_month</span>
                        <span class="match-date">Nov 25, 9:00PM</span>
                        <span class="match-stadium">Etihad Stadium</span>
                        <span class="stadium-icon material-symbols-outlined">stadium</span>
                    </div>
                </div>

                <div class="match-bottom">
                    <div class="jersey jersey-home">
                        <img src="./assets/jersy/newcastle.png" alt="">
                    </div>
                    <div class="bets-container">
                        <div class="bets-buttons">
                            <p>1</p>
                            <button class="draw-btn">2.40</button>
                        </div>
                        <div class="bets-buttons">
                            <p>X</p>
                            <button class="draw-btn">3.30</button>
                        </div>
                        <div class="bets-buttons">
                            <p>2</p>
                            <button class="draw-btn">3.90</button>
                        </div>
                    </div>
                    <div class="jersey jersey-away">
                        <img src="./assets/jersy/city.png" alt="">
                    </div>
                </div>
            </div>

        </div>
    </section>

    <div id="bettingModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Place Your Bet</h2>
                <span class="modal-close">&times;</span>
            </div>
            <div class="modal-body">
                <div class="bet-match-info">
                    <div class="bet-teams">
                        <div class="bet-team">
                            <img id="modal-home-logo" src="" alt="" class="bet-team-logo">
                            <span id="modal-home-name"></span>
                        </div>
                        <span class="bet-vs">VS</span>
                        <div class="bet-team">
                            <img id="modal-away-logo" src="" alt="" class="bet-team-logo">
                            <span id="modal-away-name"></span>
                        </div>
                    </div>
                </div>
                <div class="betting-categories">
                    <div class="bet-category">
                        <div class="bet-category-header">
                            <span class="category-name">Match Result</span>
                        </div>
                        <div class="bet-category-content">
                            <div class="bet-options">
                                <button class="bet-option-btn" data-selection="Home Win" data-odds="2.10">
                                    <span class="option-label" id="modal-home-name-display"></span>
                                    <span class="option-odds">2.10x</span>
                                </button>
                                <button class="bet-option-btn" data-selection="Draw" data-odds="3.10">
                                    <span class="option-label">Draw</span>
                                    <span class="option-odds">3.10x</span>
                                </button>
                                <button class="bet-option-btn" data-selection="Away Win" data-odds="3.80">
                                    <span class="option-label" id="modal-away-name-display"></span>
                                    <span class="option-odds">3.80x</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bet-category">
                        <div class="bet-category-header">
                            <span class="category-name">1st Half Result</span>
                        </div>
                        <div class="bet-category-content">
                            <div class="bet-options">
                                <button class="bet-option-btn" data-selection="1st Half - Home Win" data-odds="2.50">
                                    <span class="option-label" id="modal-home-name-display-1h"></span>
                                    <span class="option-odds">2.50x</span>
                                </button>
                                <button class="bet-option-btn" data-selection="1st Half - Draw" data-odds="2.80">
                                    <span class="option-label">Draw</span>
                                    <span class="option-odds">2.80x</span>
                                </button>
                                <button class="bet-option-btn" data-selection="1st Half - Away Win" data-odds="3.20">
                                    <span class="option-label" id="modal-away-name-display-1h"></span>
                                    <span class="option-odds">3.20x</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bet-category">
                        <div class="bet-category-header">
                            <span class="category-name">2nd Half Result</span>
                        </div>
                        <div class="bet-category-content">
                            <div class="bet-options">
                                <button class="bet-option-btn" data-selection="2nd Half - Home Win" data-odds="2.95">
                                    <span class="option-label" id="modal-home-name-display-2h"></span>
                                    <span class="option-odds">2.95x</span>
                                </button>
                                <button class="bet-option-btn" data-selection="2nd Half - Draw" data-odds="3.58">
                                    <span class="option-label">Draw</span>
                                    <span class="option-odds">3.58x</span>
                                </button>
                                <button class="bet-option-btn" data-selection="2nd Half - Away Win" data-odds="2.38">
                                    <span class="option-label" id="modal-away-name-display-2h"></span>
                                    <span class="option-odds">2.38x</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bet-category">
                        <div class="bet-category-header">
                            <span class="category-name">Corners</span>
                        </div>
                        <div class="bet-category-content">
                            <div class="bet-options-grid">
                                <div class="bet-section">
                                    <div class="bet-section-label">FULL MATCH</div>
                                    <div class="bet-options">
                                        <button class="bet-option-btn" data-selection="Over 9.5 Corners" data-odds="1.96">
                                            <span class="option-label">Over 9.5</span>
                                            <span class="option-odds">1.96x</span>
                                        </button>
                                        <button class="bet-option-btn" data-selection="Under 9.5 Corners" data-odds="2.20">
                                            <span class="option-label">Under 9.5</span>
                                            <span class="option-odds">2.20x</span>
                                        </button>
                                        <button class="bet-option-btn" data-selection="Over 10.5 Corners" data-odds="1.80">
                                            <span class="option-label">Over 10.5</span>
                                            <span class="option-odds">1.80x</span>
                                        </button>
                                        <button class="bet-option-btn" data-selection="Under 10.5 Corners" data-odds="1.90">
                                            <span class="option-label">Under 10.5</span>
                                            <span class="option-odds">1.90x</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bet-category">
                        <div class="bet-category-header">
                            <span class="category-name">Yellow Cards</span>
                        </div>
                        <div class="bet-category-content">
                            <div class="bet-options-grid">
                                <div class="bet-section">
                                    <div class="bet-section-label">FULL MATCH</div>
                                    <div class="bet-options">
                                        <button class="bet-option-btn" data-selection="Over 3.5 Yellow Cards" data-odds="1.96">
                                            <span class="option-label">Over 3.5</span>
                                            <span class="option-odds">1.96x</span>
                                        </button>
                                        <button class="bet-option-btn" data-selection="Under 3.5 Yellow Cards" data-odds="2.20">
                                            <span class="option-label">Under 3.5</span>
                                            <span class="option-odds">2.20x</span>
                                        </button>
                                        <button class="bet-option-btn" data-selection="Over 4.5 Yellow Cards" data-odds="1.80">
                                            <span class="option-label">Over 4.5</span>
                                            <span class="option-odds">1.80x</span>
                                        </button>
                                        <button class="bet-option-btn" data-selection="Under 4.5 Yellow Cards" data-odds="1.90">
                                            <span class="option-label">Under 4.5</span>
                                            <span class="option-odds">1.90x</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="bet-section">
                                    <div class="bet-section-label">1ST HALF</div>
                                    <div class="bet-options">
                                        <button class="bet-option-btn" data-selection="1H Over 1.5 Yellow Cards" data-odds="2.18">
                                            <span class="option-label">Over 1.5</span>
                                            <span class="option-odds">2.18x</span>
                                        </button>
                                        <button class="bet-option-btn" data-selection="1H Under 1.5 Yellow Cards" data-odds="1.99">
                                            <span class="option-label">Under 1.5</span>
                                            <span class="option-odds">1.99x</span>
                                        </button>
                                        <button class="bet-option-btn" data-selection="1H Over 2.5 Yellow Cards" data-odds="2.24">
                                            <span class="option-label">Over 2.5</span>
                                            <span class="option-odds">2.24x</span>
                                        </button>
                                        <button class="bet-option-btn" data-selection="1H Under 2.5 Yellow Cards" data-odds="1.97">
                                            <span class="option-label">Under 2.5</span>
                                            <span class="option-odds">1.97x</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bet-category">
                        <div class="bet-category-header">
                            <span class="category-name">Red Cards</span>
                        </div>
                        <div class="bet-category-content">
                            <div class="bet-options">
                                <button class="bet-option-btn" data-selection="Over 0.5 Red Cards" data-odds="3.50">
                                    <span class="option-label">Over 0.5</span>
                                    <span class="option-odds">3.50x</span>
                                </button>
                                <button class="bet-option-btn" data-selection="Under 0.5 Red Cards" data-odds="1.30">
                                    <span class="option-label">Under 0.5</span>
                                    <span class="option-odds">1.30x</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bet-category">
                        <div class="bet-category-header">
                            <span class="category-name">Cards (Total)</span>
                        </div>
                        <div class="bet-category-content">
                            <div class="bet-options">
                                <button class="bet-option-btn" data-selection="Over 4.5 Total Cards" data-odds="1.85">
                                    <span class="option-label">Over 4.5</span>
                                    <span class="option-odds">1.85x</span>
                                </button>
                                <button class="bet-option-btn" data-selection="Under 4.5 Total Cards" data-odds="1.95">
                                    <span class="option-label">Under 4.5</span>
                                    <span class="option-odds">1.95x</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bet-category">
                        <div class="bet-category-header">
                            <span class="category-name">Shots on Target</span>
                        </div>
                        <div class="bet-category-content">
                            <div class="bet-options">
                                <button class="bet-option-btn" data-selection="Over 8.5 Shots on Target" data-odds="1.90">
                                    <span class="option-label">Over 8.5</span>
                                    <span class="option-odds">1.90x</span>
                                </button>
                                <button class="bet-option-btn" data-selection="Under 8.5 Shots on Target" data-odds="1.90">
                                    <span class="option-label">Under 8.5</span>
                                    <span class="option-odds">1.90x</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bet-category">
                        <div class="bet-category-header">
                            <span class="category-name">Offsides</span>
                        </div>
                        <div class="bet-category-content">
                            <div class="bet-options">
                                <button class="bet-option-btn" data-selection="Over 3.5 Offsides" data-odds="2.10">
                                    <span class="option-label">Over 3.5</span>
                                    <span class="option-odds">2.10x</span>
                                </button>
                                <button class="bet-option-btn" data-selection="Under 3.5 Offsides" data-odds="1.75">
                                    <span class="option-label">Under 3.5</span>
                                    <span class="option-odds">1.75x</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bet-category">
                        <div class="bet-category-header">
                            <span class="category-name">Fouls</span>
                        </div>
                        <div class="bet-category-content">
                            <div class="bet-options">
                                <button class="bet-option-btn" data-selection="Over 20.5 Fouls" data-odds="1.95">
                                    <span class="option-label">Over 20.5</span>
                                    <span class="option-odds">1.95x</span>
                                </button>
                                <button class="bet-option-btn" data-selection="Under 20.5 Fouls" data-odds="1.85">
                                    <span class="option-label">Under 20.5</span>
                                    <span class="option-odds">1.85x</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bet-category">
                        <div class="bet-category-header">
                            <span class="category-name">Posts and Crossbar</span>
                        </div>
                        <div class="bet-category-content">
                            <div class="bet-options">
                                <button class="bet-option-btn" data-selection="Over 0.5 Posts/Crossbar" data-odds="2.50">
                                    <span class="option-label">Over 0.5</span>
                                    <span class="option-odds">2.50x</span>
                                </button>
                                <button class="bet-option-btn" data-selection="Under 0.5 Posts/Crossbar" data-odds="1.50">
                                    <span class="option-label">Under 0.5</span>
                                    <span class="option-odds">1.50x</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bet-category">
                        <div class="bet-category-header">
                            <span class="category-name">Throw-ins</span>
                        </div>
                        <div class="bet-category-content">
                            <div class="bet-options">
                                <button class="bet-option-btn" data-selection="Over 45.5 Throw-ins" data-odds="1.90">
                                    <span class="option-label">Over 45.5</span>
                                    <span class="option-odds">1.90x</span>
                                </button>
                                <button class="bet-option-btn" data-selection="Under 45.5 Throw-ins" data-odds="1.90">
                                    <span class="option-label">Under 45.5</span>
                                    <span class="option-odds">1.90x</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bet-category">
                        <div class="bet-category-header">
                            <span class="category-name">Shots towards Goal</span>
                        </div>
                        <div class="bet-category-content">
                            <div class="bet-options">
                                <button class="bet-option-btn" data-selection="Over 18.5 Shots" data-odds="1.95">
                                    <span class="option-label">Over 18.5</span>
                                    <span class="option-odds">1.95x</span>
                                </button>
                                <button class="bet-option-btn" data-selection="Under 18.5 Shots" data-odds="1.85">
                                    <span class="option-label">Under 18.5</span>
                                    <span class="option-odds">1.85x</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="selected-bet-info" id="selectedBetInfo" style="display: none;">
                    <div class="bet-selection-display">
                        <p>Your Selection: <span id="modal-selection"></span></p>
                        <p>Odds: <span id="modal-odds"></span></p>
                    </div>
                </div>

                <div class="bet-amount">
                    <label for="betAmount">Bet Amount ($):</label>
                    <input type="number" id="betAmount" placeholder="Enter amount" min="1" step="0.01">
                </div>
                <div class="bet-payout">
                    <p>Potential Payout: <span id="potentialPayout">0.00</span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="modal-btn confirm-btn">Place Bet</button>
            </div>
        </div>
    </div>
    <section class="footer">
        <div class="left-footer">
          <h2>FOOTCAST</h2>
          <p>One click away, from winning it all.</p>
    
          <div class="social-links">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-youtube"></i></a>
            <a href="#"><i class="fab fa-x-twitter"></i></a>
          </div>
    
          <p class="footer-copy">© 2025 Blerona Thaci &amp; Vesa Susuri | All Rights Reserved</p>
        </div>
    
        <div class="right-footer">
          <h2>Company</h2>
          <ul>
            <li><a href="#">About Us</a></li>
            <li><a href="#">Our Team</a></li>
            <li><a href="#">Our Work</a></li>
            <li><a href="#">Partners</a></li>
            <li><a href="#">Clients</a></li>
          </ul>
        </div>
    
        <div class="right-footer">
          <h2>Support</h2>
          <ul>
            <li><a href="#">Contact Us</a></li>
            <li><a href="#">Blog</a></li>
            <li><a href="#">Q &amp; A</a></li>
            <li><a href="#">Affiliates</a></li>
          </ul>
        </div>
    
        <div class="right-footer">
          <h2>Trust</h2>
          <ul>
            <li><a href="#">User Trust</a></li>
            <li><a href="#">Guidelines</a></li>
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="#">Terms of Use</a></li>
            <li><a href="#">Security</a></li>
          </ul>
        </div>
      </section>

    
    <script>
        const toggleBtn = document.getElementById('menu-toggle');
        const mobileNav = document.getElementById('mobile-nav');
        const header = document.querySelector('.header');

        toggleBtn.addEventListener('click', () => {
            mobileNav.classList.toggle('open');
            toggleBtn.classList.toggle('open');
        });

        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        const modal = document.getElementById('bettingModal');
        const closeModal = document.querySelector('.modal-close');

        const bettingButtons = document.querySelectorAll('.bets-buttons');

        bettingButtons.forEach(button => {
            button.addEventListener('click', function() {
                const matchBox = this.closest('.match-box');
                const teams = matchBox.querySelectorAll('.team-name');
                const teamLogos = matchBox.querySelectorAll('.team-logo img');

                const homeName = teams[0].textContent;
                const awayName = teams[1].textContent;
                
                document.getElementById('modal-home-logo').src = teamLogos[0].src;
                document.getElementById('modal-away-logo').src = teamLogos[1].src;
                document.getElementById('modal-home-name').textContent = homeName;
                document.getElementById('modal-away-name').textContent = awayName;
                
                document.getElementById('modal-home-name-display').textContent = homeName;
                document.getElementById('modal-away-name-display').textContent = awayName;
                document.getElementById('modal-home-name-display-1h').textContent = homeName;
                document.getElementById('modal-away-name-display-1h').textContent = awayName;
                document.getElementById('modal-home-name-display-2h').textContent = homeName;
                document.getElementById('modal-away-name-display-2h').textContent = awayName;

                modal.style.display = 'block';
            });
        });

        function closeModalFunc() {
            modal.style.display = 'none';
        }

        closeModal.addEventListener('click', closeModalFunc);

        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeModalFunc();
            }
        });
    </script>
</body>

</html>