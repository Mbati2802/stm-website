# Super Admin Control Center - Setup & Usage Guide

## 🔐 Overview

The Super Admin Control Center is a dedicated administrative platform that provides:

- **Single Super Admin User** - One person who controls all access
- **Granular Permission Management** - Define exactly who can do what
- **Comprehensive Audit Logging** - Track every action across all portals
- **2-Factor Authentication** - Secure login with OTP verification
- **Session Management** - Monitor and revoke user sessions
- **Emergency Controls** - Maintenance mode and portal lockdown
- **Real-time Activity Monitoring** - Dashboard with suspicious activity alerts

---

## 📦 Installation & Setup

### Step 1: Database Migrations

The super admin system requires new database tables. Two migration files have been created:

```bash
database/migration_super_admin.sql      # Main super admin tables
database/migration_enhance_users_table.sql  # Enhanced user tracking
```

### Step 2: Run the Seeder Script

Execute the interactive setup script to initialize the system:

```bash
php super_admin_seeder.php
```

This script will:
1. ✅ Run all database migrations
2. ✅ Create the initial super admin user
3. ✅ Set up default access matrix permissions
4. ✅ Initialize system settings

**Important**: During setup, you'll be prompted for:
- Full Name
- Email Address
- Strong Password (minimum 12 characters)

Example:
```
📦 Step 1: Running database migrations...
✅ Executed: migration_super_admin.sql
✅ Executed: migration_enhance_users_table.sql

👤 Step 2: Setting up Super Admin user...

📋 Enter Super Admin Details:
Full Name: John Administrator
Email Address: john@example.com
Password: MySecurePass123!
Confirm Password: MySecurePass123!
```

### Step 3: Access the System

After setup, access the super admin panel:

```
🌐 URL: https://yourdomain.com/super-admin/login
📧 Email: john@example.com
🔐 Password: MySecurePass123!
```

---

## 🔑 User & Authentication System

### Authentication Flow

1. **Initial Login**: Super admin enters email + password
2. **2FA Verification**: Six-digit OTP sent to registered email
3. **Session Created**: Secure session token stored in database
4. **Session Tracking**: All activity logged to audit trail

### Security Features

#### ✓ 2-Factor Authentication (2FA)
- OTP code sent via email
- 10-minute expiration window
- Prevents unauthorized access

#### ✓ IP Whitelisting
- Optional IP address restrictions
- Only allows login from specified IPs
- Can be managed in database

#### ✓ Account Lockout
- Automatically locks after 3 failed login attempts
- 15-minute lockout period
- Failed attempts are logged

#### ✓ Session Management
- All sessions stored in database
- Automatic timeout: 1 hour
- One-click logout functionality
- View all active sessions

#### ✓ Audit Trail
- Every login/logout recorded
- Failed attempts tracked
- IP address and device fingerprint captured

---

## 👥 User Management

### Creating New Users

Navigate to: **👥 Users** → **+ Add New User**

```
Name: Alice Johnson
Email: alice@example.com
Role: junior_admin
```

### Available Roles

| Role | Permissions |
|------|-------------|
| **super_admin** | Full access to everything |
| **junior_admin** | Manage users, content, students |
| **editor** | Manage website content only |
| **registrar** | Manage students and academic records |
| **teacher** | Manage courses and grades |
| **viewer** | Read-only access |

### Editing Users

- Click **Edit** to modify user details
- Change role, name, email, or status
- All changes are logged in audit trail

### Deleting Users

- Click **Delete** to remove a user
- Cannot delete yourself
- Deletion is logged with reason

---

## 🔑 Access Matrix - Permission Management

### What is the Access Matrix?

The Access Matrix replaces the old CSV-based permissions with a **granular, role-based system**:

- Define exactly which **role** can perform which **action** on which **resource**
- Support for conditional permissions
- Visual matrix interface for easy management
- Bulk permission updates

### Accessing the Matrix

Navigate to: **🔑 Access Matrix**

### Understanding the Matrix

```
              super_admin  junior_admin  editor  viewer
programmes        ✓            ✓         ✓       ✓
students          ✓            ✓         ✗       ✓
users             ✓            ✓         ✗       ✗
grades            ✓            ✓         ✗       ✓
```

