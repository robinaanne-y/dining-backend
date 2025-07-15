
# 🍽️ Dining App Backend

This is a Laravel-powered backend API for a dining app. Customers can scan QR codes, browse menus, and place orders. Restaurant admins can manage menus, tables, and track orders through a dashboard.

---

## 📌 Features

- API endpoints for menu browsing, order creation, and status updates
- Admin features for managing:
  - Menu items (CRUD)
  - Restaurant tables and QR codes
  - Customer orders and fulfillment
- Role-based authentication using Laravel Sanctum
- Test-driven development with PHPUnit
- Clean and modular code structure following Laravel best practices

---

## ⚙️ Tech Stack

- **Backend Framework:** Laravel 11
- **Authentication:** Laravel Sanctum
- **Database:** MySQL / PostgreSQL
- **Testing:** PHPUnit
- **QR Code Generation:** simple-qrcode package
- **API Format:** RESTful JSON

---


## 🚀 Getting Started

### Clone the repository
```bash
git clone https://github.com/yourusername/dining-app-backend.git
cd dining-app-backend
```

### Install dependencies
```bash
composer install
```

### Copy and configure environment file
```bash
cp .env.example .env
# Edit .env to set your database and other environment variables
```

### Generate application key
```bash
php artisan key:generate
```

### Update .env with local DB config:
```env
DB_CONNECTION=mysql
DB_DATABASE=dining_app
DB_USERNAME=root
DB_PASSWORD=
```

### Run migrations
```bash
php artisan migrate
```

### (Optional) Seed the database
```bash
php artisan db:seed
```

### Start the development server
```bash
php artisan serve
```


---


## 🔑 Authentication
This project uses Laravel Sanctum for token-based authentication.
For API testing (e.g., in Postman), use login endpoints to retrieve tokens and attach them as Authorization: Bearer {token} in headers.


---


## 🔌 API Endpoints
| Endpoint                        | Method | Description                  |
| ------------------------------- | ------ | ---------------------------- |
| `/api/login`                    | POST   | User login                   |
| `/api/logout`                   | POST   | User logout                  |
| `/api/menu-items`               | GET    | Get available menu items     |
| `/api/orders`                   | POST   | Place a new order            |
| `/api/orders/{id}`              | GET    | View order details           |
| `/api/admin/orders/{id}/status` | PUT    | Update order status (admin)  |
| `/api/admin/tables`             | GET    | List restaurant tables       |
| `/api/admin/menu-items`         | POST   | Create new menu item (admin) |

---


## ✨ Author
Robina Anne Yuson
📧 robinaanne93@gmail.com
🔗 linkedin.com/in/robinaanneyuson