<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OutstandingServiceController;
use App\Http\Controllers\LichHenController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SubmitController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\PaymentController;

// ...existing code...


Route::get('/outstanding-service', [OutstandingServiceController::class, 'index'])->name('outstanding-service');
Route::get('/outstanding-service/{id}', [OutstandingServiceController::class, 'show'])->name('outstanding-service.show');

Route::view('/', 'pages.home')->name('home');

// Webhook để nhận email reply từ công dân (không cần auth)
Route::post('/webhook/email-reply', [AdminController::class, 'receiveMailReply'])->name('webhook.email-reply');
Route::view('/about', 'pages.about')->name('about');

Route::view('/contact', 'pages.contact')->name('contact');
Route::get('/danh-gia-dich-vu', [App\Http\Controllers\PublicServiceController::class, 'ratings'])->name('service.ratings');
Route::get('/danh-gia-dich-vu/{maTTHC}', [App\Http\Controllers\PublicServiceController::class, 'showProcedureRatings'])->name('service.ratings.detail');

// Public tracking routes
Route::get('/tra-cuu-ho-so', [App\Http\Controllers\PublicController::class, 'trackingForm'])->name('tracking');
Route::post('/tra-cuu-ho-so', [App\Http\Controllers\PublicController::class, 'trackingSearch'])->name('tracking.search');

Route::view('/chatbot', 'pages.chatbot')->name('chatbot');
Route::view('/history', 'pages.history')->name('history');
Route::view('/register', 'pages.register')->name('register');
Route::post('/register', [RegisterController::class, 'submit'])->name('register.submit');
Route::get('/verify-otp', [RegisterController::class, 'showOtpForm'])->name('register.otp.show');
Route::post('/verify-otp', [RegisterController::class, 'verifyOtp'])->name('register.otp.verify');
Route::post('/resend-otp', [RegisterController::class, 'resendOtp'])->name('register.otp.resend');

// Login routes
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Profile routes (requires authentication)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/services/load-more', [ProfileController::class, 'loadMoreServices'])
        ->name('profile.services.load-more');

    // Appointments
    Route::get('/profile/appointments', [ProfileController::class, 'appointments'])->name('profile.appointments');
    Route::get('/profile/appointments/load-more', [ProfileController::class, 'loadMoreAppointments'])
        ->name('profile.appointments.load-more');
    Route::get('/profile/appointments/{id}', [ProfileController::class, 'showAppointment'])
        ->name('profile.appointments.show');
    Route::post('/profile/appointments/{id}/cancel', [ProfileController::class, 'cancelAppointment'])
        ->name('profile.appointments.cancel');

    Route::get('/profile/identity', [ProfileController::class, 'identityInfo'])->name('profile.identity');

    // Ho so (hoso xuly) detail API for modal
    Route::get('/profile/hoso/{maHSXL}', [ProfileController::class, 'showHoSo'])
        ->name('profile.hoso.show');

    // Payment history
    Route::get('/profile/payments', [ProfileController::class, 'payments'])
        ->name('profile.payments');
    Route::get('/profile/payments/{id}/invoice', [ProfileController::class, 'paymentInvoice'])
        ->name('profile.payments.invoice');

    // Notifications
    Route::get('/profile/notifications', [ProfileController::class, 'notifications'])
        ->name('profile.notifications');
    Route::get('/profile/notifications/load-more', [ProfileController::class, 'loadMoreNotifications'])
        ->name('profile.notifications.load-more');
    Route::post('/profile/notifications/{id}/mark-read', [ProfileController::class, 'markNotificationAsRead'])
        ->name('profile.notifications.mark-read');
    Route::post('/profile/notifications/{id}/detail', [ProfileController::class, 'getNotificationDetail'])
        ->name('profile.notifications.detail');

    // Password change
    Route::get('/profile/password-change', [ProfileController::class, 'showPasswordChangeForm'])
        ->name('profile.password-change');
    Route::post('/profile/password-change', [ProfileController::class, 'requestPasswordChange'])
        ->name('profile.password-change.request');
    Route::get('/profile/password-change/verify', [ProfileController::class, 'showVerifyOtpForm'])
        ->name('profile.password-change.verify');
    Route::post('/profile/password-change/verify', [ProfileController::class, 'verifyPasswordChangeOtp'])
        ->name('profile.password-change.verify.submit');
    Route::post('/profile/password-change/resend-otp', [ProfileController::class, 'resendPasswordChangeOtp'])
        ->name('profile.password-change.resend-otp');
    
    // Document supplement upload
    Route::post('/profile/application/{maHSXL}/upload-supplement', [ProfileController::class, 'uploadSupplementDocuments'])
        ->name('profile.application.upload-supplement');
    
    // Service rating
    Route::post('/profile/application/{maHSXL}/rate', [ProfileController::class, 'rateService'])
        ->name('profile.application.rate');

    // Ratings history
    Route::get('/profile/ratings', [ProfileController::class, 'showRatings'])->name('profile.ratings');
});