✓ = Permission Granted
✗ = Permission Denied

### Managing Permissions

1. **Check a box** to grant permission
2. **Uncheck a box** to revoke permission
3. **Click Save Changes** to apply modifications
4. All changes are logged to audit trail

### Resources Available

- `users` - User management
- `students` - Student records
- `courses` - Course management
- `programmes` - Program/degree management
- `grades` - Grade management
- `reports` - System reports
- `settings` - System configuration
- `admin_logs` - Audit log access
- And many more...

### Actions Available

- `view` - Can view the resource
- `create` - Can create new items
- `edit` - Can modify existing items
- `delete` - Can delete items
- `export` - Can export data

---

## 📝 Audit Logs & Monitoring

### Accessing Audit Logs

Navigate to: **📝 Audit Logs**

### What Gets Logged?

Every action is tracked:
- ✅ User login/logout
- ✅ Content creation, modification, deletion
- ✅ User role changes
- ✅ Permission changes
- ✅ Data exports
- ✅ Bulk operations
- ✅ System settings changes
- ✅ Failed login attempts

### Log Details

Each log entry contains:
- **Timestamp** - When the action occurred
- **User Type** - admin, crm_admin, student, super_admin
- **Action** - create, edit, delete, login, export, etc.
- **Entity** - What was affected (users, students, courses, etc.)
- **IP Address** - Where the request came from
- **Status** - success, failed, or suspicious
- **Old Values** - Previous values (for edits/deletes)
- **New Values** - New values (for creates/edits)

### Filtering Logs

Filter by:
- **User Type** - admin, CRM admin, student, super admin
- **Action** - login, create, edit, delete, export, etc.
- **Entity Type** - users, students, courses, etc.
- **Status** - success, failed, or suspicious
- **Date Range** - View logs from specific dates
- **IP Address** - Find activity from specific IPs

### Example: Track a User's Changes

1. Go to **📝 Audit Logs**
2. Filter by **User Type**: "admin"
3. Filter by **User ID**: "5"
4. See all actions taken by this user
5. Review the "Old Values" and "New Values" columns

---

## ⚠️ Suspicious Activity Alerts

### What Triggers an Alert?

High-risk actions automatically create alerts:

- ❌ Failed login attempts (5+ in 15 minutes)
- ❌ Bulk user deletions
- ❌ Bulk permission changes
- ❌ Unauthorized IP access attempts
- ❌ Large data exports
- ❌ System settings modifications
- ❌ Invalid 2FA codes

### Viewing Alerts

Navigate to: **⚠️ Alerts**

Each alert shows:
- **Type** - What kind of suspicious activity
- **Severity** - low, medium, high, critical
- **User** - Who triggered it
- **Timestamp** - When it happened
- **IP Address** - Where it came from
- **Status** - new, reviewed, resolved, false alarm

### Reviewing Alerts

1. Click on an alert to view details
2. Mark as "reviewed", "resolved", or "false alarm"
3. Add notes about actions taken
4. Document the investigation

---

## 🔗 Session Management

### Active Sessions

Navigate to: **🔗 Sessions**

View all currently active sessions:
- User ID and type
- Login timestamp
- Last activity time
- IP address
- Device fingerprint

### Force Logout a User

If you suspect a compromised session:

1. Go to **🔗 Sessions**
2. Click **Logout** next to the session
3. User will be immediately logged out
4. Action is logged to audit trail

### Force Logout Everyone

Emergency option to logout all non-super-admin users:

```
POST /super-admin/force-logout-all
```

Use case: Suspected security breach

---

## 🚨 Emergency Controls

### Maintenance Mode

Temporarily disable public portals:

Navigate to: **🚨 Emergency** → **Maintenance Mode**

```
✓ Enable Maintenance Mode
📝 Message: "System undergoing scheduled maintenance. Please try again in 1 hour."
```

When enabled:
- 🔒 Public website shows maintenance message
- 🔒 Student portal unavailable
- ✅ Super admin & admin panel still accessible
- ✅ CRM remains operational

