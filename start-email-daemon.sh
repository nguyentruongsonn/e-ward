#!/bin/bash

echo "========================================"
echo " Email Reply Daemon - E-Ward"
echo "========================================"
echo ""
echo "Daemon sẽ chạy liên tục và kiểm tra email mỗi 30 giây"
echo "Nhấn Ctrl+C để dừng daemon"
echo ""
echo "========================================"
echo ""

cd "$(dirname "$0")"
php artisan email:check-replies --daemon --interval=30

