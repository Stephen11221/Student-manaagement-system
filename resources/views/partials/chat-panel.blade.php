<style>
    .chat-container {
        position: fixed;
        bottom: 100px;
        right: 22px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: flex-end;
        z-index: 9998;
        max-width: calc(100vw - 44px);
    }

    .chat-popup {
        position: relative;
        width: 320px;
        height: 400px;
        background: rgba(15, 23, 42, 0.98);
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(20px);
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            transform: translateY(100px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .chat-popup__header {
        padding: 12px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.12);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, rgba(22, 163, 74, 0.2), rgba(34, 211, 238, 0.2));
    }

    .chat-popup__name {
        color: #f8fafc;
        font-weight: 600;
        margin: 0;
        font-size: 0.95rem;
        flex: 1;
    }

    .chat-popup__close {
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        font-size: 1rem;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chat-popup__close:hover {
        color: #f8fafc;
    }

    .chat-popup__messages {
        flex: 1;
        overflow-y: auto;
        padding: 10px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .chat-popup__message {
        background: rgba(34, 211, 238, 0.12);
        padding: 8px;
        border-radius: 6px;
        border-left: 3px solid #22d3ee;
        word-wrap: break-word;
    }

    .chat-popup__message-text {
        color: #f8fafc;
        font-size: 0.85rem;
        line-height: 1.4;
    }

    .chat-popup__message-time {
        color: #64748b;
        font-size: 0.7rem;
        margin-top: 3px;
    }

    .chat-popup__input-area {
        padding: 8px;
        border-top: 1px solid rgba(148, 163, 184, 0.12);
        display: flex;
        gap: 6px;
    }

    .chat-popup__textarea {
        flex: 1;
        padding: 6px;
        border-radius: 4px;
        border: 1px solid rgba(148, 163, 184, 0.2);
        background: rgba(2, 6, 23, 0.56);
        color: #f8fafc;
        font-family: inherit;
        resize: none;
        min-height: 32px;
        font-size: 0.85rem;
    }

    .chat-popup__send {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        border-radius: 4px;
        background: linear-gradient(135deg, #22d3ee, #06b6d4);
        color: #082f49;
        border: none;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.8rem;
        white-space: nowrap;
    }

    .chat-popup__send:hover {
        opacity: 0.9;
    }

    .chat-list {
        position: fixed;
        right: 22px;
        bottom: 100px;
        width: 280px;
        max-height: 400px;
        background: rgba(15, 23, 42, 0.98);
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 12px;
        z-index: 9998;
        display: none;
        flex-direction: column;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(20px);
        animation: slideIn 0.3s ease-out;
    }

    .chat-list.active {
        display: flex !important;
    }

    .chat-list__header {
        padding: 12px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.12);
        color: #f8fafc;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .chat-list__users {
        flex: 1;
        overflow-y: auto;
        padding: 8px;
    }

    .chat-list__user {
        padding: 10px;
        margin-bottom: 6px;
        background: rgba(34, 211, 238, 0.12);
        border-radius: 6px;
        cursor: pointer;
        color: #dbeafe;
        font-size: 0.9rem;
        border: 1px solid rgba(34, 211, 238, 0.2);
        transition: all 0.2s;
    }

    .chat-list__user:hover {
        background: rgba(34, 211, 238, 0.25);
        border-color: rgba(34, 211, 238, 0.4);
    }

    @media (max-width: 640px) {
        .chat-container {
            bottom: 70px;
        }

        .chat-popup {
            width: 280px;
            height: 350px;
        }

        .chat-list {
            bottom: 70px;
            width: 250px;
        }
    }
</style>

<div class="chat-list" id="chatList">
    <div class="chat-list__header">
        <i class="fa-solid fa-comments"></i> Open Chat
    </div>
    <div class="chat-list__users" id="chatListUsers">
        @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'trainer']))
            <div class="chat-list__user" data-user-id="trainer_1" data-user-name="Mr. Smith">👨‍🏫 Mr. Smith</div>
            <div class="chat-list__user" data-user-id="trainer_2" data-user-name="Mrs. Johnson">👩‍🏫 Mrs. Johnson</div>
            <div class="chat-list__user" data-user-id="student_1" data-user-name="John Doe">👨‍🎓 John Doe</div>
            <div class="chat-list__user" data-user-id="student_2" data-user-name="Jane Smith">👩‍🎓 Jane Smith</div>
            <div class="chat-list__user" data-user-id="student_3" data-user-name="Mike Johnson">👨‍🎓 Mike Johnson</div>
        @else
            <div class="chat-list__user" data-user-id="trainer_1" data-user-name="Mr. Smith">👨‍🏫 Mr. Smith</div>
            <div class="chat-list__user" data-user-id="trainer_2" data-user-name="Mrs. Johnson">👩‍🏫 Mrs. Johnson</div>
        @endif
    </div>
