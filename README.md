# TraceX

TraceX is a lightweight web-based personal finance tracker built with native PHP, MySQL, Tailwind CSS, and JavaScript.

It helps users track daily expenses, organize spending by category, manage budgets, follow savings goals, and review spending insights from a simple dashboard.

## What's New

- Public landing page with SEO metadata, Open Graph tags, sitemap, robots.txt, and a terms page.
- Dashboard summaries for current month expenses, budget totals, savings deposits, category counts, recent transactions, and budget progress.
- Chart-based dashboard views using Chart.js for monthly spending and category breakdowns.
- Expense filtering by date range, category, minimum amount, and maximum amount.
- Budget CRUD with category/month duplicate protection and spent-vs-budget progress.
- Savings goals with deposits, withdrawals, status tracking, and withdrawal validation.
- Insights page with current-month category analysis, daily average, trend comparison, top expenses, and simple spending suggestions.
- Profile page with editable user details and account statistics.
- Database setup script with fresh rebuild, optional seeding, charset normalization, and connection-kill support for locked local databases.
- Local vendor assets for Font Awesome, Flatpickr, Chart.js, SweetAlert2, and Work Sans.

## Core Features

### Authentication

- User registration and login.
- Password hashing with PHP's password API.
- Remember-me login token support.
- Guest and logged-in route guards.

### Expense Management

- Add, edit, and delete expense records.
- Store amount, date, category, payment method, notes, and paid/unpaid status.
- Filter expense history by date range, category, and amount range.
- Paginated expense list.

### Categories

- Seeded default spending categories.
- Category list with search support.
- Category icons, colors, and expense counts.

### Budgets

- Create monthly budgets per category.
- Edit and delete budgets.
- Prevent duplicate budgets for the same user, category, and month.
- Compare budget amount against actual spending.

### Savings

- Create and manage savings goals.
- Track target amount, start date, target date, and status.
- Add deposit and withdrawal transactions.
- Prevent withdrawals greater than the current saved amount.
- Show recent savings transactions.

### Reports and Insights

- Dashboard charts for monthly expenses and category breakdowns.
- Reports page with summary cards, charts, secondary reports, and recent expenses.
- Insights page with category spending, budget comparisons, daily average, month-over-month trend, top expenses, and suggestions.

### Profile

- View account details.
- Update name and email.
- See account statistics such as expenses added, savings goals, categories, and days active.

## Tech Stack

| Layer | Technology |
| --- | --- |
| Backend | Native PHP, PDO |
| Frontend | HTML, Tailwind CSS, JavaScript |
| Database | MySQL |
| Charts | Chart.js |
| UI Helpers | Flatpickr, SweetAlert2, Font Awesome |
| Local Server | PHP built-in server, Apache, XAMPP, or WAMP |

## Project Structure

```text
tracex/
|-- config/                 # Database configuration
|-- database/               # Setup script and SQL dump
|-- login/                  # Sign-in page
|-- register/               # Sign-up page
|-- public/                 # Authenticated app pages and public assets
|-- src/                    # Bootstrap, helpers, Tailwind input/output
|-- views/                  # Reusable view components and page sections
|-- index.php               # Public landing page
|-- terms-and-conditions.php
|-- robots.txt
|-- sitemap.xml
`-- package.json
```

## Prerequisites

- PHP 8.0 or higher recommended.
- MySQL 5.7 or higher.
- Node.js and npm for Tailwind builds.
- Apache/XAMPP/WAMP or the PHP built-in development server.

## Installation

### 1. Clone the project

```bash
git clone https://github.com/aungkyawthetx/tracex.git
cd tracex
```

### 2. Install frontend dependencies

```bash
npm install
```

### 3. Create the database

Create a MySQL database before running the setup script:

```sql
CREATE DATABASE tracex CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Configure database connection

Open `config/db.php` and update the values for your local environment:

```php
$DB_HOST = "localhost";
$DB_NAME = "tracex";
$DB_USER = "root";
$DB_PASS = "";
```

### 5. Create tables and seed defaults

```bash
php database/setup.php
```

Useful setup options:

```bash
php database/setup.php --fresh
php database/setup.php --fresh --kill-connections
php database/setup.php --no-seed
php database/setup.php --fresh --no-seed
```

### 6. Build Tailwind CSS

```bash
npm run build
```

For active UI development, run:

```bash
npm run watch
```

### 7. Run the application

```bash
npm run serve
```

Then open:

```text
http://localhost:8000
```

The landing page is available at `/`. After signing in, the app redirects to:

```text
http://localhost:8000/public/index.php
```

## Database Tables

The setup script manages these tables:

- `users`
- `categories`
- `payment_methods`
- `expenses`
- `budgets`
- `savings`
- `saving_transactions`

## npm Scripts

| Command | Description |
| --- | --- |
| `npm run serve` | Start PHP dev server at `localhost:8000`. |
| `npm run build` | Build minified Tailwind CSS. |
| `npm run watch` | Watch Tailwind input and rebuild on changes. |
| `npm test` | Run the PHPUnit test suite. |

## Testing

Install the dev dependencies once:

```bash
composer install
```

Run the unit tests:

```bash
composer test
```

Run them with a line coverage report (requires pcov or Xdebug):

```bash
php -d pcov.enabled=1 vendor/bin/phpunit --coverage-text
```

Tests live in `tests/` and cover the reusable helpers in `src/helpers/`. Database-backed helpers are tested against `PDO` mocks, so no MySQL instance is needed.

## Troubleshooting

- If the database connection fails, confirm `config/db.php` and make sure the database exists.
- If tables are missing or outdated, run `php database/setup.php --fresh`.
- If fresh setup hangs because of local MySQL locks, run `php database/setup.php --fresh --kill-connections`.
- If styles are missing or stale, run `npm run build` or `npm run watch`.
- If `npm run serve` is already using port `8000`, stop the existing server or run PHP manually on another port.
