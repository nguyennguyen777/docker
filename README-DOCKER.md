# Hướng dẫn chạy ứng dụng với Docker

## Yêu cầu hệ thống
- Docker
- Docker Compose

## Cài đặt và chạy

### 1. Tạo file .env
```bash
cp env.example .env
```

Sau đó chỉnh sửa file `.env` theo nhu cầu:
```env
# Database Configuration
DB_HOST=web-mysql
DB_USERNAME=root
DB_PASSWORD=root123
DB_DATABASE=app_web1
DB_PORT=3306

# Redis Configuration
REDIS_HOST=web-redis
REDIS_PORT=6379

# Application Configuration
APP_ENV=local
```

### 2. Chạy ứng dụng
```bash
# Build và khởi động tất cả services
docker-compose up -d --build

# Xem logs
docker-compose logs -f
```

### 3. Truy cập ứng dụng
- **Ứng dụng chính**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8082
- **Redis**: localhost:6379

### 4. Khởi tạo database (tùy chọn)
```bash
# Chạy script khởi tạo database
docker-compose exec web-backend bash /init-db.sh
```

## Các tính năng được hỗ trợ

### LocalStorage
- Truy cập: http://localhost:8080/localstorage_demo.html
- Lưu trữ dữ liệu trên trình duyệt
- Hỗ trợ set/get/remove/clear

### Redis
- Truy cập: http://localhost:8080/redis_demo.php
- Lưu trữ dữ liệu trên server
- Hỗ trợ set/get operations
- Dữ liệu được lưu trữ persistent

### Database
- MySQL 5.7.43
- phpMyAdmin để quản lý database
- Dữ liệu được lưu trữ persistent

## Quản lý containers

### Dừng ứng dụng
```bash
docker-compose down
```

### Dừng và xóa volumes (xóa dữ liệu)
```bash
docker-compose down -v
```

### Xem logs của service cụ thể
```bash
docker-compose logs web-backend
docker-compose logs web-mysql
docker-compose logs web-redis
```

### Truy cập container
```bash
# Truy cập backend container
docker-compose exec web-backend bash

# Truy cập MySQL
docker-compose exec web-mysql mysql -u root -p

# Truy cập Redis
docker-compose exec web-redis redis-cli
```

## Cấu trúc dự án
```
docker/
├── config/
│   ├── backend/
│   │   ├── Dockerfile
│   │   ├── php.ini
│   │   └── start
│   ├── mysql/
│   │   └── my.cnf
│   └── httpd_security.conf
├── training-php/
│   ├── api_*.php          # API endpoints
│   ├── configs/
│   │   ├── database.php    # Database config
│   │   └── hacker.php
│   ├── database/           # SQL files
│   ├── models/             # Model classes
│   ├── utils/              # Utility classes
│   ├── views/              # View templates
│   ├── public/             # Static assets
│   ├── localstorage_demo.html  # LocalStorage demo
│   └── redis_demo.php      # Redis demo
├── docker-compose.yml
├── init-db.sh
└── env.example
```

## Troubleshooting

### Lỗi kết nối database
- Kiểm tra MySQL container đã chạy: `docker-compose ps`
- Kiểm tra logs: `docker-compose logs web-mysql`

### Lỗi kết nối Redis
- Kiểm tra Redis container: `docker-compose logs web-redis`
- Test kết nối: `docker-compose exec web-redis redis-cli ping`

### Lỗi permissions
- Đảm bảo file `init-db.sh` có quyền execute: `chmod +x init-db.sh`

