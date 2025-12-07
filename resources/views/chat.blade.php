<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-time Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background: #f8f9fa; min-height: 100vh; }
        .toast-container { position: fixed; top: 20px; right: 20px; z-index: 1090; }
        #messages { height: 400px; overflow-y: auto; }
        .msg-me { text-align: right; }
        .msg-me .bubble { background: #007bff; color: white; }
        .msg-other .bubble { background: #28a745; color: white; }
        .bubble { display: inline-block; padding: 10px 15px; border-radius: 18px; max-width: 70%; margin-bottom: 10px; }
    </style>
</head>
<body class="py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <h2 class="text-center mb-4 text-primary">
                    <i class="fas fa-comments me-2"></i>Real-time Chat
                </h2>

                <!-- Messages -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-inbox me-2"></i>Messages
                    </div>
                    <div class="card-body" id="messages">
                        <p class="text-muted text-center" id="noMessages">No messages yet...</p>
                    </div>
                </div>

                <!-- Form -->
                <div class="card">
                    <div class="card-body">
                        <form id="msgForm">
                            <div class="row g-2">
                                <div class="col-3">
                                    <input type="number" class="form-control" id="senderId" value="123" min="1" max="999">
                                </div>
                                <div class="col-7">
                                    <input type="text" class="form-control" id="msgText" placeholder="Type message..." required>
                                </div>
                                <div class="col-2">
                                    <button type="submit" class="btn btn-primary w-100" id="sendBtn">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div id="status" class="mt-2 small text-muted">
                            <i class="fas fa-spinner fa-spin"></i> Connecting...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container"></div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

   <script>
    $(function() {
        // WEBSOCKET SETUP
        var pusher = new Pusher('{{ env("REVERB_APP_KEY", "local-key") }}', {
            wsHost: '{{ env("REVERB_HOST", "127.0.0.1") }}',
            wsPort: {{ env("REVERB_PORT", 8080) }},
            forceTLS: false,
            disableStats: true,
            enabledTransports: ['ws'],
            cluster: 'mt1'
        });

        var channel = pusher.subscribe('chat-messages');

        // LISTEN - Only shows OTHER users' messages
        channel.bind('new-message', function(data) {
            console.log('🌐 RECEIVED FROM OTHERS:', data);
            showMessage(data.sender_id, data.message, data.id, 'other');
            showToast('New Message!', 'User #' + data.sender_id + ': ' + data.message, 'success');
        });

        pusher.connection.bind('connected', function() {
            $('#status').html('<i class="fas fa-check-circle text-success"></i> Connected! Socket: ' + pusher.connection.socket_id);
            console.log('✅ CONNECTED, Socket ID:', pusher.connection.socket_id);
        });

        // SEND MESSAGE (with Socket ID)
        $('#msgForm').on('submit', function(e) {
            e.preventDefault();

            var senderId = $('#senderId').val();
            var msgText = $('#msgText').val().trim();
            if (!msgText) return;

            $('#sendBtn').prop('disabled', true);

            $.ajax({
                url: '/api/create/messages',
                method: 'POST',
                contentType: 'application/json',
                headers: {
                    'X-Socket-ID': pusher.connection.socket_id  // 🔥 EXCLUDE SENDER
                },
                data: JSON.stringify({ sender_id: parseInt(senderId), message: msgText }),
                success: function(res) {
                    console.log('✅ SENT:', res);
                    showMessage(senderId, msgText, res.message_id, 'me');  // Show as "You"
                    $('#msgText').val('');
                },
                error: function(err) {
                    console.log('❌ ERROR:', err);
                },
                complete: function() {
                    $('#sendBtn').prop('disabled', false);
                }
            });
        });

        function showMessage(senderId, text, id, type) {
            $('#noMessages').remove();
            var html = '<div class="msg-' + type + '">' +
                '<div class="bubble">' +
                '<strong>' + (type === 'me' ? 'You' : 'User #' + senderId) + '</strong><br>' +
                text + '<br><small>#' + id + '</small></div></div>';
            $('#messages').append(html).scrollTop($('#messages')[0].scrollHeight);
        }

        function showToast(title, msg, type) {
            var html = '<div class="toast show align-items-center text-white bg-' + type + ' border-0">' +
                '<div class="d-flex"><div class="toast-body"><strong>' + title + '</strong><br>' + msg + '</div>' +
                '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>';
            $('.toast-container').append(html);
            setTimeout(function() { $('.toast-container .toast').first().remove(); }, 5000);
        }
    });
</script>

</body>
</html>
