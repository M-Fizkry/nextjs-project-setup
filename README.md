# Inventory Control System

A simple and effective PHP-based inventory control system with multilingual support.

## Features

- User Authentication
  - Login system with role-based access (Admin/User)
  - Session management
  - Password hashing for security

- Dashboard
  - Stock level monitoring
  - Visual representation with charts
  - Stock status indicators (Low/Normal/Over Stock)

- Bill of Materials (BOM)
  - Create and manage BOMs
  - Add multiple materials with quantities
  - Edit and delete functionality
  - Material relationship tracking

- Production Planning
  - Three types of production plans
  - Achievement tracking
  - Production status monitoring

- Multilingual Support
  - Indonesian (Default)
  - English
  - Japanese
  - Easy to add more languages

- System Settings
  - Language preference
  - System title customization
  - Logo management

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- PDO PHP Extension
- Web server (Apache/Nginx) or PHP built-in server

## Installation

1. Clone or download this repository
2. Create a MySQL database
3. Run the setup script:
   ```bash
   php setup.php
   ```
4. Update database connection settings in `config/database.php`
5. Start the PHP development server:
   ```bash
   php -S localhost:8000
   ```
6. Visit http://localhost:8000 in your browser

## Default Login Credentials

- Username: admin
- Password: admin123

**Important:** Change the default password after first login.

## Project Structure

```
/
├── assets/           # Static assets (CSS, JS, images)
├── config/           # Configuration files
├── controllers/      # PHP controllers
├── languages/        # Language files
├── models/           # Database models
├── views/           # PHP view files
│   ├── auth/        # Authentication views
│   ├── dashboard/   # Dashboard views
│   ├── bom/         # BOM management views
│   ├── production/  # Production planning views
│   ├── users/       # User management views
│   └── settings/    # System settings views
├── database.sql     # Database schema
├── index.php        # Entry point
├── setup.php        # Setup script
└── README.md        # Documentation
```

## Security Considerations

1. Change default admin password
2. Update database credentials
3. Configure proper web server
4. Set appropriate file permissions
5. Enable error reporting only in development

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.
