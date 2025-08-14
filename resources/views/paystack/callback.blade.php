<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Callback</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
        }
        .card {
            max-width: 500px;
            width: 100%;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .card-header {
            padding: 20px;
            text-align: center;
            color: white;
        }
        .card-header.success {
            background-color: #28a745;
        }
        .card-header.failed, .card-header.error {
            background-color: #dc3545;
        }
        .card-header.pending {
            background-color: #ffc107;
        }
        .card-body {
            padding: 30px;
            text-align: center;
        }
        .icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        .btn-home {
            margin-top: 20px;
            border-radius: 25px;
            padding: 10px 30px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header {{ $status }}">
            <h3>{{ ucfirst($status) }}!</h3>
        </div>
        <div class="card-body">
            @if($status === 'success')
                <div class="icon text-success">✅</div>
            @elseif($status === 'failed' || $status === 'error')
                <div class="icon text-danger">❌</div>
            @else
                <div class="icon text-warning">⏳</div>
            @endif
            <p>{{ $message }}</p>
            @if($reference)
                <p><strong>Reference:</strong> {{ $reference }}</p>
            @endif
            <a href="{{ user.url('/') }}" class="btn btn-primary btn-home">Return to Home</a>
        </div>
    </div>

    <!-- Bootstrap JS (Optional for interactivity) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>