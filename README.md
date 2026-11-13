[EN](README.md) | [TH](README.th.md)

<h1 align="center">PHP-CRUD-PDO-OOP</h1>

<p align="center">
  A minimal PHP + PDO CRUD app for learning: list, add, edit, and delete users.
</p>

<p align="center">
  <a href="#overview">Overview</a> ·
  <a href="#features">Features</a> ·
  <a href="#tech-stack">Tech stack</a> ·
  <a href="#project-structure">Structure</a> ·
  <a href="#database-setup">Database</a> ·
  <a href="#getting-started">Getting started</a> ·
  <a href="#environment-variables">Environment</a> ·
  <a href="#usage">Usage</a> ·
  <a href="#notes">Notes</a>
</p>

---

## Overview

This project is a teaching example for building a CRUD application in plain PHP. It stores users in a MySQL/MariaDB database, talks to the database through PDO in an OOP style (`Config` → `Database`), and drives the UI with Bootstrap 5 modals plus a small amount of vanilla JavaScript that talks to the backend over `fetch` (AJAX) instead of full page reloads.

The codebase is intentionally small and split into single-responsibility files so the request flow is easy to follow end to end:

1. `index.php` renders the page shell (table + add/edit modals).
2. `main.js` loads the user list and submits add/edit/delete requests to `action.php`.
3. `action.php` is the controller. It cleans input with `Util`, then calls the matching method on `Database`.
4. `Database` (in `db.php`) runs prepared statements against the `users` table via a PDO connection created in `Config` (in `config.php`).

## Features

- List all users in a responsive Bootstrap table, newest first (`id DESC`).
- Add a new user through a Bootstrap modal form.
- Edit an existing user in a modal, pre-filled from `action.php?edit=1&id=...` (JSON).
- Delete a user directly from its row; there is no confirmation prompt.
- No full page reloads: list, add, edit, and delete all run over `fetch`.
- Prepared statements with named placeholders for every query.
- Input sanitising helper (`trim`, `stripcslashes`, `htmlspecialchars`, `strip_tags`).
- Feedback alerts rendered as Bootstrap dismissible alerts.
- Empty-state row (`No users found in the Database`) when the table has no rows.

## Tech stack

| Layer | Technology |
| --- | --- |
| Backend | PHP 8.2.35 / 8.3.36 / 8.4.27 / 8.5.12 or newer, `pdo_mysql`; procedural entry points + OOP classes |
| Database | MySQL / MariaDB (`InnoDB`, `utf8` / `utf8_general_ci`); dump generated on MariaDB 10.4.32 |
| Data access | PDO with prepared statements and named placeholders |
| Frontend | HTML5, Bootstrap 5.3.3 via jsDelivr CDN, vanilla JavaScript (Fetch API) |
| Tooling | phpMyAdmin SQL dump; no Composer, npm, or build step |

## Project structure

```
learn-php-crud-pdo/
├── .gitattributes                  # LF line-ending normalisation rule
├── README.md                       # English documentation (this file)
├── README.th.md                    # Thai documentation
├── config.php                      # DB connection configuration class (PDO)
├── db.php                          # Database class: CRUD methods
├── util.php                        # Util class: input sanitising + alert markup
├── action.php                      # Controller: routes requests to Database
├── index.php                       # Page shell: table + add/edit modals
├── main.js                         # Frontend logic: fetch calls, modal wiring
└── database/
    └── php_crud_pdo_oop.sql        # Schema + seed data (phpMyAdmin dump)
```

### Key files

| File | Role |
| --- | --- |
| `config.php` | `Config`: DB constants, DSN, shared PDO connection (`PDO::FETCH_ASSOC`) |
| `db.php` | `Database extends Config`: `insert`, `read`, `readOne`, `update`, `delete` |
| `util.php` | `Util`: `testInput()` sanitiser and `showMessage()` Bootstrap alert |
| `action.php` | Controller: dispatches add/read/edit/update/delete requests |
| `index.php` | Single page: user table, add/edit modals, alert container |
| `main.js` | Loads the table and wires add/edit/delete over `fetch` |
| `database/php_crud_pdo_oop.sql` | Schema and two sample rows (phpMyAdmin dump) |

