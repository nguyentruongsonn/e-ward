@foreach ($notifications as $notification)
    <div class="list-group-item list-group-item-action 
        {{ !$notification->is_read ? 'bg-light border-start border-3 border-warning' : '' }} 
        mb-2 rounded notification-item"
        style="cursor: pointer;" 
        data-notification-id="{{ $notification->id }}"
        data-notification-read="{{ $notification->is_read ? '1' : '0' }}"
        onclick="openNotificationModal({{ $notification->id }}, {{ $notification->is_read ? 'true' : 'false' }})">
        <div class="d-flex w-100 justify-content-between align-items-start">
            <div class="flex-grow-1">
                <h6 class="mb-1 {{ !$notification->is_read ? 'fw-bold' : '' }}">
                    @if (!$notification->is_read)
                        <span class="badge bg-warning text-dark me-2">Mới</span>
                    @endif
                    {{ $notification->tieuDe }}
                </h6>
                <p class="mb-1 text-muted">
                    {{ Str::limit($notification->noiDung ?? '', 100) }}
                </p>
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>
                    {{ $notification->created_at ? $notification->created_at->format('d/m/Y H:i') : '' }}
                    @if ($notification->loai)
                        <span class="ms-2">
                            <i class="fas fa-tag me-1"></i>{{ $notification->loai }}
                        </span>
                    @endif
                </small>
            </div>
        </div>
    </div>
@endforeach

