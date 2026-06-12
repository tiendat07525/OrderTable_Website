<div align="center">

<h1>Golden Spoons</h1>

<p>
  <strong>Hệ thống đặt bàn nhà hàng trực tuyến</strong><br/>
  Online Restaurant Table Booking System built with Laravel 13
</p>

<p>
  <a href="https://github.com/NTTDat7525/GoldenSpoons/blob/main/LICENSE">
    <img src="https://img.shields.io/badge/License-MIT-yellow.svg" alt="License: MIT"/>
  </a>
  <a href="https://laravel.com">
    <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel&logoColor=white" alt="Laravel"/>
  </a>
  <a href="https://www.php.net">
    <img src="https://img.shields.io/badge/PHP-8.3+-777BB4?logo=php&logoColor=white" alt="PHP"/>
  </a>
  <img src="https://img.shields.io/badge/TailwindCSS-4.x-06B6D4?logo=tailwindcss&logoColor=white" alt="TailwindCSS"/>
  <a href="https://tadneit07525.site">
    <img src="https://img.shields.io/badge/Demo-Live-brightgreen?logo=vercel" alt="Live Demo"/>
  </a>
</p>

<p>
  <a href="#-giới-thiệu">Giới thiệu</a> •
  <a href="#-tính-năng">Tính năng</a> •
  <a href="#-công-nghệ-sử-dụng">Công nghệ</a> •
  <a href="#-cài-đặt">Cài đặt</a> •
  <a href="#-cấu-hình">Cấu hình</a> •
  <a href="#-cấu-trúc-dự-án">Cấu trúc</a> •
  <a href="#-đóng-góp">Đóng góp</a> •
  <a href="#-giấy-phép">Giấy phép</a>
</p>

</div>

---

## Giới thiệu

**Golden Spoons** là một ứng dụng web đặt bàn nhà hàng trực tuyến được xây dựng bằng **Laravel 13**, giúp khách hàng dễ dàng đặt bàn, thanh toán online và nhận xác nhận qua email — tất cả trong vài bước đơn giản.

> An online restaurant table booking web application built with **Laravel 13**, enabling customers to reserve tables, pay online, and receive instant email confirmation.