### Request flow

| Request | Method | Result |
| --- | --- | --- |
| `add` (form field) | POST | Sanitise input, insert a user, return a success alert |
| `read` | GET query (the front end sends POST with the same query) | HTML table rows, or the empty-state row |
| `edit` + `id` | GET | One user as JSON, used to fill the edit modal |
| `update` (form field) | POST | Sanitise input, update the user, return an alert |
| `delete` + `id` | GET | Delete the user and return an alert |

## Database setup

The dump contains the `users` table and two sample rows. It does **not** include `CREATE DATABASE` or `USE`, so create an empty database first and then import into it:

```bash
mysql -u root -p -e "CREATE DATABASE php_crud_pdo_oop CHARACTER SET utf8 COLLATE utf8_general_ci"
mysql -u root -p php_crud_pdo_oop < database/php_crud_pdo_oop.sql
```

Or create a database named `php_crud_pdo_oop` in phpMyAdmin and use its **Import** tab.

Table `users`:

| Column | Type | Attributes |
| --- | --- | --- |
| `id` | `int(10)` | Primary key, auto-increment |
| `first_name` | `varchar(255)` | Not null |
| `last_name` | `varchar(255)` | Not null |
| `email` | `varchar(255)` | Not null |
| `phone` | `varchar(10)` | Not null |
| `timestamp` | `timestamp` | Nullable; defaults to `CURRENT_TIMESTAMP`, updates on row change |

- Engine: `InnoDB`, default charset `utf8`, collation `utf8_general_ci`.
- Auto-increment starts at `3` after importing the two sample users (`UserOne`, `UserTwo`).
- Make sure the credentials in `config.php` match your environment.

## Getting started

Requirements:

- **PHP with the `pdo_mysql` extension** — the dump was generated with PHP 8.2.12; use a current patched release (8.2.35 / 8.3.36 / 8.4.27 / 8.5.12 or newer).
- **MySQL or MariaDB** — the dump was generated with MariaDB 10.4.32.
- **A PHP-capable web server** — PHP's built-in server, Apache, or Nginx.

Install:

1. Clone the project into your web root or any local directory:
   ```bash
   git clone https://github.com/NetParamad/learn-php-crud-pdo.git
   cd learn-php-crud-pdo
   ```
2. Create the database and import the schema (see [Database setup](#database-setup)).
3. Update the constants in `config.php` (`DBHOST`, `DBUSER`, `DBPASS`, `DBNAME`).
4. Start PHP's built-in server from the project directory:
   ```bash
   php -S localhost:8000
   ```

Then open `http://localhost:8000/index.php` in your browser.

## Environment variables

This project reads no environment variables and has no `.env.example`. Connection settings are hard-coded as private class constants in `config.php`:

| Constant | Required | Notes |
| --- | --- | --- |
| `DBHOST` | ✅ | Database host; defaults to `localhost` |
| `DBUSER` | ✅ | Database user; defaults to `root` |
| `DBPASS` | ✅ | Database password; defaults to empty |
| `DBNAME` | ✅ | Database name; defaults to `php_crud_pdo_oop` |

These are PHP constants, not environment variables. Move them to environment variables or a git-ignored local config before any real deployment.

## Usage

1. The page loads and immediately lists all users from the database.
2. Click **Add New User**, fill in first name, last name, e-mail, and phone, then submit. A success alert appears and the table refreshes.
3. Click **Edit** on a row to open a pre-filled modal, change the fields, and submit to update the record.
4. Click **Delete** on a row to remove that user immediately.

## Notes

- Credentials are hard-coded in `config.php`. Treat this as a learning project only and externalise secrets before any production use.
- Input cleanup happens in PHP (`Util::testInput`), but there is no server-side type/format validation, no CSRF protection, and no authentication. Add those before exposing the app.
- The `phone` column is `varchar(10)`, matching the 10-digit format used in the sample data.
- `timestamp` is managed by the database and shows the last time a row was inserted or changed.
- **License** — MIT (see [LICENSE](LICENSE)).
