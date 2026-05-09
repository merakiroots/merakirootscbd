#!/bin/bash
# WordPress/WooCommerce Development Environment Setup Script

echo "Setting up WordPress/WooCommerce development environment..."

# Start Docker containers
echo "Starting Docker containers..."
"C:\Program Files\Docker\Docker\resources\bin\docker.exe" compose up -d

# Wait for WordPress to be ready
echo "Waiting for WordPress to initialize..."
sleep 10

# Install WordPress core
echo "Installing WordPress core..."
"C:\Program Files\Docker\Docker\resources\bin\docker.exe" compose exec wordpress php wp-cli.phar core install \
  --url="http://localhost:8080" \
  --title="Meraki Roots CBD" \
  --admin_user=admin \
  --admin_password=admin123 \
  --admin_email=admin@example.com \
  --allow-root

# Download and install WooCommerce
echo "Downloading WooCommerce..."
"C:\Program Files\Docker\Docker\resources\bin\docker.exe" compose exec wordpress curl -LO https://downloads.wordpress.org/plugin/woocommerce.10.7.0.zip

echo "Installing WooCommerce..."
"C:\Program Files\Docker\Docker\resources\bin\docker.exe" compose exec wordpress php wp-cli.phar plugin install woocommerce.10.7.0.zip --activate --allow-root

# Clean up WooCommerce zip file
echo "Cleaning up..."
"C:\Program Files\Docker\Docker\resources\bin\docker.exe" compose exec wordpress rm wp-content/plugins/woocommerce.10.7.0.zip

echo "WordPress/WooCommerce setup complete!"
echo "Access your site at: http://localhost:8080"
echo "WordPress Dashboard: http://localhost:8080/wp-admin"
echo "Username: admin"
echo "Password: admin123"

# Display useful commands
echo ""
echo "Useful commands:"
echo "  View logs: docker compose logs -f"
echo "  Stop containers: docker compose down"
echo "  Start containers: docker compose up -d"
echo "  Access WP-CLI: docker compose exec wordpress php wp-cli.phar [command] --allow-root"
echo "  Access database: docker compose exec db mysql -u\${MYSQL_USER} -p\${MYSQL_PASSWORD} \${MYSQL_DATABASE}"