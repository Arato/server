<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>Password Reset</h2>

<div>
    <p>To reset your password, complete this form: <a href="http://localhost:9000/#/password/reset/{{$token}}">http://localhost:9000/#/password/reset/{{$token}}</a>.
    </p>

    <p>This link will expire in {{ Config::get('auth.reminder.expire', 60) }} minutes.</p>
</div>
</body>
</html>
