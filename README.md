# Blood Bank Management System

A web-based application for managing blood bank operations, including donor registration, staff management, and administrative oversight.

## Features

### User Roles
- **Admin**: Full access to all system features, including user management and reports
- **Staff**: Access to donor management and basic operations
- **Donor**: Can view their donation history (future feature)

### Core Functionality
- **Donor Registration**: Comprehensive form for adding new donors with automatic address population based on city
- **Dashboard**: Role-based dashboards for admin and staff
- **Authentication**: Secure login/logout system
- **Donor Management**: Add, view, and manage donor information

### Donor Registration Features
- Automatic population of district, state, and pincode based on city entry
- Fuzzy matching for city names to handle minor spelling errors
- Age calculation from date of birth
- Validation for mobile numbers and other required fields
- Support for replacement and voluntary donations

## Technology Stack

- **Backend**: PHP 7+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Server**: Apache (XAMPP recommended)

## Installation

### Prerequisites
- XAMPP (or similar Apache/MySQL/PHP stack)
- Git

### Setup Steps

1. **Clone the repository:**
   ```bash
   git clone https://github.com/bhishmang-patel/Blood_Bank_Management.git
   cd Blood_Bank_Management
   ```

2. **Move to XAMPP htdocs:**
   - Copy the project folder to `C:\xampp\htdocs\bloodbank`

3. **Database Setup:**
   - Start XAMPP and ensure Apache and MySQL are running
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `bloodbank`
   - Import the SQL schema (if provided) or create tables as needed

4. **Database Tables:**
   The system uses the following main table:
   - `donors`: Stores donor information

5. **Configuration:**
   - Update `db.php` with your database credentials if different from defaults
   - Default MySQL port is 3307 (XAMPP default)

6. **Access the Application:**
   - Open browser and go to: `http://localhost/bloodbank`
   - Login with appropriate credentials

## Usage

### Adding a Donor
1. Navigate to the Add Donor page
2. Fill in the donor's personal information
3. Enter the city/village - district, state, and pincode will auto-populate
4. Complete health and donation information
5. Submit the form

### City Address Auto-Population
- Enter a city name in the "City/Village" field
- The system will automatically fill district, state, and pincode
- Supports fuzzy matching for minor spelling variations
- Covers major Indian cities

## File Structure

```
bloodbank/
├── add_donor.php          # Donor registration form
├── dashboard.php          # Main dashboard
├── dashboard_admin.php    # Admin dashboard
├── dashboard_staff.php    # Staff dashboard
├── db.php                 # Database configuration
├── index.html             # Landing page
├── login.php              # Login page
├── logout.php             # Logout functionality
├── css/
│   ├── add_donor.css      # Styles for donor form
│   └── style.css          # General styles
├── js/
│   ├── add_donor.js       # JavaScript for donor form
│   └── dashboard.js       # Dashboard scripts
└── README.md              # This file
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is for educational purposes. Please ensure compliance with local regulations for blood bank management systems.

## Support

For issues or questions, please create an issue in the GitHub repository.