// Dev-only: quick mail test endpoint
if (app()->environment('local')) {
    Route::get('/_mail-test', function() {
        $to = request('to');
        if (!$to) {
            return 'Thiếu ?to=email@example.com';
        }
        try {
            Mail::to($to)->send(new \App\Mail\OtpCodeMail('123456'));
            return 'Đã gửi mail test tới ' . $to;
        } catch (\Throwable $e) {
            return 'Gửi mail thất bại: ' . $e->getMessage();
        }
    });
}
Route::view('/404', 'pages.404')->name('404');

// Admin routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login.submit');
    
    // Admin routes cần authentication và middleware admin
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
        
        // Quản lý hồ sơ
        Route::get('/hosoxuly', [AdminController::class, 'indexHoSo'])->name('admin.hosoxuly.index');
        Route::get('/hosoxuly/nhan-truc-tiep', [AdminController::class, 'indexHoSoNhanTrucTiep'])->name('admin.hosoxuly.nhan-truc-tiep');
        Route::get('/hosoxuly/all', [AdminController::class, 'indexAllHoSo'])->name('admin.hosoxuly.all');
        Route::get('/hosoxuly/{maHSXL}', [AdminController::class, 'showHoSo'])->name('admin.hosoxuly.show');
        Route::get('/hosoxuly/{maHSXL}/confirmation', [AdminController::class, 'printConfirmation'])->name('admin.hosoxuly.confirmation');
        Route::get('/hosoxuly/{maHSXL}/leader-transfer', [AdminController::class, 'printLeaderTransfer'])->name('admin.hosoxuly.leader-transfer');
        Route::post('/hosoxuly/{maHSXL}/trangthai', [AdminController::class, 'updateTrangThai'])->name('admin.hosoxuly.updateTrangThai');
        Route::post('/hosoxuly/{maHSXL}/send-mail', [AdminController::class, 'sendMailHoSo'])->name('admin.hosoxuly.send-mail');
        Route::post('/hosoxuly/{maHSXL}/add-mail-reply', [AdminController::class, 'addMailReply'])->name('admin.hosoxuly.add-mail-reply');
        
        // Filtered lists
        Route::get('/hosoxuly-tiepnhan', [AdminController::class, 'indexHoSoTiepNhan'])->name('admin.hosoxuly.tiepnhan');
        Route::get('/hosoxuly-cho-xuly', [AdminController::class, 'indexHoSoChoXuLy'])->name('admin.hosoxuly.cho-xuly');
        Route::get('/hosoxuly-yeu-cau-bo-sung', [AdminController::class, 'indexHoSoYeuCauBoSung'])->name('admin.hosoxuly.danh-sach-yeu-cau-bo-sung');
        Route::get('/hosoxuly-da-xu-ly-xong', [AdminController::class, 'indexHoSoDaXuLyXong'])->name('admin.hosoxuly.da-xu-ly-xong');
        Route::get('/hosoxuly-da-tra-ket-qua', [AdminController::class, 'indexHoSoDaTraKetQua'])->name('admin.hosoxuly.da-tra-ket-qua');
        Route::get('/hosoxuly-tat-ca', [AdminController::class, 'indexAllHoSo'])->name('admin.hosoxuly.all');
        
        // Workflow actions
        Route::post('/hosoxuly/{maHSXL}/tiepnhan', [AdminController::class, 'tiepNhanHoSo'])->name('admin.hosoxuly.tiepnhan-action');
        Route::post('/hosoxuly/{maHSXL}/chuyen-thuly', [AdminController::class, 'chuyenThuLy'])->name('admin.hosoxuly.chuyen-thuly');
        Route::post('/hosoxuly/{maHSXL}/chuyen-lanhdao', [AdminController::class, 'chuyenLanhDao'])->name('admin.hosoxuly.chuyen-lanhdao');
        Route::post('/hosoxuly/{maHSXL}/pheduyet', [AdminController::class, 'pheDuyet'])->name('admin.hosoxuly.pheduyet');
        Route::get('/hosoxuly/{maHSXL}/leader-approval', [AdminController::class, 'printLeaderApproval'])->name('admin.hosoxuly.leader-approval');
        Route::post('/hosoxuly/{maHSXL}/tralai', [AdminController::class, 'traLai'])->name('admin.hosoxuly.tralai');
        Route::post('/hosoxuly/{maHSXL}/tra-ketqua', [AdminController::class, 'traKetQua'])->name('admin.hosoxuly.tra-ketqua');
        Route::post('/hosoxuly/{maHSXL}/y-kien-xu-ly', [AdminController::class, 'yKienXuLy'])->name('admin.hosoxuly.y-kien-xu-ly');
        Route::post('/hosoxuly/{maHSXL}/upload-ykien', [AdminController::class, 'uploadYKien'])->name('admin.hosoxuly.upload-ykien');
        Route::post('/hosoxuly/{maHSXL}/save-ykien', [AdminController::class, 'saveYKien'])->name('admin.hosoxuly.save-ykien');
        Route::post('/hosoxuly/{maHSXL}/ket-qua-xu-ly', [AdminController::class, 'ketQuaXuLy'])->name('admin.hosoxuly.ket-qua-xu-ly');
        Route::post('/hosoxuly/{maHSXL}/convert-to-result', [AdminController::class, 'convertToResult'])->name('admin.hosoxuly.convert-to-result');
        Route::post('/hosoxuly/{maHSXL}/remove-ykien-file', [AdminController::class, 'removeYKienFile'])->name('admin.hosoxuly.remove-ykien-file');
        Route::post('/hosoxuly/{maHSXL}/remove-ketqua-file', [AdminController::class, 'removeKetQuaFile'])->name('admin.hosoxuly.remove-ketqua-file');
        
        // New workflow features
        Route::post('/hosoxuly/{maHSXL}/sign-file', [AdminController::class, 'signFile'])->name('admin.hosoxuly.sign-file');
        Route::post('/hosoxuly/{maHSXL}/yeu-cau-xu-ly-lai', [AdminController::class, 'yeuCauXuLyLai'])->name('admin.hosoxuly.yeu-cau-xu-ly-lai');
        Route::post('/hosoxuly/{maHSXL}/yeu-cau-bo-sung', [AdminController::class, 'yeuCauBoSung'])->name('admin.hosoxuly.yeu-cau-bo-sung');
        
        // Quản lý lịch hẹn
        Route::get('/appointment', [AdminController::class, 'indexAppointments'])->name('admin.appointment.index');
        Route::get('/appointment/today', [AdminController::class, 'todayAppointments'])->name('admin.appointment.today');
        Route::get('/appointment/scan', [AdminController::class, 'showScanQR'])->name('admin.appointment.scan');
        Route::post('/appointment/checkin/{token}', [AdminController::class, 'processCheckin'])->name('admin.appointment.checkin');
        Route::post('/appointment/send-reminder', [AdminController::class, 'sendReminder'])->name('admin.appointment.send-reminder');
        
        // Quản lý người dùng
        Route::get('/users/congdan', [AdminController::class, 'indexCongDan'])->name('admin.users.congdan');
        Route::get('/users/congdan/create', [AdminController::class, 'createCongDan'])->name('admin.users.congdan.create');
        Route::post('/users/congdan/store', [AdminController::class, 'storeCongDan'])->name('admin.users.congdan.store');
        Route::get('/users/congdan/{id}', [AdminController::class, 'showCongDan'])->name('admin.users.congdan.show');
        Route::get('/users/congdan/{id}/edit', [AdminController::class, 'editCongDan'])->name('admin.users.congdan.edit');
        Route::post('/users/congdan/{id}/update', [AdminController::class, 'updateCongDan'])->name('admin.users.congdan.update');
        Route::post('/users/congdan/{id}/delete', [AdminController::class, 'destroyCongDan'])->name('admin.users.congdan.destroy');

        Route::get('/users/canbo', [AdminController::class, 'indexCanBo'])->name('admin.users.canbo');
        Route::get('/users/canbo/create', [AdminController::class, 'createCanBo'])->name('admin.users.canbo.create');
        Route::post('/users/canbo/store', [AdminController::class, 'storeCanBo'])->name('admin.users.canbo.store');
        Route::get('/users/canbo/{id}', [AdminController::class, 'showCanBo'])->name('admin.users.canbo.show');
        Route::get('/users/canbo/{id}/edit', [AdminController::class, 'editCanBo'])->name('admin.users.canbo.edit');
        Route::post('/users/canbo/{id}/update', [AdminController::class, 'updateCanBo'])->name('admin.users.canbo.update');
        Route::post('/users/canbo/{id}/delete', [AdminController::class, 'destroyCanBo'])->name('admin.users.canbo.destroy');
        
        // Quản lý thủ tục hành chính
        Route::prefix('tthc')->group(function () {
            // TTHC
            Route::get('/', [AdminController::class, 'indexTTHC'])->name('admin.tthc.index');
            Route::get('/create', [AdminController::class, 'createTTHC'])->name('admin.tthc.create');
            Route::post('/store', [AdminController::class, 'storeTTHC'])->name('admin.tthc.store');
            Route::get('/{id}/edit', [AdminController::class, 'editTTHC'])->name('admin.tthc.edit');
            Route::post('/{id}/update', [AdminController::class, 'updateTTHC'])->name('admin.tthc.update');
            Route::post('/{id}/delete', [AdminController::class, 'destroyTTHC'])->name('admin.tthc.destroy');
            
            // Lĩnh vực
            Route::prefix('linhvuc')->group(function () {
                Route::get('/', [AdminController::class, 'indexLinhVuc'])->name('admin.tthc.linhvuc.index');
                Route::get('/create', [AdminController::class, 'createLinhVuc'])->name('admin.tthc.linhvuc.create');
                Route::post('/store', [AdminController::class, 'storeLinhVuc'])->name('admin.tthc.linhvuc.store');
                Route::get('/{id}/edit', [AdminController::class, 'editLinhVuc'])->name('admin.tthc.linhvuc.edit');
                Route::post('/{id}/update', [AdminController::class, 'updateLinhVuc'])->name('admin.tthc.linhvuc.update');
                Route::post('/{id}/delete', [AdminController::class, 'destroyLinhVuc'])->name('admin.tthc.linhvuc.destroy');
            });
        });
        
        // Quản lý thanh toán
        Route::get('/payment/history', [AdminController::class, 'indexPaymentHistory'])->name('admin.payment.history');
        Route::get('/payment/revenue', [AdminController::class, 'revenueReport'])->name('admin.payment.revenue');
        Route::get('/payment/history/export', [AdminController::class, 'exportPaymentHistory'])->name('admin.payment.history.export');
        Route::get('/payment/revenue/export', [AdminController::class, 'exportRevenueReport'])->name('admin.payment.revenue.export');

        // API debug: danh sách user đang cache trong Redis
        Route::get('/redis-users', [AdminController::class, 'cachedUsers'])->name('admin.redis-users');
    });
});

