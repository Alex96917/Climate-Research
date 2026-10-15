# Climate-Research
# Climate Research Registration

A PHP and MySQL website built for the **Kiambu National Polytechnic Web Design Skills Competition (Task 1)**.

A newly launched company is recruiting members to help research the causes of climate change in the country. Interested people register online, their details are stored in a database, and once registered they can log in to reach a members-only homepage.

---

## Features

**Registration page** (`register.php`)
- Form title: *Climate Research Registration*
- Fields: first name, last name, email address, phone number, gender (radio buttons), age, residential city (drop-down of five cities), password with confirmation
- Live validation hints and a password strength bar
- Show/hide password toggle
- Server-side validation, so the rules still apply if JavaScript is switched off
- Duplicate email detection

**Login page** (`login.php`)
- Email address and password
- Clear error messages without revealing which of the two was wrong

**Homepage** (`home.php`)
- Personalized welcome message, for example *"Welcome, Jane Wanjiku!"*
- Member profile card (initials avatar, email, city, member-since date)
- Information about the research project in six image cards with a green hover effect
- Logout button and footer

**Security**
- Passwords are **hashed** with `password_hash()` (bcrypt) and checked with `password_verify()`. No plain-text passwords are stored anywhere.
- All database queries use **PDO prepared statements** (protects against SQL injection)
- All output is escaped with `htmlspecialchars()` (protects against XSS)
- **CSRF tokens** on both forms
- Session ID regenerated at login (protects against session fixation)
- Session cookie set to `HttpOnly` and `SameSite=Lax`
- Homepage is protected: visitors who are not logged in are redirected to the login page
- No-cache headers on the homepage, so the Back button does not show it after logout

---

## Technologies

| Layer | Technology |
|---|---|
| Back end | PHP 8+ (PDO) |
| Database | MySQL / MariaDB |
| Front end | HTML5, CSS3, vanilla JavaScript |
| Local server | XAMPP (Apache + MySQL) |
| Font | Poppins (Google Fonts) |

---

## Requirements

- [XAMPP](https://www.apachefriends.org/) (or WAMP / MAMP / any Apache + PHP 8 + MySQL stack)
- A modern browser (Chrome, Edge or Firefox)
- Internet connection is optional. It is only used to load the Poppins font, and the site falls back to Arial without it.

---

## Setup Instructions

### 1. Copy the project

Copy the `climate-research` folder into XAMPP's web root:

```
C:\xampp\htdocs\climate-research\
```

### 2. Start the servers

Open the **XAMPP Control Panel** and click **Start** next to both **Apache** and **MySQL**.

### 3. Create the database

**Option A: import the file (recommended)**

1. Open `http://localhost/phpmyadmin`
2. Click the **Import** tab
3. Choose `database.sql` from the project folder
4. Click **Import**

**Option B: paste the SQL**

1. Open `http://localhost/phpmyadmin`
2. Click the **SQL** tab
3. Paste the contents of `database.sql` and click **Go**

Either way you end up with a database called `climate_research` containing a `members` table.

### 4. Check the database settings

Open `config.php`. The defaults match a standard XAMPP install:

```php
$host = "localhost";
$db   = "climate_research";
$user = "root";
$pass = "";
```

Only change these if your MySQL has a different username or password.

### 5. Open the site

```
http://localhost/climate-research/register.php
```

---

## How to Test

1. **Register**: open `register.php`, fill in the form and submit. Try bad input first (short password, mismatched passwords, no city) to see the validation messages.
2. **Check the database**: in phpMyAdmin, open `climate_research` > `members`. The `password_hash` column should hold a long string starting with `$2y$`, never the real password.
3. **Log in**: you are redirected to `login.php` with a success message. Enter your email and password.
4. **View the homepage**: you should see your personalized welcome message, your profile card and the project cards.
5. **Log out**: click **Logout**. Then press the browser's Back button. You should be sent to the login page, not shown the homepage.
6. **Protected page**: while logged out, open `http://localhost/climate-research/home.php` directly. You should be redirected to the login page.

---

## Project Structure

```
climate-research/
├── images/
│   ├── logo.png            Navbar logo
│   ├── logo-icon.png       Footer icon
│   ├── favicon.png         Browser tab icon
│   ├── hero.jpg            Banner / side-panel background
│   ├── about.jpg           Card images
│   ├── forest.jpg
│   ├── emissions.jpg
│   ├── weather.jpg
│   ├── waste.jpg
│   └── next.jpg
├── config.php              Database connection, session, security helpers
├── register.php            Registration form and processing
├── login.php               Login form and processing
├── home.php                Members-only homepage
├── logout.php              Ends the session
├── script.js               Validation hints, strength bar, password toggle
├── style.css               All styling
├── database.sql            Database and table setup
└── README.md               This file
```

---

## Database

**Database:** `climate_research`  **Table:** `members`

| Column | Type | Notes |
|---|---|---|
| `id` | INT, auto increment | Primary key |
| `first_name` | VARCHAR(50) | |
| `last_name` | VARCHAR(50) | |
| `email` | VARCHAR(100) | Unique, used as the username |
| `phone` | VARCHAR(20) | |
| `gender` | ENUM('Male','Female','Other') | |
| `age` | INT | 18 to 100 |
| `city` | VARCHAR(50) | One of five cities |
| `password_hash` | VARCHAR(255) | bcrypt hash, never plain text |
| `created_at` | TIMESTAMP | Set automatically |

**Cities offered in the drop-down:** Nairobi, Mombasa, Kisumu, Nakuru, Eldoret

---

## Troubleshooting

| Problem | Likely cause and fix |
|---|---|
| `Undefined variable $pdo` | `config.php` is not creating `$pdo`. Check it matches the file in this project, and that each page uses `require __DIR__ . '/config.php';`. |
| "Database connection failed." | MySQL is not running, or the database was not imported. Start MySQL in XAMPP and repeat step 3. |
| `404 Not Found` | The folder is in the wrong place or misnamed. It must be `htdocs\climate-research`. |
| Page looks unstyled or old | The browser is showing cached CSS. Press **Ctrl + F5**. |
| Card images or logo missing | Check the `images` folder is beside `style.css` and the file names match exactly, including lowercase `.jpg` and `.png`. |
| "Security check failed" | The form was open too long, or the page was cached. Refresh the page and submit again. |
| "That email is already registered" | Use a different email, or delete the row in phpMyAdmin. |

---


## Author

Prepared for the Kiambu National Polytechnic Web Design Skills Competition.

