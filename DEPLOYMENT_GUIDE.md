# Garden Library Deployment Guide

## Pre-Deployment Checklist

### 1. Database Setup
- [ ] Export your local database using phpMyAdmin or command line
- [ ] Remove any test/debug data
- [ ] Update database credentials in production

### 2. Configuration Updates Needed

#### Update `config/config.php`:
```php
// Production environment settings
$db_host = 'your-hosting-db-host'; // Usually 'localhost' or provided by host
$db_user = 'your-db-username';     // From hosting provider
$db_pass = 'your-db-password';     // From hosting provider  
$db_name = 'your-db-name';         // Database name on hosting
```

#### Update `includes/db_connection.php`:
```php
// Remove debug output for production
$host = 'your-hosting-db-host';
$dbname = 'your-database-name';
$username = 'your-db-username';
$password = 'your-db-password';

// Remove or comment out the debug echo statement:
// echo "Connected successfully. Tables found: " . implode(', ', $tables);
```

### 3. Security Updates for Production
- [ ] Remove any debug/test output
- [ ] Ensure proper error handling
- [ ] Set secure file permissions
- [ ] Use environment variables for sensitive data (optional)

## Deployment Methods

### Method 1: File Manager (Easiest)
1. Login to your hosting control panel (cPanel/Plesk)
2. Open File Manager
3. Navigate to public_html (or similar public folder)
4. Upload all your files except:
   - Local config files (update them instead)
   - README.md (optional)
   - Any .git folders

### Method 2: FTP/SFTP
1. Use FileZilla, WinSCP, or similar FTP client
2. Connect using credentials from your hosting provider
3. Upload files to the public_html directory

### Method 3: Git Deployment (Advanced)
1. Push code to GitHub/GitLab
2. Use hosting provider's Git integration
3. Set up automatic deployments

## Database Import
1. Access phpMyAdmin on your hosting account
2. Create a new database
3. Import your `sql_of_library_system.sql` file
4. Update database credentials in your config files

## Domain Setup
1. Point your domain to your hosting provider's nameservers
2. Update any absolute URLs in your code
3. Set up SSL certificate (usually free with hosting)

## Testing After Deployment
- [ ] Test login functionality
- [ ] Test database connections
- [ ] Check all pages load correctly
- [ ] Verify image and CSS loading
- [ ] Test admin panel
- [ ] Test book borrowing system

## Common Issues and Solutions

### Database Connection Issues
- Check database credentials
- Verify database exists and is imported
- Check if database server allows remote connections

### File Permission Issues
- Set folders to 755 permissions
- Set files to 644 permissions
- Special attention to config files

### URL/Path Issues
- Update site_url in config.php
- Check relative paths in includes
- Verify image and asset paths

## Recommended Hosting Providers for PHP Projects (2025)

### Free Options (Good for Testing):
- **InfinityFree** - Free PHP hosting with MySQL, cPanel access
- **FreeHosting.com** - Free tier with PHP/MySQL support
- **AwardSpace** - 1GB free hosting with PHP/MySQL
- **Heroku** - Free tier (requires Git deployment)
- **Railway** - Free tier for small projects

### Budget Options:
- **Hostinger** (~$2-4/month) - Very popular, good performance
- **Namecheap** (~$3-5/month) - Reliable with good support
- **DreamHost** (~$3-6/month) - WordPress optimized but great for PHP

### Reliable Premium Options:
- **SiteGround** (~$6-15/month) - Excellent performance and support
- **Bluehost** (~$5-10/month) - Popular choice, good for beginners
- **A2 Hosting** (~$6-12/month) - Fast SSD hosting

### For Developers/Advanced:
- **DigitalOcean** (~$6/month VPS) - Full control, requires setup
- **Vultr** (~$6/month VPS) - Good alternative to DigitalOcean
- **Linode** (~$5/month VPS) - Developer-friendly cloud hosting

## Quick Start Recommendation (Updated 2025)

For your first deployment, I'd recommend:

**Free Option**: **InfinityFree** - Best free hosting currently available
- ✅ PHP 8+ support  
- ✅ MySQL databases
- ✅ cPanel interface
- ✅ 5GB storage
- ✅ No forced ads
- ✅ Custom domains supported

**Paid Option**: **Hostinger** (~$3/month) - Best value for money
- ✅ Excellent performance
- ✅ Easy setup with cPanel
- ✅ PHP/MySQL support  
- ✅ Free SSL certificate
- ✅ 24/7 support
- ✅ 30-day money-back guarantee

Both options provide:
1. **Easy setup** - cPanel interface
2. **PHP/MySQL support** - Perfect for your project
3. **File manager** - No need for FTP clients initially
4. **phpMyAdmin access** - Easy database import
