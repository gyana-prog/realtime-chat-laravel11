<!DOCTYPE html>
<html>

<head>
    <title>Real-time Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #messages {
            height: 400px;
            overflow-y: auto;
            background: #f8f9fa;
        }

        .message {
            margin: 10px 0;
        }

        .me {
            text-align: right;
        }

        .me .bubble {
            background: #007bff;
            color: white;
        }

        .bubble {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 20px;
            max-width: 70%;
        }
    </style>
</head>

<body class="p-4">
    <div class="container">
        <h1 class="text-center mb-4">💬 Real-time Chat (Phase 5)</h1>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Messages -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        Messages
                    </div>
                    <div id="messages" class="p-3">
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-comments fa-3x mb-3 opacity-50"></i>
                            <p>No messages yet. Send one!</p>
                        </div>
                    </div>
                </div>

                <!-- Send Form -->
                <div class="card">
                    <div class="card-body">
                        <form id="messageForm">
                            <div class="row">
                                <div class="col-md-3">
                                    <input type="number" class="form-control" id="senderId" value="1" min="1"
                                        max="999" placeholder="Sender ID">
                                </div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control" id="messageText"
                                        placeholder="Type message..." required maxlength="200">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-paper-plane"></i> Send
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>

    <script>
        $('#messageForm').on('submit', function (e) {
            e.preventDefault();

            const senderId = $('#senderId').val();
            const messageText = $('#messageText').val().trim();
            const $sendBtn = $('#messageForm button[type="submit"]');

            if (!messageText) return;

            // 🔥 LOADING STATE
            $sendBtn.html('<i class="fas fa-spinner fa-spin me-1"></i>Sending...').prop('disabled', true);

            // AJAX Call
            $.ajax({
                url: '/api/create/messages',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    sender_id: parseInt(senderId),
                    message: messageText
                }),
                success: function (response) {
                    console.log('✅ Success:', response);

                    // Add message to chat
                    addMessage(senderId, messageText, response.message_id, 'me');

                    // SUCCESS TOAST
                    showToast('✅ Sent!', `Message ID: #${response.message_id}`, 'success');

                    // Clear input
                    $('#messageText').val('');
                },
                error: function (xhr) {
                    console.error('❌ Error:', xhr.responseText);
                    showToast('❌ Failed!', 'Message not sent', 'danger');
                },
                // 🔥 ALWAYS RESET BUTTON (success OR error)
                complete: function () {
                    $sendBtn.html('<i class="fas fa-paper-plane me-1"></i>Send').prop('disabled', false);
                }
            });
        });

        // 🔥 PERFECT TOAST FUNCTION
        function showToast(title, message, type) {
            // Create toast HTML
            const toastHtml = `
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <strong class="me-2">${title}</strong>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

            // Add container if missing
            if (!$('.toast-container').length) {
                $('body').append('<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1090;"></div>');
            }

            // Add + show toast
            $('.toast-container').append(toastHtml);
            const toastEl = $('.toast-container .toast:last')[0];
            const toast = new bootstrap.Toast(toastEl);
            toast.show();

            // Auto remove after 4 seconds
            setTimeout(() => $(toastEl).alert('close'), 4000);
        }

        // Add message to chat
        function addMessage(senderId, text, id, type) {
            $('#messages .text-center').remove();
            const html = `
            <div class="message ${type}">
                <div class="bubble">
                    <strong>${type === 'me' ? 'You' : 'User #' + senderId}</strong><br>
                    ${text}<br><small class="opacity-75">#${id}</small>
                </div>
            </div>
        `;
            $('#messages').append(html).scrollTop($('#messages')[0].scrollHeight);
        }
    </script>

</body>

</html>