# WordPress/WooCommerce Development Environment

This repository provides a hardened, reusable WordPress/WooCommerce development environment using Docker Compose.

## Features

- WordPress latest version
- WooCommerce latest version pre-installed
- MySQL 8.0 database
- WP-CLI for command-line management
- Environment variables configured via `.env` file
- Ready for theme and plugin development

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop) (with Docker Compose)
- Git (for cloning this repository)

## Quick Start

1. Clone this repository:
   ```bash
   git clone <repository-url>
   cd <repository-directory>
   ```

2. Start the environment:
   ```bash
   ./setup-wordpress.sh
   ```
   Or manually:
   ```bash
   docker compose up -d
   ```

3. Wait for initialization (about 30-60 seconds), then visit:
   - Site: http://localhost:8080
   - WordPress Admin: http://localhost:8080/wp-admin
   - Username: admin
   - Password: admin123

## Environment Variables

Edit the `.env` file to customize your setup:

```env
PROJECT_NAME=wp-dev          # Docker project name prefix
WORDPRESS_PORT=8080          # Port for WordPress site
PHPMYADMIN_PORT=8081         # Port for phpMyAdmin (if added)
MAILHOG_UI_PORT=8025         # Port for MailHog (if added)

MYSQL_DATABASE=wordpress     # Database name
MYSQL_USER=wordpress         # Database user
MYSQL_PASSWORD=wordpress     # Database password
MYSQL_ROOT_PASSWORD=root     # Database root password

WORDPRESS_DEBUG=true         # Enable WordPress debugging
WP_DEBUG=true                # Enable WP_DEBUG constant
```

## WP-CLI Usage

To run WP-CLI commands:

```bash
docker compose exec wordpress php wp-cli.phar [command] --allow-root
```

Examples:
- List plugins: `docker compose exec wordpress php wp-cli.phar plugin list --allow-root`
- Update WordPress: `docker compose exec wordpress php wp-cli.phar core update --allow-root`
- Install a plugin: `docker compose exec wordpress php wp-cli.phar plugin install plugin-slug --allow-root`
- Activate a theme: `docker compose exec wordpress php wp-cli.phar theme activate theme-slug --allow-root`

## Database Access

To access the MySQL database directly:

```bash
docker compose exec db mysql -u$MYSQL_USER -p$MYSQL_PASSWORD $MYSQL_DATABASE
```

Or find the password in your `.env` file.

## Stopping and Starting

- Stop containers: `docker compose down`
- Start containers: `docker compose up -d`
- Restart containers: `docker compose restart`
- View logs: `docker compose logs -f`

## Development Workflow

1. **Theme Development**: Place your themes in `wp-content/themes/`
2. **Plugin Development**: Place your plugins in `wp-content/plugins/`
3. **Uploads**: Media uploads go to `wp-content/uploads/`
4. **Configuration**: Modify `wp-config.php` as needed (though most settings are handled via environment)

## File Structure

```
.
├── docker-compose.yml          # Docker Compose configuration
├── .env                        # Environment variables
├── setup-wordpress.sh          # Setup script (Windows compatible)
├── wp-content/                 # WordPress content directory (mounted volume)
│   ├── themes/                 # Your themes go here
│   ├── plugins/                # Your plugins go here
│   └── uploads/                # Media uploads
├── wp-config.php               # WordPress configuration (generated)
└── wp-config-docker.php        # Docker-specific WordPress config
```

## Customization

### Adding phpMyAdmin

To add phpMyAdmin for database management, add this to your `docker-compose.yml`:

```yaml
  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    container_name: ${PROJECT_NAME}-phpmyadmin
    restart: unless-stopped
    ports:
      - "${PHPMYADMIN_PORT}:80"
    environment:
      - PMA_HOST=db
      - MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
    networks:
      - wpnet
```

### Adding MailHog for Email Testing

To add MailHog for catching and viewing emails sent by WordPress:

```yaml
  mailhog:
    image: mailhog/mailhog
    container_name: ${PROJECT_NAME}-mailhog
    restart: unless-stopped
    ports:
      - "${MAILHOG_UI_PORT}:8025"
    networks:
      - wpnet
```

Then configure WordPress to use MailHog by adding to your `wp-config.php`:
```php
if (isset($_SERVER['MAILHOG_HOST'])) {
    define('WP_SMTP_HOST', $_SERVER['MAILHOG_HOST']);
    define('WP_SMTP_PORT', 1025);
}
```

## Maintenance

### Updating WordPress and WooCommerce

```bash
# Update WordPress core
docker compose exec wordpress php wp-cli.phar core update --allow-root

# Update WooCommerce
docker compose exec wordpress php wp-cli.phar plugin update woocommerce --allow-root

# Update all plugins
docker compose exec wordpress php wp-cli.phar plugin update --all --allow-root

# Update all themes
docker compose exec wordpress php wp-cli.phar theme update --all --allow-root
```

### Backing Up

To backup your WordPress site:

```bash
# Backup database
docker compose exec db mysqldump -u$MYSQL_USER -p$MYSQL_PASSWORD $MYSQL_DATABASE > backup.sql

# Backup wp-content
docker compose run --rm wordpress tar -czf wp-content-backup.tar.gz -C /var/www/html wp-content
```

## Troubleshooting

### Common Issues

1. **"docker" command not found**
   - Make sure Docker Desktop is running
   - Verify Docker is installed and in your PATH
   - Try restarting your terminal after Docker starts

2. **Connection refused to database**
   - Wait a bit longer for MySQL to initialize
   - Check container status with `docker compose ps`
   - View logs with `docker compose logs db`

3. **WordPress not loading**
   - Check WordPress container logs: `docker compose logs wordpress`
   - Verify port mapping in `.env` and `docker-compose.yml`
   - Try accessing via `http://localhost:8080` (adjust port if changed)

4. **Permission issues with wp-content**
   - The Docker container runs as www-data user
   - Files in wp-content should be writable by this user
   - If you see permission errors, you may need to adjust file permissions

## Security Notes

- This environment is for **development only**
- Do not use these credentials in production
- The admin credentials are intentionally simple for development
- Never expose this environment to the public internet without proper security measures

## License

MIT