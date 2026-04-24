@auth
    @php
        $initialMessages = \App\Models\ChatMessage::with('sender')
            ->where('room', 'all-users')
            ->latest()
            ->limit(20)
            ->get()
            ->reverse()
            ->values();
    @endphp

    <style>
        .chat-panel {
            position: fixed;
            right: 22px;
            bottom: 90px;
            width: min(420px, calc(100vw - 32px));
            height: min(620px, calc(100vh - 120px));
            display: none;
            flex-direction: column;
            z-index: 9998;
            border-radius: 28px;
            border: 1px solid rgba(51, 65, 85, 0.95);
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.98), rgba(2, 6, 23, 0.98));
            box-shadow: 0 28px 80px rgba(2, 6, 23, 0.52);
            backdrop-filter: blur(20px);
            overflow: hidden;
        }

        .chat-panel.is-open {
            display: flex;
            animation: chatRise 180ms ease-out;
        }

        @keyframes chatRise {
            from { transform: translateY(16px) scale(0.98); opacity: 0; }
            to { transform: translateY(0) scale(1); opacity: 1; }
        }

        .chat-panel__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 16px 18px;
            border-bottom: 1px solid rgba(51, 65, 85, 0.95);
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.18), rgba(34, 197, 94, 0.14));
        }

        .chat-panel__title {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .chat-panel__name {
            color: #f8fafc;
            font-size: 1rem;
            font-weight: 800;
        }

        .chat-panel__subtitle {
            color: #cbd5e1;
            font-size: 0.82rem;
            line-height: 1.4;
        }

        .chat-panel__status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 10px;
            border-radius: 999px;
            border: 1px solid rgba(34, 197, 94, 0.28);
            background: rgba(34, 197, 94, 0.12);
            color: #bbf7d0;
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            white-space: nowrap;
        }

        .chat-panel__close {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            border: 1px solid rgba(51, 65, 85, 0.95);
            background: rgba(15, 23, 42, 0.92);
            color: #f8fafc;
            cursor: pointer;
        }

        .chat-panel__messages {
            flex: 1;
            overflow-y: auto;
            padding: 16px 16px 12px;
            display: grid;
            gap: 10px;
        }

        .chat-panel__empty {
            min-height: 100%;
            display: grid;
            place-items: center;
            text-align: center;
            color: #cbd5e1;
            padding: 24px;
        }

        .chat-panel__empty strong {
            display: block;
            color: #f8fafc;
            font-size: 1.05rem;
            margin-bottom: 6px;
        }

        .chat-panel__message {
            display: grid;
            gap: 6px;
            padding: 12px 14px;
            border-radius: 18px;
            border: 1px solid rgba(51, 65, 85, 0.95);
            background: rgba(15, 23, 42, 0.9);
        }

        .chat-panel__message.is-self {
            background: rgba(56, 189, 248, 0.14);
            border-color: rgba(56, 189, 248, 0.28);
        }

        .chat-panel__message-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-size: 0.82rem;
            color: #cbd5e1;
        }

        .chat-panel__message-sender {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 800;
            color: #f8fafc;
        }

        .chat-panel__message-role {
            color: #7dd3fc;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .chat-panel__message-text {
            color: #e2e8f0;
            font-size: 0.95rem;
            line-height: 1.6;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .chat-panel__message-time {
            color: #94a3b8;
            font-size: 0.75rem;
        }

        .chat-panel__composer {
            padding: 14px 16px 16px;
            border-top: 1px solid rgba(51, 65, 85, 0.95);
            background: rgba(2, 6, 23, 0.88);
        }

        .chat-panel__composer-note {
            color: #cbd5e1;
            font-size: 0.82rem;
            line-height: 1.5;
            margin-bottom: 10px;
        }

        .chat-panel__input-row {
            display: flex;
            gap: 10px;
            align-items: end;
        }

        .chat-panel__textarea {
            flex: 1;
            min-height: 84px;
            resize: vertical;
            border-radius: 16px;
            border: 1px solid rgba(51, 65, 85, 0.95);
            background: rgba(15, 23, 42, 0.96);
            color: #f8fafc;
            padding: 12px 14px;
            font: inherit;
            outline: none;
        }

        .chat-panel__textarea:focus {
            border-color: rgba(56, 189, 248, 0.65);
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.12);
        }

        .chat-panel__send {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 14px;
            border-radius: 16px;
            border: 0;
            background: linear-gradient(135deg, #38bdf8, #0ea5e9);
            color: #082f49;
            font-weight: 800;
            cursor: pointer;
            white-space: nowrap;
            min-width: 120px;
        }

        .chat-panel__send:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .chat-panel__hint {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 10px;
            color: #94a3b8;
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .chat-panel__toast {
            position: absolute;
            left: 16px;
            right: 16px;
            top: 64px;
            z-index: 2;
            display: none;
            border-radius: 14px;
            padding: 10px 12px;
            background: rgba(34, 197, 94, 0.14);
            border: 1px solid rgba(34, 197, 94, 0.28);
            color: #bbf7d0;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .chat-panel__toast.is-visible {
            display: block;
        }

        @media (max-width: 640px) {
            .chat-panel {
                right: 12px;
                left: 12px;
                width: auto;
                bottom: 78px;
                height: min(72vh, 560px);
            }

            .chat-panel__input-row {
                flex-direction: column;
            }

            .chat-panel__send {
                width: 100%;
            }
        }
    </style>

    <div
        class="chat-panel"
        id="chatPanel"
        data-chat-panel
        data-fetch-url="{{ route('chat.messages.index') }}"
        data-send-url="{{ route('chat.messages.store') }}"
        aria-hidden="true"
    >
        <div class="chat-panel__toast" data-chat-toast></div>
        <div class="chat-panel__header">
            <div class="chat-panel__title">
                <div class="chat-panel__name"><i class="fa-solid fa-comments"></i> All Users Chat</div>
                <div class="chat-panel__subtitle">Everyone in the portal can see this stream. Keep messages short, clear, and school-appropriate.</div>
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
                <div class="chat-panel__status"><i class="fa-solid fa-signal"></i> Live</div>
                <button type="button" class="chat-panel__close" id="closeChatPanel" aria-label="Close chat panel">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <div class="chat-panel__messages" id="chatPanelMessages">
            @forelse ($initialMessages as $message)
                <div class="chat-panel__message {{ $message->sender_id === auth()->id() ? 'is-self' : '' }}">
                    <div class="chat-panel__message-head">
                        <div class="chat-panel__message-sender">
                            <i class="fa-solid fa-user"></i>
                            <span>{{ $message->sender?->name ?? 'Unknown user' }}</span>
                            <span class="chat-panel__message-role">{{ ucfirst(str_replace('_', ' ', $message->sender?->role ?? 'user')) }}</span>
                        </div>
                        <div class="chat-panel__message-time">{{ $message->created_at?->diffForHumans() }}</div>
                    </div>
                    <div class="chat-panel__message-text">{{ $message->message }}</div>
                </div>
            @empty
                <div class="chat-panel__empty">
                    <div>
                        <strong>No messages yet</strong>
                        <div>Be the first to start the all-users conversation.</div>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="chat-panel__composer">
            <div class="chat-panel__composer-note">
                Your message will be posted to the shared all-users chat room and visible to everyone who is signed in.
            </div>
            <div class="chat-panel__input-row">
                <textarea
                    id="chatPanelInput"
                    class="chat-panel__textarea"
                    placeholder="Write a message to everyone..."
                    maxlength="1000"
                ></textarea>
                <button type="button" class="chat-panel__send" id="chatPanelSend">
                    <i class="fa-solid fa-paper-plane"></i> Send
                </button>
            </div>
            <div class="chat-panel__hint">
                <span><i class="fa-regular fa-circle-dot"></i> Shared room</span>
                <span id="chatPanelCount">{{ $initialMessages->count() }} messages loaded</span>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chatFabBtn = document.getElementById('chatFabBtn');
            const chatPanel = document.getElementById('chatPanel');
            const closeChatPanel = document.getElementById('closeChatPanel');
            const chatMessages = document.getElementById('chatPanelMessages');
            const chatInput = document.getElementById('chatPanelInput');
            const sendBtn = document.getElementById('chatPanelSend');
            const chatToast = document.querySelector('[data-chat-toast]');
            const chatCount = document.getElementById('chatPanelCount');
            const fetchUrl = chatPanel?.dataset.fetchUrl;
            const sendUrl = chatPanel?.dataset.sendUrl;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            let refreshTimer = null;

            const escapeHtml = (value) => {
                const div = document.createElement('div');
                div.textContent = value ?? '';
                return div.innerHTML;
            };

            const showToast = (message, isError = false) => {
                if (!chatToast) return;
                chatToast.textContent = message;
                chatToast.style.background = isError ? 'rgba(239, 68, 68, 0.14)' : 'rgba(34, 197, 94, 0.14)';
                chatToast.style.borderColor = isError ? 'rgba(239, 68, 68, 0.28)' : 'rgba(34, 197, 94, 0.28)';
                chatToast.style.color = isError ? '#fecaca' : '#bbf7d0';
                chatToast.classList.add('is-visible');
                window.clearTimeout(chatToast._timer);
                chatToast._timer = window.setTimeout(() => chatToast.classList.remove('is-visible'), 2200);
            };

            const renderMessages = (messages) => {
                if (!chatMessages) return;

                chatMessages.innerHTML = '';

                if (!messages.length) {
                    chatMessages.innerHTML = `
                        <div class="chat-panel__empty">
                            <div>
                                <strong>No messages yet</strong>
                                <div>Be the first to start the all-users conversation.</div>
                            </div>
                        </div>
                    `;
                    return;
                }

                messages.forEach((item) => {
                    const el = document.createElement('div');
                    el.className = `chat-panel__message${item.is_self ? ' is-self' : ''}`;
                    el.innerHTML = `
                        <div class="chat-panel__message-head">
                            <div class="chat-panel__message-sender">
                                <i class="fa-solid fa-user"></i>
                                <span>${escapeHtml(item.sender)}</span>
                                <span class="chat-panel__message-role">${escapeHtml(item.role)}</span>
                            </div>
                            <div class="chat-panel__message-time">${escapeHtml(item.time ?? '')}</div>
                        </div>
                        <div class="chat-panel__message-text">${escapeHtml(item.message)}</div>
                    `;
                    chatMessages.appendChild(el);
                });

                chatMessages.scrollTop = chatMessages.scrollHeight;
            };

            const loadMessages = async () => {
                if (!fetchUrl || !chatMessages) return;

                try {
                    const response = await fetch(fetchUrl, {
                        headers: {
                            'Accept': 'application/json',
                        },
                    });

                    if (!response.ok) return;

                    const payload = await response.json();
                    const messages = payload.messages ?? [];
                    renderMessages(messages);

                    if (chatCount) {
                        chatCount.textContent = `${messages.length} message${messages.length === 1 ? '' : 's'} loaded`;
                    }
                } catch (error) {
                    console.error('Failed to load chat messages', error);
                }
            };

            const sendMessage = async () => {
                const message = chatInput?.value.trim();

                if (!message) {
                    showToast('Please type a message first.', true);
                    return;
                }

                if (!sendUrl) return;

                sendBtn.disabled = true;

                try {
                    const response = await fetch(sendUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ message }),
                    });

                    if (!response.ok) {
                        const payload = await response.json().catch(() => ({}));
                        const errorMessage = payload.message || 'Could not send message.';
                        showToast(errorMessage, true);
                        return;
                    }

                    chatInput.value = '';
                    await loadMessages();
                    showToast('Message posted to all users.');
                } catch (error) {
                    console.error('Failed to send chat message', error);
                    showToast('Could not send message right now.', true);
                } finally {
                    sendBtn.disabled = false;
                }
            };

            const openPanel = () => {
                if (!chatPanel) return;
                chatPanel.classList.add('is-open');
                chatPanel.setAttribute('aria-hidden', 'false');
                loadMessages();
                refreshTimer = window.setInterval(loadMessages, 10000);
                window.setTimeout(() => chatInput?.focus(), 50);
            };

            const closePanel = () => {
                if (!chatPanel) return;
                chatPanel.classList.remove('is-open');
                chatPanel.setAttribute('aria-hidden', 'true');
                if (refreshTimer) {
                    window.clearInterval(refreshTimer);
                    refreshTimer = null;
                }
            };

            chatFabBtn?.addEventListener('click', function (event) {
                event.preventDefault();
                if (chatPanel?.classList.contains('is-open')) {
                    closePanel();
                } else {
                    openPanel();
                }
            });

            closeChatPanel?.addEventListener('click', closePanel);

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') closePanel();
            });

            sendBtn?.addEventListener('click', sendMessage);

            chatInput?.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' && !event.shiftKey) {
                    event.preventDefault();
                    sendMessage();
                }
            });
        });
    </script>
@endauth