### Portal Lockdown

Disable all logins except super admin:

Navigate to: **🚨 Emergency** → **Portal Lockdown**

```
✓ Enable Portal Lockdown
```

When enabled:
- 🔒 No users can login
- ✅ Only super admin remains authenticated
- ✓ Useful for security investigations
- ✓ Action is logged as CRITICAL

### System Status

Real-time system overview:

Navigate to: **🚨 Emergency** → **System Status**

Displays:
- Total admin users
- Total students
- Active sessions count
- Total audit logs
- Pending security alerts

---

## 📊 Dashboard & Analytics

### Main Dashboard

Navigate to: **📊 Dashboard**

Shows real-time overview:
- 📈 Admin user count
- 🎓 Student enrollment count
- 🔗 Active session count
- 📝 Audit log statistics

### Activity Statistics

Last 7 days breakdown:
- Actions by type (create, edit, delete, login, export, etc.)
- Activity by user type (admin, CRM, student, super admin)
- Failed action count
- Suspicious alerts count

### Suspicious Activity Widget

Top 5 most recent alerts with:
- Alert type
- Severity badge (low, medium, high, critical)
- Description
- Timestamp
- User IP address

### Recent Activity Table

Latest 20 system-wide actions:
- Timestamp
- User type
- Action performed
- Entity affected
- IP address

---

## 🛡️ Security Best Practices

### Password Security

✅ **DO:**
- Use passwords with 12+ characters
- Mix uppercase, lowercase, numbers, symbols
- Change password every 30 days
- Use unique passwords for super admin
- Store password securely (password manager)

❌ **DON'T:**
- Share your super admin password
- Use simple passwords like "admin123"
- Reuse passwords from other systems
- Write password down on paper
- Use your name or email in password

### 2FA & IP Whitelisting

✅ **ENABLE:**
- 2-Factor Authentication (OTP to email)
- IP Whitelisting (only login from office/home)
- Session timeout (15-30 minutes)

### Monitoring

✅ **REGULARLY:**
- Review audit logs (weekly)
- Check suspicious activity alerts
- Monitor active sessions
- Review user permissions
- Check failed login attempts

### Incident Response

If you suspect compromise:

1. 🚨 **Immediately** change your password
2. 🚨 **Enable** Portal Lockdown
3. 🚨 **Review** audit logs for unauthorized actions
4. 🚨 **Check** suspicious activity alerts
5. 🚨 **Force logout** all users
6. 🚨 **Investigate** and document findings
7. 🚨 **Enable** Maintenance Mode if needed
8. 🚨 **Contact** IT security team

---

## 📋 Database Tables Reference

### super_admin
Stores super admin user account

```sql
- id: Unique identifier
- name: Full name
- email: Email address (unique)
- password_hash: Bcrypt hashed password
- ip_whitelist: Comma-separated IP addresses (optional)
- two_fa_enabled: 1 or 0
- two_fa_secret: TOTP secret (if using TOTP)
- last_login: Last successful login timestamp
- locked_until: Account lockout timestamp
- failed_login_attempts: Counter for failed attempts
- status: active, suspended, archived
```

### audit_logs
Logs every action in the system

```sql
- id: Log entry ID
- user_id: Who performed the action
- user_type: admin, crm_admin, student, super_admin
- action: create, edit, delete, login, export, etc.
- entity_type: What was affected
- entity_id: ID of affected entity
- old_values: JSON of previous values
- new_values: JSON of new values
- ip_address: Source IP address
- user_agent: Browser user agent
- timestamp: When action occurred
- status: success, failed, suspicious
```

### access_matrix
Stores role-based permissions

```sql
- id: Permission rule ID
- role_id: Role ID from users table
- resource: Resource name (users, students, courses, etc.)
- action: Action (view, create, edit, delete, export)
- conditions: JSON of conditional logic
- priority: Rule priority for conflicts
- created_by: Who created this permission
- created_at: When created
```

### user_sessions
Tracks all active sessions

