@auth
    @php
        $recentChatCount = \App\Models\ChatMessage::where('room', 'all-users')
            ->where('created_at', '>=', now()->subDay())
            ->count();
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
            font-weight: 800;
            color: #082f49;
            background: linear-gradient(135deg, #38bdf8, #0ea5e9);
            box-shadow: 0 18px 40px rgba(14, 165, 233, 0.34);
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
            background: rgba(255, 255, 255, 0.18);
            flex-shrink: 0;
            color: #082f49;
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
            color: #0ea5e9;
            border: 2px solid #0ea5e9;
            font-size: 0.72rem;
            line-height: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .chat-fab__label {
            white-space: nowrap;
        }

        .chat-fab__subtext {
            display: block;
            font-size: 0.72rem;
            font-weight: 700;
            opacity: 0.88;
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

    <a href="#" class="chat-fab" id="chatFabBtn" aria-label="Open all users chat">
        <span class="chat-fab__icon">
            <i class="fa-solid fa-comments"></i>
            @if ($recentChatCount > 0)
                <span class="chat-fab__badge">{{ $recentChatCount > 99 ? '99+' : $recentChatCount }}</span>
            @endif
        </span>
        <span class="chat-fab__label">
            Chat
            <span class="chat-fab__subtext">All users</span>
        </span>
    </a>
@endauth
