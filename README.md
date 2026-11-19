# Xlator — PHP Edition with Admin Portal for Hostinger

A faithful, high-performance, modular **PHP & Pure CSS** version of [xlator.lovable.app](https://xlator.lovable.app/), pre-configured for direct deployment on **Hostinger** (hPanel, Apache/LiteSpeed web servers) with a full **Events Management Admin Panel**.

---

## 🔐 Admin Portal

Access the backend management system to perform full **CRUD** operations on events:

- **Local URL**: [http://localhost:8000/admin/](http://localhost:8000/admin/)
- **Live URL**: `https://yourdomain.com/admin/`

*(Admin credentials and settings are configured in [includes/config.php](file:///c:/Users/NIDHIN/Desktop/uxhub/xlatorweb/includes/config.php)).*

### Admin Features:
- **Create**: Add new offline sessions, masterclasses, open houses, or workshops with dates, categories, formats, locations, summaries, and full descriptions.
- **Read**: View all events in an intuitive dashboard with stats (Total, Upcoming, Past, Drafts).
- **Update**: Edit existing events, modify schedules, change venues, or switch cover imagery.
- **Delete**: Remove obsolete sessions with confirmation prompts.
- **Publish / Draft Toggle**: Control which events are visible on the live public website.
- **Zero-Setup Database**: Uses an atomic file-based JSON store in `data/events.json` protected by `data/.htaccess`. No MySQL configuration required!

---

## 📁 Directory Structure

```
xlatorweb/
│
├── .htaccess                             # Hostinger Apache config (Clean URLs, Gzip, Caching, Security)
├── index.php                             # Homepage (dynamically showcases latest upcoming events)
├── program.php                           # Program & Framework comparison (Xlator vs. Elite)
├── mentors.php                           # Mentors profile page (Brand Swamy & Faisal Cheeran)
├── events.php                            # Events schedule & upcoming session filter (dynamic)
├── event-detail.php                      # Dynamic single event detail page
├── join.php                              # Application & membership form
├── 404.php                               # Custom branded 404 error page
│
├── admin/                                # Admin Backend Management Portal
│   ├── index.php                         # Events dashboard & data table
│   ├── event-edit.php                    # Create & Edit event form
│   ├── event-delete.php                  # Secure event deletion handler
│   ├── login.php                         # Admin authentication page
│   └── logout.php                        # Session destruction handler
│
├── api/
│   └── submit-application.php            # PHP backend for lead capture, anti-spam, and email alerts
│
├── includes/
│   ├── config.php                        # Site configuration, credentials, email settings
│   ├── events-db.php                     # Events Data Access Layer (CRUD functions)
│   ├── header.php                        # Global modular header & mobile drawer
│   └── footer.php                        # Global modular footer with dynamic copyright year & admin link
│
├── data/
│   ├── .htaccess                         # Secures lead data & events from direct browser access
│   └── events.json                       # Persistent JSON event database
│
├── css/
│   └── styles.css                        # Complete Pure CSS stylesheet (Tailwind utilities + design tokens)
│
├── images/                               # High-resolution logos & photography
│   ├── logo-black.png
│   ├── logo-white.png
│   ├── hero-handshake.jpg
│   ├── team-together.jpg
│   ├── mentor-brand-swamy.jpg
│   ├── mentor-faisal-cheeran.jpg
│   ├── collaboration.jpg
│   ├── building-blocks.jpg, community.jpg, ...
│   └── favicon.png
│
└── js/
    └── main.js                           # Vanilla JS for mobile drawer, event tabs & AJAX form UX
```

---

## 🚀 How to Deploy to Hostinger

### Method 1: Using Hostinger File Manager (Quickest)

1. Log in to your **Hostinger hPanel** (`hpanel.hostinger.com`).
2. Navigate to **Websites** &rarr; click **Manage** next to your domain.
3. Open **File Manager** (Files &rarr; File Manager &rarr; Access files).
4. Go into the **`public_html`** directory.
5. Upload all the files and folders from this project directly into `public_html`.
   *(You can select all files, create a `.zip` on your computer, upload the `.zip`, and use Hostinger's "Extract" feature).*
6. Your website and admin panel are live immediately!

---

## ⚙️ Configuration

### Admin Credentials & Notification Email

Configure credentials and notification settings in [includes/config.php](file:///c:/Users/NIDHIN/Desktop/uxhub/xlatorweb/includes/config.php):

```php
// Notification recipient email for join applications
define('ADMIN_EMAIL', 'yourname@yourdomain.com');

// Admin Portal Credentials
define('ADMIN_USERNAME', 'your_username');
// Generate a secure bcrypt hash for your password:
define('ADMIN_PASSWORD_HASH', 'your_bcrypt_hash_here');
```
