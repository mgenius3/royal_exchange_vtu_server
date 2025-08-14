<!DOCTYPE html>
<html>
<head>
    <title>Activity Notification</title>
</head>
<body>
    <h1>Hello, {{ $userName }}!</h1>
    <p>We wanted to let you know that the following activity has occurred on your account:</p>
    <h2>{{ $activity }}</h2>
    <p><strong>Details:</strong></p>
    <ul>
        @foreach($details as $key => $value)
            <li>{{ $key }}: {{ $value }}</li>
        @endforeach
    </ul>
    <p>If you did not initiate this activity, please contact support immediately.</p>
    <p>Best regards,<br>Royal Exchange Team</p>
</body>
</html>