# Footcast

Footcast is a football betting and stats application with a PHP backend, admin dashboard, and database-driven content.

**Repository:** [https://github.com/Blerone/Footcast](https://github.com/Blerone/Footcast)

---

## Features

- **Home & matches** — Landing page, match listings, and detailed match views
- **Lineups** — Interactive pitch with starting XI, substitutes, and injuries
- **Promotions** — Card-based layout for betting offers (admin-managed)
- **Sports** — Popular sports and top leagues (admin-managed)
- **Admin dashboard** — Manage users, lineups, promotions, homepage content, sports, and contact messages

---

## API key requirement

Several features (match data, lineups, live scores, standings, and bet settlement) depend on **external football data**. This project uses the [Football-Data.org](https://www.football-data.org/) API.

**Without a valid API key:**

- Match listings, fixture details, and live scores may not load or may return errors
- Lineup and standings data may be missing or outdated
- Automated bet settlement that relies on match results may not function correctly

**To get full functionality:**

1. Sign up at **[https://www.football-data.org/](https://www.football-data.org/)** and create an account.
2. Obtain your free API token from the dashboard.
3. Add it to your environment (see [Getting started](#getting-started) below).

The application is designed to run with or without a key: core pages and the admin dashboard work regardless; only API-dependent features are affected when the key is missing or invalid.

---

## Getting started

### 1. Database

Import the provided schema (e.g. `db/footcast.sql` or equivalent) into your MySQL/MariaDB database.

### 2. Environment and API key

- Copy `.env.example` to `.env` (if present).
- Set your Football-Data.org API token:

  ```env
  FOOTBALL_API_KEY=your_token_here
  ```

  Replace `your_token_here` with the token you received after signing up at [football-data.org](https://www.football-data.org/).

### 3. Server

Use a PHP-capable stack such as **XAMPP** (Apache + MySQL) or any similar environment.

**XAMPP:**

- Place the project in the XAMPP `htdocs` directory.
- Start **Apache** and **MySQL** from the XAMPP Control Panel.
- Open `http://localhost/Footcast/` in your browser.

### 4. Admin dashboard

- Log in with an admin user.
- Dashboard: `http://localhost/Footcast/admin-dashboard/index.php`
- Username: admin@example.com 
- Password: admin@exampe.com
- From there you can manage homepage content, sports, promotions, lineups, users, contact messages, and bets.

### 5. User dashboard

- Log in or create an account as a normal user.
- Dashboard: `http://localhost/Footcast/user-dashboard/index.php`

### 6. Navigation

Use the main navigation to move between **Home**, **Matches**, **Sports**, **Promotions**, **Lineups**, and **Standings** as applicable.

---

## Summary

| Item | Action |
|------|--------|
| **API-dependent features** | Sign up at [football-data.org](https://www.football-data.org/) and set `FOOTBALL_API_KEY` in `.env` |
| **Database** | Import the project’s SQL schema |
| **Server** | Run via XAMPP or another PHP + MySQL stack |
| **Issues with matches/lineups/settlement** | Confirm your API key is set and valid; some behaviour may be limited without it |
