# Admin Control - Quick Reference Guide

## 🎯 Admin Features at a Glance

### Dashboard
- **URL**: `/admin/dashboard`
- **Access**: Admin role only
- **Shows**: System statistics, recent activity, user distribution

### User Management
- **URL**: `/admin/users`
- **Features**: 
  - Create users with department assignment
  - Edit user details and status
  - Suspend/unsuspend accounts
  - Lock/unlock accounts
  - Activate/deactivate users
  - Track status changes in audit logs

### User Status Controls
| Status | Effect | Duration | User Access |
|--------|--------|----------|-------------|
| Active | Normal operation | Indefinite | ✅ Full access |
| Inactive | Account disabled | Indefinite | ❌ No access |
| Suspended | Temporary lockout | Can expire | ❌ No access |
| Locked | Permanent until unlocked | Until admin unlocks | ❌ No access |

### Department Management
- **URL**: `/admin/departments`
- **Features**:
  - Create/edit/delete departments
  - Assign department heads
  - Track department size
  - Manage contact information
  - Enable/disable departments

### Permissions Management
- **URL**: `/admin/permissions`
- **Features**:
  - Create new permissions
  - Assign permissions to roles
  - Manage role-based access
  - Edit role permissions

### System Settings
- **URL**: `/admin/settings`
- **Features**:
  - Configure app settings
  - Manage by category (general, email, system, security)
  - Support for multiple data types
  - Persistent storage

### Audit Logs
- **URL**: `/admin/audit-logs`
- **Features**:
  - View all system activities
  - Filter by user, action, date
  - Track detailed changes
  - IP address logging
  - Complete change history

### Access Control
- **URL**: `/admin/access-control`
- **Features**:
  - View restricted users
  - Manage suspensions
  - Manage locks
  - Quick access restoration
  - Statistics on restrictions

---

## 📋 Common Admin Tasks

### Suspend a User for 7 Days
```
1. Go to /admin/access-control
2. Find user
3. Click "Suspend"
4. Set days to 7
5. Add reason (optional)
6. Confirm
```

### Create a New Department
```
1. Go to /admin/departments
2. Click "+ New Department"
3. Fill in name, slug, description
4. Assign department head
5. Set contact info
6. Save
```

### Add a Permission to a Role
```
1. Go to /admin/roles
2. Click "Manage Permissions" for desired role
3. Select permissions from list
4. Save changes
```

### Configure System Settings
```
1. Go to /admin/settings
2. Modify values by group
3. Click "Save Settings"
4. Changes apply immediately
```

---

## 🔍 Monitoring & Auditing

### Check Audit Logs
- View all admin actions with timestamps
- Filter by user, action type, date
- View IP addresses and descriptions
- Track database changes

### View Access Statistics
- Total users count
- Active vs inactive breakdown
- Suspended users count
- Locked accounts count

---

## 🔐 Security Best Practices

1. **Regularly audit logs** - Check `/admin/audit-logs` daily
2. **Monitor suspicious accounts** - Use `/admin/access-control`
3. **Review permissions** - Ensure roles have appropriate permissions
4. **Update settings** - Configure security settings in `/admin/settings`
5. **Lock unused accounts** - Disable inactive user accounts
6. **Suspend violators** - Use temporary suspension for policy violations

---

## 📊 Admin Model Methods

### User Status Methods
```php
$user->isActive()        // Check if active
$user->isSuspended()     // Check if suspended
$user->isLocked()        // Check if locked
$user->canAccess()       // All checks combined

$user->suspend($until, $reason)  // Suspend with expiry
$user->unsuspend()               // Remove suspension
$user->lock()                    // Lock account
$user->unlock()                  // Unlock account
$user->activate()                // Activate account
$user->deactivate()              // Deactivate account
```

### Setting Methods
```php
Setting::get('key', 'default')           // Get setting value
Setting::set('key', 'value', 'type', 'group')  // Set setting
```

### AuditLog Methods
```php
AuditLog::log($action, $modelType, $modelId, $changes, $description)
// Example
AuditLog::log('update', 'User', 123, ['role' => ['staff' => 'admin']], 'Promoted user');
```

### Role/Permission Methods
```php
$role->givePermission($permission)       // Add permission to role
$role->revokePermission($permission)     // Remove permission from role
$role->hasPermission('permission_name')  // Check permission
```

---

## 🚨 Troubleshooting

### User can't access after suspension
- Check if suspension has expired
- Use `/admin/access-control` to unsuspend
- Verify account is not also locked

### Settings not saving
- Check database is writable
- Verify settings table exists
- Review error logs

### Audit logs empty
- Check if table exists
- Verify admin actions are logged
- Review logs at `/admin/audit-logs`

### Permissions not working
- Verify permissions table populated
- Check role-permissions relationships
- Ensure middleware is registered

---

## 📞 Support
For issues or questions about admin features, refer to [ADMIN_FEATURES.md](./ADMIN_FEATURES.md)