</div>

<div class="chat-container" id="chatContainer"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatFabBtn = document.getElementById('chatFabBtn');
        const chatPanel = document.getElementById('chatPanel');
        const closeChatPanel = document.getElementById('closeChatPanel');
        const recipientType = document.getElementById('chatRecipientType');
        const recipientSelect = document.getElementById('chatRecipient');
        const recipientLabel = document.getElementById('chatRecipientLabel');
        const chatMessages = document.getElementById('chatPanelMessages');
        const chatInput = document.getElementById('chatPanelInput');
        const sendBtn = document.getElementById('chatPanelSend');

        // Sample data for different recipient types
        const recipients = {
            trainer: {
                label: 'Trainer',
                options: [
                    { value: 'all_trainers', text: 'All Trainers' },
                    { value: 'trainer_1', text: 'Mr. Smith' },
                    { value: 'trainer_2', text: 'Mrs. Johnson' },
                    { value: 'trainer_3', text: 'Dr. Williams' }
                ]
            },
            student: {
                label: 'Student',
                options: [
                    { value: 'all_students', text: 'All Students' },
                    { value: 'student_1', text: 'John Doe' },
                    { value: 'student_2', text: 'Jane Smith' },
                    { value: 'student_3', text: 'Mike Johnson' },
                    { value: 'student_4', text: 'Sarah Davis' }
                ]
            },
            department: {
                label: 'Department',
                options: [
                    { value: 'all_dept', text: 'All Departments' },
                    { value: 'dept_1', text: 'Mathematics' },
                    { value: 'dept_2', text: 'Science' },
                    { value: 'dept_3', text: 'English' },
                    { value: 'dept_4', text: 'History' }
                ]
            }
        };

        // Toggle chat panel when FAB clicked
        if (chatFabBtn) {
            chatFabBtn.addEventListener('click', function(e) {
                e.preventDefault();
                chatPanel.classList.toggle('active');
            });
        }

        // Close chat when close button clicked
        if (closeChatPanel) {
            closeChatPanel.addEventListener('click', function() {
                chatPanel.classList.remove('active');
            });
        }

        // Update recipient dropdown based on type
        if (recipientType) {
            recipientType.addEventListener('change', function() {
                const type = this.value;
                
                if (type && recipients[type]) {
                    recipientLabel.textContent = recipients[type].label;
                    recipientSelect.innerHTML = '<option value="">Select recipient</option>';
                    
                    recipients[type].options.forEach(opt => {
                        const option = document.createElement('option');
                        option.value = opt.value;
                        option.textContent = opt.text;
                        recipientSelect.appendChild(option);
                    });
                } else {
                    recipientLabel.textContent = 'Recipient';
                    recipientSelect.innerHTML = '<option value="">Select recipient</option>';
                }
            });
        }

        // Send message function
        function sendMessage() {
            const message = chatInput.value.trim();
            const recipient = recipientSelect.value;

            if (!message) {
                alert('Please type a message');
                return;
            }

            if (!recipient) {
                alert('Please select a recipient');
                return;
            }

            // Clear "no messages" text
            const emptyMessage = chatMessages.querySelector('.chat-panel__empty');
            if (emptyMessage) {
                emptyMessage.remove();
            }

            // Create message element
            const messageEl = document.createElement('div');
            messageEl.className = 'chat-panel__message';

            const time = new Date().toLocaleTimeString();
            const recipientText = recipientSelect.options[recipientSelect.selectedIndex].text;
            let typeText = '';
            
            if (recipientType && recipientType.value) {
                const type = recipientType.value;
                if (recipients[type]) {
                    typeText = recipients[type].label + ': ';
                }
            }
            
            messageEl.innerHTML = `
                <div class="chat-panel__message-recipient">${typeText}${recipientText}</div>
                <div class="chat-panel__message-text">${message}</div>
                <div class="chat-panel__message-time">${time}</div>
            `;

            chatMessages.appendChild(messageEl);
            chatMessages.scrollTop = chatMessages.scrollHeight;
            chatInput.value = '';
        }

        // Send message on button click
        if (sendBtn) {
            sendBtn.addEventListener('click', sendMessage);
        }

        // Send message on Enter key
        if (chatInput) {
            chatInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
        }

        console.log('Chat initialized:', { chatFabBtn, chatPanel, recipientType, sendBtn });
    });
</script>
