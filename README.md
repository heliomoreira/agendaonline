

## Installation & Configuration


### Configure Apache
Virtualmin > Web Configuration > Website Options > Website matches all subdomains

### Create User
Webmin > Servers > MariaDB Database Server > User Permissions > Create a new user (with all permissions)

### 



### Image Upload

php artisan storage:link
Create folder storage/app/public/tenants/logos

### Install Redis
sudo dnf install redis

sudo systemctl enable redis

sudo systemctl start redis
