    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-body footer mt-5 pt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-4">THÔNG TIN LIÊN HỆ</h5>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Ủy ban nhân dân Phường ABC</p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+012 345 67890</p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>contact@phuongabc.gov.vn</p>
                    <p class="mb-2"><i class="far fa-clock me-3"></i>Thứ 2 - Chủ nhật: 07:30 - 17:00</p>
                    <div class="d-flex pt-2">
                        <a class="btn btn-square btn-outline-light btn-social" href="#" title="Facebook"><i
                                class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-square btn-outline-light btn-social" href="#" title="Zalo"><i
                                class="fab fa-youtube"></i></a>
                        <a class="btn btn-square btn-outline-light btn-social" href="#" title="Email"><i
                                class="fa fa-envelope"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-4">DỊCH VỤ CÔNG</h5>
                    <a class="btn btn-link" href="{{ route('services') }}">Danh sách dịch vụ</a>
                    <a class="btn btn-link" href="{{ route('outstanding-service') }}">Dịch vụ nổi bật</a>
                    <a class="btn btn-link" href="{{ route('tracking') }}">Tra cứu hồ sơ</a>
                    <a class="btn btn-link" href="{{ route('service.ratings') }}">Đánh giá dịch vụ</a>
                    <a class="btn btn-link" href="{{ route('support.guide') }}">Hướng dẫn sử dụng</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-4">THÔNG TIN</h5>
                    <a class="btn btn-link" href="{{ route('support.about') }}">Giới thiệu</a>
                    <a class="btn btn-link" href="{{ route('support.notice') }}">Thông báo</a>
                    <a class="btn btn-link" href="{{ route('support.faq') }}">Câu hỏi thường gặp</a>
                    <a class="btn btn-link" href="{{ route('support.terms') }}">Điều khoản sử dụng</a>
                    <a class="btn btn-link" href="{{ route('contact') }}">Liên hệ</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-4">HỖ TRỢ</h5>
                    <p class="mb-2">Nếu bạn cần hỗ trợ, vui lòng liên hệ:</p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-2"></i>Hotline: 1900-xxxx</p>
                    <p class="mb-2"><i class="fa fa-envelope me-2"></i>Email: support@phuongabc.gov.vn</p>
                    <p class="mb-2"><i class="far fa-clock me-2"></i>Thời gian: 7:30 - 17:00 (T2-CN)</p>
                    @auth
                        <a href="{{ route('profile') }}" class="btn btn-sm btn-primary mt-2">
                            <i class="fa fa-user me-1"></i> Tài khoản của tôi
                        </a>
                    @else
                        <button type="button" class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="fa fa-sign-in-alt me-1"></i> Đăng nhập
                        </button>
                    @endauth
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        &copy; {{ date('Y') }} <a href="{{ route('home') }}" class="text-white">Ủy ban nhân dân Phường ABC</a>. Tất cả các quyền được bảo lưu.
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <span class="text-white">HỆ THỐNG QUẢN LÝ THỦ TỤC HÀNH CHÍNH TRỰC TUYẾN - E-WARD</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a href="#" class="btn btn-lg btn-back-to-top btn-lg-square rounded-circle back-to-top"><i
            class="bi bi-arrow-up"></i></a>





    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('lib/counterup/counterup.min.js') }}"></script>
    <script src="{{ asset('lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('lib/isotope/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('lib/lightbox/js/lightbox.min.js') }}"></script>


    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/chatbot.js') }}"></script>


    </body>

    </html>
