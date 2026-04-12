@php
    $unreadNotifications = auth()->check()
        ? auth()->user()->notifications()->whereNull('read_at')->count()
        : 0;
@endphp

<style>
    .chat-fab {
        position: fixed;
        right: 22px;
        bottom: 22px;
        z-index: 60;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 14px 18px;
        border-radius: 999px;
        text-decoration: none;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        box-shadow: 0 18px 40px rgba(220, 38, 38, 0.35);
        border: 1px solid rgba(255, 255, 255, 0.12);
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .chat-fab:hover {
        transform: translateY(-2px);
    }

    .chat-fab__icon {
        position: relative;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.16);
        flex-shrink: 0;
    }

    .chat-fab__badge {
        position: absolute;
        top: -6px;
        right: -6px;
        min-width: 20px;
        height: 20px;
        padding: 0 6px;
        border-radius: 999px;
        background: #fff;
        color: #dc2626;
        border: 2px solid #dc2626;
        font-size: 0.72rem;
        line-height: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .chat-fab__label {
        white-space: nowrap;
    }

    @media (max-width: 640px) {
        .chat-fab {
            right: 14px;
            bottom: 14px;
            padding: 12px 14px;
        }

        .chat-fab__label {
            display: none;
        }
    }
</style>

<a href="#" class="chat-fab" id="chatFabBtn" aria-label="Open messages and notifications">
    <span class="chat-fab__icon">
        <i class="fa-solid fa-comments"></i>
        @if ($unreadNotifications > 0)
            <span class="chat-fab__badge">{{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}</span>
        @endif
    </span>
    <span class="chat-fab__label">Chat</span>
</a>