```sql
- id: Session ID
- user_id: User ID
- user_type: admin, crm_admin, student, super_admin
- session_token: Unique session token
- ip_address: Login IP address
- device_fingerprint: Device identifier
- created_at: Session start time
- last_activity: Last activity timestamp
- expires_at: Session expiration time
- is_active: 1 or 0
```

### suspicious_activity_alerts
Tracks potential security issues

```sql
- id: Alert ID
- user_id: User involved
- user_type: User type
- alert_type: Type of suspicious activity
- description: Alert description
- ip_address: Source IP
- device_fingerprint: Device identifier
- severity: low, medium, high, critical
- status: new, reviewed, resolved, false_alarm
- reviewed_by: Super admin ID who reviewed
- reviewed_at: When reviewed
```

---

## 🔧 API Endpoints Reference

### Authentication

```bash
GET  /super-admin/login           # Login form
POST /super-admin/login           # Authenticate
POST /super-admin/verify-2fa      # Verify OTP
GET  /super-admin/logout          # Logout
```

### Dashboard & Monitoring

```bash
GET /super-admin/dashboard        # Main dashboard
GET /super-admin/system-status    # System overview
GET /super-admin/audit-logs       # Audit logs
GET /super-admin/suspicious-alerts # Security alerts
```

### User Management

```bash
GET  /super-admin/users           # List users
GET  /super-admin/create-user     # Create form
POST /super-admin/create-user     # Store new user
GET  /super-admin/edit-user/{id}  # Edit form
POST /super-admin/edit-user/{id}  # Update user
POST /super-admin/delete-user/{id}# Delete user
```

### Permission Management

```bash
GET  /super-admin/access-matrix        # Permission matrix
POST /super-admin/update-access-matrix # Update permissions
GET  /super-admin/user-permissions/{id}# User permissions
```

### Session Management

```bash
GET  /super-admin/sessions          # Active sessions
POST /super-admin/force-logout/{id} # Force logout user
POST /super-admin/force-logout-all  # Force logout all users
```

### Emergency Controls

```bash
GET  /super-admin/emergency-mode    # Emergency page
POST /super-admin/emergency-mode    # Toggle maintenance
POST /super-admin/lockdown-portal   # Emergency lockdown
```

---

## ❓ Troubleshooting

### "2FA code not received"

**Solution:**
1. Check spam/junk folder for email
2. Verify email address in database
3. Check mail server logs
4. Verify SMTP settings in config

### "Account locked - too many failed attempts"

**Solution:**
1. Wait 15 minutes for automatic unlock
2. Or manually update database:
   ```sql
   UPDATE super_admin 
   SET locked_until = NULL, failed_login_attempts = 0
   WHERE email = 'admin@example.com';
   ```

### "Session expired"

**Solution:**
1. Re-login to create new session
2. Increase session timeout in config if needed
3. Check database connection for session storage

### "Permission denied on resource"

**Solution:**
1. Check access matrix permissions
2. Verify role assignment
3. Review audit logs for permission changes
4. Clear browser cache

### "Database migration failed"

**Solution:**
1. Check MySQL version (requires 5.7+)
2. Verify database user has CREATE permission
3. Run migrations manually from MySQL client
4. Check for duplicate table errors

---

## 📞 Support & Documentation

For more information:
- Review controller code: `app/controllers/SuperAdminController.php`
- Check auth logic: `app/core/SuperAdminAuth.php`
- Audit system: `app/core/AuditLogger.php`
- Access control: `app/core/AccessMatrix.php`

---

## ✅ Checklist: Initial Setup

- [ ] Run `php super_admin_seeder.php`
- [ ] Verify super admin account created
- [ ] Login and test 2FA
- [ ] Set IP whitelist (if desired)
- [ ] Create additional admin users
- [ ] Review and adjust permissions in access matrix
- [ ] Test audit logging
- [ ] Configure email for OTP delivery
- [ ] Set up backup of super_admin credentials
- [ ] Document emergency procedures
- [ ] Train authorized personnel
- [ ] Enable maintenance mode alerts
- [ ] Set up monitoring for suspicious alerts

---

**Version:** 1.0  
**Last Updated:** 2026-06-24  
**Status:** Complete ✅
