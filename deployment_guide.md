# 🚀 Deployment Guide: AWS EC2 (LAMP Stack)

This guide details how to deploy the **GED Exam System** to an AWS EC2 instance using Git.

## Phase 1: Local Git Setup
Since your project is not yet a git repository, you need to initialize it and push it to a remote provider (GitHub, GitLab, or Bitbucket).

1.  **Initialize Git:**
    Open your terminal in `c:\xampp\htdocs\Pearson` and run:
    ```bash
    git init
    echo "uploads/" > .gitignore
    echo "backend/config.php" >> .gitignore
    echo ".env" >> .gitignore
    git add .
    git commit -m "Initial commit"
    ```
    > **Note:** We exclude `config.php` because production credentials will be different from localhost. You will create a custom `config.php` on the server.

2.  **Push to Remote:**
    *   Run the commands to push to your repository:
        ```bash
        git remote add origin https://github.com/mmhein217/gedportal.git
        git branch -M main
        git push -u origin main
        ```

---

## Phase 2: Launch AWS EC2 Instance

1.  **Log in to AWS Console** and go to **EC2**.
2.  **Launch Instance**:
    *   **Name**: `GED-Exam-Server`
    *   **OS Image**: `Ubuntu Server 22.04 LTS` (Free Tier Eligible)
    *   **Instance Type**: `t2.micro` (Free Tier) or `t3.small` (Recommended for production).
    *   **Key Pair**: Create a new key pair (e.g., `ged-key.pem`) and **download it**. Keep it safe!
    *   **Security Group**: Create a new one allowing:
        *   **SSH (TCP 22)**: My IP (for security) or Anywhere.
        *   **HTTP (TCP 80)**: Anywhere (0.0.0.0/0).
        *   **HTTPS (TCP 443)**: Anywhere (0.0.0.0/0).
3.  **Launch** and wait for the "Running" state.

---

## Phase 3: Connect & Install Dependencies

1.  **Connect to Instance**:
    Use a terminal (or Git Bash on Windows):
    ```bash
    ssh -i "path/to/ged-key.pem" ubuntu@YOUR_EC2_PUBLIC_IP
    ```

2.  **Update & Install LAMP Stack**:
    Run these commands on the server:
    ```bash
    sudo apt update && sudo apt upgrade -y
    sudo apt install apache2 mysql-server php php-mysql php-cli php-curl php-gd php-mbstring php-xml php-zip unzip -y
    ```

3.  **Start Services**:
    ```bash
    sudo systemctl enable apache2
    sudo systemctl start apache2
    sudo systemctl enable mysql
    ```

---

## Phase 4: Database Setup

1.  **Secure MySQL**:
    ```bash
    sudo mysql_secure_installation
    # Follow prompts (Set password, Remove anonymous users, Disallow root login remotely, Remove test DB)
    ```

2.  **Create Database & User**:
    ```bash
    sudo mysql -u root -p
    ```
    Inside the MySQL shell:
    ```sql
    CREATE DATABASE exam_management;
    CREATE USER 'ged_user'@'localhost' IDENTIFIED BY 'StrongPassword123!';
    GRANT ALL PRIVILEGES ON exam_management.* TO 'ged_user'@'localhost';
    FLUSH PRIVILEGES;
    EXIT;
    ```

3.  **Upload & Import Seed Data**:
    *   **Option A (Copy Paste)**:
        Create the file on the server and paste the content of `database_seed_ec2.sql`.
        ```bash
        nano seed.sql
        # Right-click to paste, then Ctrl+O (Save), Ctrl+X (Exit)
        mysql -u ged_user -p exam_management < seed.sql
        ```
    *   **Option B (SCP)**: Upload from your local PC.
    *   **Option B (SCP)**: Upload from your local PC.
        > **Windows Users:** When generating the SQL file, use `--result-file` to avoid encoding errors (ASCII '\0'):
        > `mysqldump -u root exam_management --result-file=database_export_latest.sql`
        
        Use `database_export_latest.sql` (which contains all your data).
        ```bash
        scp -i "key.pem" database_export_latest.sql ubuntu@IP:/home/ubuntu/seed.sql
        ```

---

## Phase 5: Deploy Application

1.  **Clone Repository**:
    ```bash
    cd /var/www/html
    sudo rm index.html
    sudo git clone https://github.com/mmhein217/gedportal.git .
    ```
    *(Note: If the repo is private, you will need to generate a Personal Access Token on GitHub and use it as the password)*

2.  **Permissions**:
    Apache needs write access to specific folders.
    ```bash
    sudo chown -R www-data:www-data /var/www/html
    sudo chmod -R 755 /var/www/html
    # Ensure uploads folder exists and is writable
    sudo mkdir -p /var/www/html/uploads
    sudo chown -R www-data:www-data /var/www/html/uploads
    ```

3.  **Configure Application**:
    Copy the sample config (or create one) and edit it.
    ```bash
    cp backend/config.php backend/config.php.bak
    nano backend/config.php
    ```
    **Update with Production Credentials**:
    ```php
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'exam_management');
    define('DB_USER', 'ged_user');
    define('DB_PASS', 'StrongPassword123!');
    
    // Disable error display for production
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ```

4.  **Enable Rewrite Module (for .htaccess)**:
    ```bash
    sudo a2enmod rewrite
    ```
    Edit Apache config to allow overrides:
    ```bash
    sudo nano /etc/apache2/sites-available/000-default.conf
    ```
    Add this block inside `<VirtualHost *:80>`:
    ```apache
    <Directory /var/www/html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    ```

5.  **Restart Apache**:
    ```bash
    sudo systemctl restart apache2
    ```

---

## Phase 6: Final Verification

1.  Visit `http://YOUR_EC2_PUBLIC_IP` in your browser.
2.  Login with default admin credentials (`admin` / `password123`).
3.  Go to **Admin > Settings** to verify the system is running.

**🎉 Deployment Complete!**

---

## Maintenance: How to Update

When you make changes locally and want to update the server:

1.  **Local Machine**:
    ```bash
    git add .
    git commit -m "Description of changes"
    git push origin main
    ```

2.  **EC2 Server**:
    ```bash
    ssh -i "ged-key.pem" ubuntu@YOUR_IP
    cd /var/www/html
    # Only needs to be run once if you see "dubious ownership" error:
    sudo git config --global --add safe.directory /var/www/html
    
    sudo git pull origin main
    ```
    *(Note: If you see "Permission denied", use `sudo git pull`)*