**Live Demo:** [https://tadneit07525.site](https://tadneit07525.site)

---

## Thành viên nhóm
| Họ và tên | Mã sinh viên | Công việc |
|-----------|--------------|-----------|
| Nguyễn Trịnh Tiến Đạt | 23810310148 | Lập trình Backend, thiết kế cơ sở dữ liệu |
| Bùi Minh Đức | 23810310110 | Lập trình Frontend |
---

## Tính năng

### Khách hàng (Customer)

| Tính năng | Mô tả |
|-----------|-------|
| Đăng ký / Đăng nhập | Xác thực tài khoản qua email + OTP |
| Đăng nhập Google | OAuth 2.0 thông qua Laravel Socialite |
| Đặt bàn | Xem và đặt bàn trống theo thời gian thực |
| Thanh toán online | Tích hợp cổng thanh toán SePay |
| Xác nhận email | Nhận email xác nhận sau khi đặt bàn thành công |
| Lịch sử đặt bàn | Xem và hủy các đơn đặt bàn |
| Quản lý tài khoản | Cập nhật thông tin cá nhân và đổi mật khẩu |

### Quản trị (Admin)

| Tính năng | Mô tả |
|-----------|-------|
| Dashboard tổng quan | Thống kê tóm tắt hoạt động nhà hàng |
| Quản lý bàn ăn | Thêm, sửa, xóa bàn; chiếm/giải phóng bàn theo thời gian thực |
| Quản lý đặt bàn | Xem tất cả đơn đặt bàn, lọc theo trạng thái |
| Thống kê doanh thu | Theo dõi doanh thu theo ngày/tháng |
| Xuất báo cáo | Xuất dữ liệu ra file Excel |

---

## Công nghệ sử dụng

| Lớp | Công nghệ |
|-----|-----------|
| **Backend** | [Laravel 13](https://laravel.com) (PHP 8.3+) |
| **Frontend** | [TailwindCSS v4](https://tailwindcss.com), [Vite 8](https://vite.dev), JavaScript |
| **Database** | MySQL |
| **Authentication** | Laravel built-in Auth + [Laravel Socialite](https://laravel.com/docs/socialite) (Google OAuth) |
| **Email** | Laravel Mail (SMTP via Gmail) |
| **Payment** | [SePay](https://sepay.vn) Webhook API |
| **Queue** | Laravel Queue (sync / database) |
| **Export** | [Maatwebsite Excel](https://laravel-excel.com) |
| **Dev Tools** | Laravel Pail, Laravel Pint, PHPUnit |

---

## Cài đặt

### Yêu cầu hệ thống (Prerequisites)

Trước khi bắt đầu, hãy chắc chắn rằng bạn đã cài đặt:

- **PHP** >= 8.3
- **Composer** >= 2.x
- **Node.js** >= 20.x & **npm**
- **MySQL** >= 8.0
- **Git**

### Cài đặt nhanh (Quick Setup)

```bash
# 1. Clone repository
git clone https://github.com/NTTDat7525/GoldenSpoons.git
cd GoldenSpoons

# 2. Chạy script setup tự động (khuyến nghị)
composer run setup
```

> Script `composer run setup` sẽ tự động: cài dependencies, tạo `.env`, generate app key, migrate database, build assets.

---

### Cài đặt thủ công (Manual Setup)

<details>
<summary>Xem hướng dẫn chi tiết</summary>

```bash
# 1. Clone repository
git clone https://github.com/NTTDat7525/GoldenSpoons.git
cd GoldenSpoons

# 2. Cài PHP dependencies
composer install

# 3. Cài Node.js dependencies
npm install

# 4. Sao chép file cấu hình môi trường
cp .env.example .env

# 5. Tạo application key
php artisan key:generate

# 6. Cấu hình biến môi trường trong file .env
# (xem mục Cấu hình bên dưới)

# 7. Tạo bảng và dữ liệu mẫu
php artisan migrate --seed

# 8. Tạo symbolic link cho storage
php artisan storage:link

# 9. Build assets frontend
npm run build

# 10. Chạy server phát triển
php artisan serve

# 11. Tài khoản mẫu
Customer
- Tên đăng nhập: customer
- Mật khẩu: password

Admin
- Tên đăng nhập: admin
- Mật khẩu: password
```

Truy cập ứng dụng tại: **http://localhost:8000**

</details>

### Chạy môi trường phát triển (Development Server)

```bash
# Chạy đồng thời: web server, queue worker, log viewer, vite dev server
composer run dev
```

---

## Cấu hình

Sau khi sao chép `.env.example` thành `.env`, hãy cấu hình các giá trị sau:

### Database

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=golden_spoons
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Email (SMTP Gmail)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password      # Dùng App Password của Google
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@goldenspoons.com"
MAIL_FROM_NAME="Golden Spoons"
```

> Để lấy App Password của Google: Tài khoản Google → Bảo mật → Xác minh 2 bước → Mật khẩu ứng dụng

### Google OAuth (Đăng nhập Google)

```env
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

> Tạo credentials tại [Google Cloud Console](https://console.cloud.google.com/)

### SePay Payment

```env
SEPAY_API_KEY=your_sepay_api_key
SEPAY_WEBHOOK_SECRET=your_webhook_secret
SEPAY_ACCOUNT_NUMBER=your_bank_account
```

> Đăng ký tài khoản tại [SePay](https://sepay.vn) để lấy thông tin API

### Queue (Gửi mail bất đồng bộ)

```env
# Sync: gửi trực tiếp (đơn giản, phù hợp dev)
QUEUE_CONNECTION=sync

# Database: gửi qua queue worker (khuyến nghị cho production)
QUEUE_CONNECTION=database
```

---

## Cấu trúc dự án

```
GoldenSpoons/
├── app/
│   ├── Exports/                  # Excel export classes
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php
│   │   │   ├── AuthController.php      # Đăng ký, đăng nhập, OTP, Google OAuth
│   │   │   ├── BookingController.php   # Đặt bàn, lịch sử, hủy
│   │   │   ├── PaymentController.php   # SePay webhook
│   │   │   ├── ReportController.php    # Báo cáo & xuất Excel
│   │   │   ├── RevenueController.php   # Thống kê doanh thu
│   │   │   ├── TableController.php     # Quản lý bàn ăn
│   │   │   └── UserController.php      # Quản lý người dùng
│   │   └── Middleware/
│   ├── Jobs/
│   │   └── SendPaymentSuccessEmailJob.php
│   ├── Mail/
│   │   ├── ForgotPasswordMail.php
│   │   ├── PaymentSuccessMail.php
│   │   └── SendOtpMail.php
│   └── Models/
│       ├── Booking.php
│       ├── EmailOtp.php
│       ├── Session.php
│       ├── Table.php
│       ├── Transaction.php
│       └── User.php
├── database/
│   ├── migrations/               # Database schema
│   └── seeders/                  # Dữ liệu mẫu
├── resources/
│   ├── css/                      # Tailwind CSS
│   ├── js/                       # JavaScript
│   └── views/
│       ├── admin/                # Giao diện quản trị
│       ├── auth/                 # Đăng nhập, đăng ký, OTP
│       ├── customer/             # Giao diện khách hàng
│       └── emails/               # Email templates
├── routes/
│   ├── api.php
│   └── web.php
├── .env.example                  # Mẫu cấu hình môi trường
├── composer.json
├── package.json
└── vite.config.js
```

## Đóng góp (Contributing)

Chúng tôi hoan nghênh mọi đóng góp từ cộng đồng! Để đóng góp:

1. **Fork** repository này
2. Tạo branch mới cho tính năng của bạn:
   ```bash
   git checkout -b feature/ten-tinh-nang
   ```
3. Commit các thay đổi:
   ```bash
   git commit -m "feat: thêm tính năng XYZ"
   ```
4. Push lên branch của bạn:
   ```bash
   git push origin feature/ten-tinh-nang
   ```
5. Mở **Pull Request** và mô tả chi tiết thay đổi của bạn

### Quy ước commit

Dự án sử dụng [Conventional Commits](https://www.conventionalcommits.org/):

| Tiền tố | Ý nghĩa |
|---------|---------|
| `feat:` | Tính năng mới |
| `fix:` | Sửa lỗi |
| `docs:` | Cập nhật tài liệu |
| `style:` | Thay đổi giao diện/format |
| `refactor:` | Tái cấu trúc code |
| `test:` | Thêm/sửa tests |

> Vui lòng đọc [CONTRIBUTING.md](CONTRIBUTING.md) để biết thêm chi tiết

---

## Báo lỗi (Bug Reports)

Nếu bạn phát hiện lỗi, vui lòng tạo mới 1 Issue trong tab [Issue](https://github.com/NTTDat7525/GoldenSpoons/issues) với đầy đủ thông tin:
- Mô tả lỗi
- Các bước tái hiện
- Kết quả mong đợi vs thực tế
- Phiên bản PHP, Laravel, hệ điều hành

---

## Hình ảnh kết quả
<img width="940" height="535" alt="image" src="https://github.com/user-attachments/assets/60913039-ceae-4402-b16f-76629fcf291e" />

---

<img width="940" height="535" alt="image" src="https://github.com/user-attachments/assets/e2cf0e94-9a24-4801-a1d4-15a0af5634d7" />

---

<img width="940" height="538" alt="image" src="https://github.com/user-attachments/assets/5ea5870e-91c0-43df-8465-3dc7ae0dce91" />

---

<img width="940" height="538" alt="image" src="https://github.com/user-attachments/assets/ca85ac92-3af7-44b2-bacc-229ff357f193" />

---

<img width="940" height="542" alt="image" src="https://github.com/user-attachments/assets/26521b1e-137d-468c-961d-795bb98c27a1" />

---

<img width="940" height="535" alt="image" src="https://github.com/user-attachments/assets/0677cd0c-1def-41b1-b4ce-97d370bd68a3" />

---

<img width="940" height="535" alt="image" src="https://github.com/user-attachments/assets/c9e8b0a4-6df2-4583-a8a5-1b1cf8b9d47a" />

---

<img width="940" height="538" alt="image" src="https://github.com/user-attachments/assets/8b0999f7-81b7-4954-824f-9fa1708eac30" />

---

<img width="940" height="533" alt="image" src="https://github.com/user-attachments/assets/1f30b280-0387-4a88-b268-597179b55f7d" />

---

<img width="940" height="533" alt="image" src="https://github.com/user-attachments/assets/668ba54b-0e7f-4d78-931f-b24d53c7e97c" />

---

<img width="940" height="535" alt="image" src="https://github.com/user-attachments/assets/9541562f-8313-4787-b9cf-dd2fa5baf32d" />

---

<img width="940" height="535" alt="image" src="https://github.com/user-attachments/assets/dd17db5f-b72d-468a-ad60-279b5ac4396e" />

---

<img width="940" height="535" alt="image" src="https://github.com/user-attachments/assets/8644e59b-4eb1-4673-90fe-07c0de6ff0d0" />

---

<img width="940" height="535" alt="image" src="https://github.com/user-attachments/assets/539fc5f8-cdbc-469d-8527-c4dfb8c5822b" />

---

<img width="940" height="535" alt="image" src="https://github.com/user-attachments/assets/cf5b90ce-c96c-4b92-91f9-d443bd0990e2" />

---
<img width="940" height="535" alt="image" src="https://github.com/user-attachments/assets/699e7946-c9bd-4790-bfb0-93f007aebcb2" />

---

## Demo Video

[▶ Xem video demo tại đây](https://drive.google.com/file/d/1E7tyBMrUIuW2h8yrWYFDmw8T5ZqFwXBp/view?usp=sharing)

---

## Giấy phép (License)

Dự án được phân phối theo giấy phép **MIT**. Xem file [LICENSE](LICENSE) để biết thêm chi tiết.

---

<div align="center">

Made with using [Laravel](https://laravel.com)

Nếu dự án này hữu ích, hãy để lại một Star!

</div>
