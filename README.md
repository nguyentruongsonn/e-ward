# HỆ THỐNG ĐẶT LỊCH & THEO DÕI TIẾN ĐỘ XỬ LÝ HỒ SƠ TRUNG TÂM HÀNH CHÍNH CÔNG CỦA PHƯỜNG

🔗 **Demo hệ thống:**
[https://ewardabc.online/](https://ewardabc.online/)

---

## 1. Tổng quan dự án

Hệ thống được xây dựng nhằm **số hóa quy trình tiếp nhận và xử lý hồ sơ hành chính** tại Trung tâm Hành chính công cấp phường.

Dự án tập trung vào:

* Chuẩn hóa quy trình xử lý 3 cấp
* Giảm tải cho bộ phận một cửa
* Minh bạch tiến độ xử lý hồ sơ
* Ứng dụng AI hỗ trợ tư vấn thủ tục

Hệ thống được phát triển theo mô hình **Web-based Application** với kiến trúc tách biệt Backend – Frontend.

---

## 2. Mục tiêu hệ thống

* Cung cấp nền tảng đặt lịch trực tuyến cho người dân
* Tự động hóa quy trình tiếp nhận – xử lý – phê duyệt hồ sơ
* Theo dõi trạng thái xử lý theo thời gian thực
* Hỗ trợ tư vấn thủ tục hành chính bằng AI
* Tạo báo cáo thống kê phục vụ quản lý

---

##  3. Đối tượng sử dụng

### 🔹 Người dân

* Đăng ký / đăng nhập
* Đặt lịch nộp hồ sơ
* Nộp hồ sơ trực tuyến
* Theo dõi trạng thái xử lý
* Nhận thông báo email
* Đánh giá dịch vụ

### 🔹 Cán bộ một cửa

* Tiếp nhận hồ sơ
* Kiểm tra tính hợp lệ
* Chuyển hồ sơ cho cán bộ thụ lý

### 🔹 Cán bộ thụ lý

* Xử lý hồ sơ
* Yêu cầu bổ sung
* Trình lãnh đạo phê duyệt

### 🔹 Lãnh đạo

* Phê duyệt / từ chối hồ sơ

### 🔹 Quản trị viên

* Quản lý người dùng
* Quản lý danh mục thủ tục
* Thống kê & báo cáo

---

## 4. Chức năng chính

### 4.1 Quản lý tài khoản & phân quyền

* Hệ thống phân quyền theo vai trò (RBAC)
* Middleware kiểm soát truy cập

### 4.2 Quản lý thủ tục hành chính

* Danh mục thủ tục
* Hồ sơ yêu cầu
* Thời gian xử lý

### 4.3 Đặt lịch & Check-in QR

* Chọn ngày giờ linh hoạt
* Sinh mã QR xác nhận lịch hẹn
* Kiểm soát số lượng theo khung giờ

### 4.4 Nộp hồ sơ trực tuyến

* Upload tài liệu
* Theo dõi trạng thái
* Email Notification

### 4.5 Quy trình xử lý 3 cấp

Người dân → Một cửa → Thụ lý → Lãnh đạo → Hoàn tất

### 4.6 Chatbot AI (Gemini Integration)

* Trả lời câu hỏi về thủ tục hành chính
* Hướng dẫn quy trình
* Hỗ trợ tra cứu nhanh

### 4.7 Báo cáo & thống kê

* Số lượng hồ sơ theo thời gian
* Tỷ lệ xử lý đúng hạn
* Biểu đồ trực quan

---

## 5. Kiến trúc hệ thống

### 🔹 Backend

* Laravel 10
* RESTful API
* Eloquent ORM

### 🔹 Frontend

* Blade Template Engine
* Bootstrap / TailwindCSS
* AJAX

### 🔹 Database

* MySQL

### 🔹 Cache & Performance

* Redis Cache

### 🔹 AI Integration

* Google Gemini API

---
