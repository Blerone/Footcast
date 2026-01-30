## Footcast

Footcast is a football betting and stats UI with a PHP backend, admin dashboard, and database-driven content.

Link to github repo: https://github.com/Blerone/Footcast

## Features
- **Home & matches**: Landing page text, match listings, and detailed match views.
- **Lineups view**: Interactive pitch with starting XI, subs, and injuries.
- **Promotions**: Card-based layout for active betting offers (admin-managed).
- **Sports page**: Popular sports and top leagues (admin-managed).
- **Admin dashboard**: Manage users, lineups, promotions, homepage text, and sports content.

## Getting started
- **Database**: Import `db/footcast.sql`.
- **Env**: Copy `.env.example` to `.env` and set `FOOTBALL_API_KEY=your_token_here`.
- **API key**: Get a free token from https://www.football-data.org/ and use it as `FOOTBALL_API_KEY`.
- **Server**: Use XAMPP (Apache + MySQL) or any PHP server that can serve this folder.
- **XAMPP setup**:
  - Place the project in your XAMPP `htdocs` folder.
  - Start Apache and MySQL from the XAMPP control panel.
  - Open `http://localhost/Footcast/` in your browser.
- **Admin access**:
  - Login with an admin user.
  - Admin dashboard: `http://localhost/Footcast/admin-dashboard/index.php`.
  - Manage homepage: `http://localhost/Footcast/admin-dashboard/homepage.php`.
  - Manage sports: `http://localhost/Footcast/admin-dashboard/sports.php`.
  - Manage promotions: `http://localhost/Footcast/admin-dashboard/promotions.php`.
  - Manage lineups: `http://localhost/Footcast/admin-dashboard/lineups.php`.
- **Navigate**: Use the top navigation to move between Home, Matches, Sports, Promotions, Lineups, and Standings.
