<?php

namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class UserRedisService
{
    private const HASH_KEY = 'users';
    private const PROFILE_PREFIX = 'users:profile:';
    private const DEFAULT_TTL = 3600; // 1 hour to keep basic profile cache fresh

    /**
     * Lưu thông tin user đã đăng nhập vào Redis
     */
    public function storeAuthenticatedUser(Authenticatable $user, Request $request): void
    {
        $userId = $this->extractUserId($user);
        if (!$userId) {
            return;
        }

        $payload = $this->buildPayload($user, $request);

        $this->execute(function () use ($userId, $payload) {
            // Hash 'users' giữ danh sách người đang đăng nhập
            Redis::hset(self::HASH_KEY, $userId, json_encode($payload, JSON_UNESCAPED_UNICODE));

            // Đồng thời lưu cache profile riêng để tái sử dụng nhanh
            Redis::setex($this->profileKey($userId), self::DEFAULT_TTL, json_encode($payload, JSON_UNESCAPED_UNICODE));
        });
    }

    /**
     * Xóa thông tin user khỏi Redis khi logout
     */
    public function forget(Authenticatable $user = null): void
    {
        $user = $user ?: auth()->user();
        $userId = $this->extractUserId($user);
        if (!$userId) {
            return;
        }

        $this->execute(function () use ($userId) {
            Redis::hdel(self::HASH_KEY, $userId);
            Redis::del($this->profileKey($userId));
        });
    }

    /**
     * Lấy thông tin user từ cache, nếu không có thì lấy từ callback và lưu lại
     */
    public function rememberProfile(int|string $userId, callable $resolver, int $ttl = self::DEFAULT_TTL): array
    {
        $key = $this->profileKey($userId);

        $cached = $this->execute(function () use ($key) {
            return Redis::get($key);
        });

        if ($cached) {
            return json_decode($cached, true) ?: [];
        }

        $data = $resolver();
        if (!is_array($data)) {
            return [];
        }

        $this->execute(function () use ($key, $ttl, $data) {
            Redis::setex($key, $ttl, json_encode($data, JSON_UNESCAPED_UNICODE));
        });

        return $data;
    }

    /**
     * Lấy toàn bộ user đang được cache trong hash 'users'
     */
    public function getAllCachedUsers(): array
    {
        $raw = $this->execute(function () {
            return Redis::hgetall(self::HASH_KEY) ?? [];
        });

        if (!is_array($raw)) {
            return [];
        }

        $users = [];
        foreach ($raw as $id => $json) {
            $decoded = json_decode($json, true) ?: [];
            // Đảm bảo luôn có id trong payload
            $decoded['id'] = $decoded['id'] ?? (int) $id;
            $users[] = $decoded;
        }

        return $users;
    }

    /**
     * Tìm kiếm user trong Redis theo tên/email/role (chứa chuỗi q, không phân biệt hoa thường)
     */
    public function searchCachedUsers(string $query): array
    {
        $query = mb_strtolower(trim($query));
        if ($query === '') {
            return $this->getAllCachedUsers();
        }

        $all = $this->getAllCachedUsers();

        return array_values(array_filter($all, function (array $u) use ($query) {
            $haystack = mb_strtolower(
                ($u['email'] ?? '') . ' ' .
                ($u['name'] ?? '') . ' ' .
                ($u['role'] ?? '')
            );
            return str_contains($haystack, $query);
        }));
    }

    private function extractUserId(?Authenticatable $user): ?int
    {
        if (!$user) {
            return null;
        }

        return $user->IDnguoiDung ?? $user->id ?? null;
    }

    private function buildPayload(Authenticatable $user, Request $request): array
    {
        return [
            'id' => $this->extractUserId($user),
            'email' => $user->email ?? null,
            'name' => method_exists($user, 'getAttribute') ? ($user->getAttribute('hoTen') ?? $user->name ?? optional($user->nguoi)->hoTen) : null,
            'role' => $user->vaiTro ?? optional($user->nguoi)->vaiTro,
            'login_at' => now()->toDateTimeString(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $request->session()->getId(),
        ];
    }

    private function profileKey(int|string $userId): string
    {
        return self::PROFILE_PREFIX . $userId;
    }

    /**
     * Bọc thao tác Redis để đảm bảo không làm hỏng luồng chính
     */
    private function execute(callable $callback)
    {
        try {
            return $callback();
        } catch (\Throwable $e) {
            Log::warning('UserRedisService error: ' . $e->getMessage());
            return null;
        }
    }
}

