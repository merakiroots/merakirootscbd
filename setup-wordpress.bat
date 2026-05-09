@echo off
REM WordPress/WooCommerce Development Environment Setup Script for Windows

echo Setting up WordPress/WooCommerce development environment...

REM Set temporary Docker config directory to avoid credential issues
set DOCKER_CONFIG=%TEMP%\docker
if not exist "%DOCKER_CONFIG%" (
    mkdir "%DOCKER_CONFIG%"
    echo {} > "%DOCKER_CONFIG%\config.json"
)

REM Start Docker containers
echo Starting Docker containers...
"C:\Program Files\Docker\Docker\resources\bin\docker.exe" compose up -d

REM Wait for WordPress to be ready
echo Waiting for WordPress to initialize...
ping -n 15 127.0.0.1 >nul

REM Install WordPress core
echo Installing WordPress core...
"C:\Program Files\Docker\Docker\resources\bin\docker.exe" compose exec wordpress php wp-cli.phar core install --url="http://localhost:8080" --title="Meraki Roots CBD" --admin_user=admin --admin_password=admin123 --admin_email=admin@example.com --allow-root
if errorlevel 1 (
    echo WordPress installation failed. Checking status...
    "C:\Program Files\Docker\Docker\resources\bin\docker.exe" compose logs wordpress
    goto :eof
)

REM Remove existing WooCommerce plugin to ensure clean install
echo Removing existing WooCommerce plugin...
"C:\Program Files\Docker\Docker\resources\bin\docker.exe" compose exec wordpress rm -rf /var/www/html/wp-content/plugins/woocommerce

REM Download and install WooCommerce
echo Downloading WooCommerce...
"C:\Program Files\Docker\Docker\resources\bin\docker.exe" compose exec wordpress curl -LO https://downloads.wordpress.org/plugin/woocommerce.10.7.0.zip
if errorlevel 1 (
    echo Failed to download WooCommerce. Checking connectivity...
    "C:\Program Files\Docker\Docker\resources\bin\docker.exe" compose exec wordpress curl -I https://wordpress.org
    goto :eof
)

echo Installing WooCommerce...
"C:\Program Files\Docker\Docker\resources\bin\docker.exe" compose exec wordpress php wp-cli.phar plugin install woocommerce.10.7.0.zip --activate --allow-root
if errorlevel 1 (
    echo WooCommerce installation failed.
    goto :eof
)

REM Clean up WooCommerce zip file
echo Cleaning up...
"C:\Program Files\Docker\Docker\resources\bin\docker.exe" compose exec wordpress rm wp-content/plugins/woocommerce.10.7.0.zip

echo.
echo WordPress/WooCommerce setup complete!
echo Access your site at: http://localhost:8080
echo WordPress Dashboard: http://localhost:8080/wp-admin
echo Username: admin
echo Password: admin123
echo.
echo Useful commands:
echo   View logs: docker compose logs -f
echo   Stop containers: docker compose down
echo   Start containers: docker compose up -d
echo   Access WP-CLI: docker compose exec wordpress php wp-cli.phar [command] --allow-root
echo   Access database: docker compose exec db mysql -u%MYSQL_USER% -p%MYSQL_PASSWORD% %MYSQL_DATABASE%