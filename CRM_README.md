# Student Admissions CRM - Setup Guide

## Overview
This CRM system manages student admissions from inquiry through enrollment. It uses a separate database (`crm_college`) and has its own authentication system.

## Database Setup

### 1. Create CRM Database
Run the migration file to create the CRM database and tables:

```bash
mysql -u root -p < database/migration_crm_database.sql
```

Or run the SQL commands manually in phpMyAdmin or your MySQL client.

### 2. Configure Database Connection
Edit `config/crm_config.php` to match your database credentials:

```php
'db' => [
    'host' => 'localhost',
    'name' => 'crm_college',
    'user' => 'your_db_user',
    'pass' => 'your_db_password',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
]
```

## Default Login

- **Username**: `admin`
- **Password**: `admin123`

⚠️ **IMPORTANT**: Change the default password immediately after first login!

## Access the CRM

Navigate to: `https://yourdomain.com/crm` or `https://yourdomain.com/crm/login`

## Features

### 1. Dashboard
- Total inquiries
- Contacted leads
- Interested leads
- Admission offers issued
- Registration paid (converted students)
- Enrolled students
- Lost leads
- Revenue from registration fees
- Conversion rate

### 2. Lead Management
- Create new leads manually
- View all leads with status
- Update lead status through pipeline
- Assign leads to admissions officers
- View lead history and communications

### 3. Status Pipeline
The CRM uses the following status pipeline:
1. **New Inquiry** - Lead captured from any source
2. **Contacted** - Admissions team has reached out
3. **Interested** - Lead has shown interest in courses
4. **Admission Offered** - Admission letter has been issued
5. **Payment Pending** - Offer made but no registration fee yet
6. **Registration Paid** - Registration fee paid (Converted student)
7. **Enrolled** - Student reported to campus
8. **Lost** - Lead not converted

### 4. Payment Recording
- Record registration fee payments manually
- Enter M-PESA transaction codes
- Enter bank deposit references
- Verify payments
- Automatic status update to "Registration Paid" on verification
- Automatic payment confirmation SMS sent on verification

### 5. Admission Letters
- Generate provisional admission letters (before payment)
- Generate confirmed admission letters (after payment)
- PDF format with college branding
- Print/download functionality
- 14-day validity for provisional offers

### 6. Communication System
- Send SMS messages to leads
- Send WhatsApp messages (requires provider setup)
- Send emails to leads
- Communication history tracking
- Automatic payment confirmation SMS

## Communication Provider Setup

### SMS Integration (Africa's Talking)
To enable actual SMS sending, update `config/crm_config.php`:

```php
'sms' => [
    'enabled' => true,
    'api_key' => 'your_africastalking_api_key',
    'sender_id' => 'STMCH'
]
```

Then update `app/core/CommunicationService.php` to integrate with Africa's Talking API.

### WhatsApp Integration
To enable WhatsApp, you need:
1. WhatsApp Business API access from Meta
2. Or use a third-party provider like Africa's Talking

Update `config/crm_config.php`:

```php
'whatsapp' => [
    'enabled' => true,
    'api_key' => 'your_whatsapp_api_key',
    'phone_number' => '254XXXXXXXXX'
]
```

### Email Integration
To enable email sending, integrate PHPMailer or similar service in `app/core/CommunicationService.php`.

## Adding CRM Users

Currently, you can add users directly in the database:

```sql
INSERT INTO crm_users (username, password_hash, name, role, email) 
VALUES ('username', password_hash('password', PASSWORD_BCRYPT), 'Full Name', 'officer', 'email@example.com');
```

Roles:
- `admin` - Full access to all features
- `officer` - Can manage leads, record payments, generate letters

## Automated Reminders (Future Enhancement)

The CRM has a reminders table structure for automated follow-ups. To implement:
1. Create a cron job to run daily
2. Check for pending reminders
3. Send scheduled messages
4. Update reminder status

## Integration with Main System

When a student reports to campus (enrolled):
1. Export student data from CRM
2. Import into main `student_accounts` table
3. Generate official admission number
4. Grant student portal access

This can be done manually or via a future automated process.

## Troubleshooting

### 404 Errors
- Ensure `.htaccess` file is in the web root
- Verify Apache mod_rewrite is enabled
- Check that routes are defined in both `index.php` and `public/index.php`

### Database Connection Errors
- Verify `crm_college` database exists
- Check database credentials in `config/crm_config.php`
- Ensure MySQL user has proper permissions

### SMS Not Sending
- Communication is currently simulated (logs to error log)
- Set up actual SMS provider integration
- Check error logs: `error_log("SMS to {$phone}: {$message}");`

## File Structure

```
app/
├── controllers/
│   └── CRMController.php          # Main CRM controller
├── core/
│   ├── CRMAuth.php                 # CRM authentication
│   └── CommunicationService.php    # SMS/WhatsApp/Email service
├── views/
│   ├── layouts/
│   │   └── crm.php                 # CRM layout
│   └── crm/
│       ├── login.php               # Login page
│       ├── dashboard.php           # Dashboard
│       ├── leads.php               # Leads list
│       ├── create_lead.php         # Create lead form
│       ├── view_lead.php           # Lead details
│       ├── record_payment.php      # Payment recording
│       └── admission_letter.php    # Admission letter PDF
config/
└── crm_config.php                 # CRM configuration
database/
└── migration_crm_database.sql      # Database migration
```

## Security Notes

1. Change default admin password immediately
2. Use strong passwords for database users
3. Keep API keys secure
4. Regularly backup CRM database
5. Implement IP whitelisting if needed
6. Use HTTPS in production

## Support

For issues or questions:
- Check error logs
- Verify database connection
- Ensure all files are deployed
- Check web server configuration
