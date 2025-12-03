<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/btn.css') }}" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('front_view.ico') }}">
</head>
<body>
    <div>
        <img src="{{ asset('image/angled_view.png') }}" alt="Home" class="top-middle-image" />
    </div>
    <div class="Main_text">
        <h1>Melodex</h1>
    </div>
    <div class="side_text">
        <h2>♪ Music without limits ♪</h2>
    </div>
    <div class="container">
        <h1 class="welcome">Welcome</h1>
        <div class="options">
            <a href="{{ route('login') }}" class="btn btn-login">Login</a>
            <a href="{{ route('register') }}" class="btn btn-register">Registration</a>
        </div>
    </div>
</body>
</html>