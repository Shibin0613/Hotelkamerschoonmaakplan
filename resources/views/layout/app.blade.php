<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- Load Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Load Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Load jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Load assets (Vite) -->
    @vite(['resources/scss/app.scss', 'resources/css/app.css', 'resources/js/app.js'])
    <title>Hotelkamerschoonmaakplan</title>
</head>

<body>
    <div class="page-container">
        <div id="sidebar">
            <div>
                <div class="logo" id="logo">
                    <img src="images/HSP-logo.png" alt="HSP Logo">
                </div>
                <ul>
                    @if (Auth::user()->role === 1)
                        <li><a class="link" href="/planning" title="Planning"><i class="fa-solid fa-calendar-days"></i></a></li>
                        <li><a class="link" href="/houses" title="Houses"><i class="fas fa-home"></i></a></li>
                        <li><a class="link" href="/damages" title="Damages"><i class="fas fa-house-damage"></i></a></li>
                        <li><a class="link" href="/gebruikers" title="Gebruikers"><i class="fas fa-user"></i></a></li>
                    @else
                    <li><a class="link" href="/planning" title="Planning"><i class="fa-solid fa-calendar-days"></i></a></li>
                    @endif
                </ul>
            </div>
            <div>
                <ul>
                    <li><a class="link" href="/logout" title="Uitloggen"><i class="fa-solid fa-arrow-right-from-bracket"></i></a></li>
                </ul>
            </div>
        </div>
        <main>
            @yield('content')
        </main>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</html>
