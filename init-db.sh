#!/bin/bash

# Script to initialize database with sample data
echo "Initializing database..."

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
while ! mysqladmin ping -h"web-mysql" -P"3306" -u"root" -p"${DB_PASSWORD}" --silent; do
    echo "Waiting for MySQL..."
    sleep 2
done

echo "MySQL is ready!"

# Import database schema and data
echo "Importing database schema..."
mysql -h"web-mysql" -P"3306" -u"root" -p"${DB_PASSWORD}" "${DB_DATABASE}" < /var/www/html/database/app_web1.sql

echo "Database initialization completed!"

