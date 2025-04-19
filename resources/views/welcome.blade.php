<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Welcome - Veritabe Group | Stock Management</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #ffffff;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }

    .background-image {
        position: absolute;
        bottom: 0;
        right: 5%;
        max-height: 70%;
        opacity: 0.95;
    }

    .overlay {
        z-index: 10;
        text-align: center;
        max-width: 800px;
        padding: 2rem;
    }

    .title {
        font-size: 3rem;
        font-weight: bold;
        color: #000;
    }

    .subtitle {
        font-size: 1.5rem;
        color: #444;
        margin-top: 0.5rem;
        margin-bottom: 2rem;
    }

    .btn {
        background-color: #000;
        color: #fff;
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
        text-decoration: none;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .btn:hover {
        background-color: #444;
    }

    img.imgdetails {
        width: 40%;
    }

    body {
        display: flex;
        flex-flow: column;
        align-items: center;
        justify-content: center;
    }

    img.imglogo {
        height: 54px;
        margin-top: 10rem;
    }

    .overlay {
        text-align: center;
    }
    </style>

</head>

<body>
    <div class="col-md-12">
        <div class="text-center">
            <img src="{{ asset('static/veritablelogo.png') }}" alt="Car" class="imglogo" />
        </div>
    </div>
    <div class="overlay">
        <h1 class="title">Welcome</h1>
        <p class="subtitle">Veritable Stock Management System</p>
        <a href="/login" class="btn">Login</a>

    </div>
    <img src="{{ asset('static/coverimg.png') }}" alt="Car" class="imgdetails" />
</body>

</html>