<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Load Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    @vite(['resources/scss/app.scss', 'resources/css/app.css', 'resources/js/app.js'])
    <title>Hotelkamerschoonmaakplan - login</title>
</head>

<body>
    <div id="login">
        <div class="sphere top"></div>
        <div class="sphere left"></div>
        <div class="sphere middle"></div>
        <div class="circle top-right">
            <div class="inner-circle"></div>
        </div>
        <div class="circle bottom">
            <div class="inner-circle"></div>
        </div>
        <div class="login-form">
            <div class="title">
                <img src="{{ asset('images/HSP-logo.png') }}" alt="Examinator Logo">
                <h1>Hotelkamerschoonmaakplan</h1>
            </div>
            @if (Session::has('error'))
                <div class="alert alert-danger" role="alert">
                    {{ Session::get('error') }}
                </div>
            @endif
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="input">
                    <label for="email">Gebruikersnaam</label>
                    <input type="email" name="email" id="email" placeholder="Vul hier je e-mailadres in...">
                </div>
                <div class="input">
                    <label for="password">Wachtwoord</label>
                    <input type="password" name="password" id="password" placeholder="Vul hier je wachtwoord in...">
                </div>
                <button>Login</button>
        </div>
        </form>
    </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>

</html>