// Đặt lịch nộp hồ sơ
Route::middleware('auth')->group(function () {
    Route::get('/appointment/{id}', [LichHenController::class, 'show'])->name('appointment');
    Route::post('/appointment/{id}', [LichHenController::class, 'store'])->name('appointment.store');
    Route::get('/appointment/{id}/available-slots', [LichHenController::class, 'getAvailableSlots'])->name('appointment.available-slots');
});

// Check-in lịch hẹn (không cần auth vì dùng token)
Route::get('/appointment/checkin/{token}', [LichHenController::class, 'checkin'])->name('appointment.checkin');
Route::post('/appointment/checkin/{token}', [LichHenController::class, 'processCheckin'])->name('appointment.checkin.process');



Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');

// Nộp hồ sơ trực tuyến (yêu cầu đăng nhập)
Route::middleware('auth')->group(function () {
    Route::get('/nop-ho-so/{maTTHC}', [SubmitController::class, 'showByTTHC'])->name('nop-ho-so.show');
    Route::post('/nop-ho-so/{maTTHC}', [SubmitController::class, 'submitApi'])->name('nop-ho-so.submit');
    Route::post('/nop-ho-so/payment/save', [SubmitController::class, 'savePaymentHistory'])->name('nop-ho-so.payment.save');
});
// // Auth routes (giả sử đã cài Laravel Breeze hoặc Auth)
// Route::post('/login', [LoginController::class, 'login'])->name('login');
// Route::post('/register', [RegisterController::class, 'register'])->name('register');
// Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
// hổ trợ
Route::controller(SupportController::class)->group(function () {
    Route::get('/support/about', 'about')->name('support.about');
    Route::get('/support/terms', 'terms')->name('support.terms');
    Route::get('/support/guide', 'guide')->name('support.guide');
    Route::get('/support/notice', 'notice')->name('support.notice');
    Route::get('/support/faq', 'faq')->name('support.faq');
});
Route::post('/vnpay_payment',[PaymentController::class,'vnpay_payment']);
Route::get('/vnpay_return',[PaymentController::class,'vnpay_return'])->name('vnpay.return');
Route::view('form','pages.form')->name('form');
