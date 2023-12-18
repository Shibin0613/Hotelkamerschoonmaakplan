<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <title>Login</title>
    @vite(['resources/scss/app.scss', 'resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div id="login">
    <div class="overlay">
        <div class="login-content">
            <div class="logo-container">
                <div class="logo">
                    <h1>Hotelkamer<br>schoonmaak<br>plan</h1>
                    <div class="left"></div>
                    <div class="bottom"></div>
                </div>
            </div>
            @if ($errors->any())
                <div class="error-message">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            <form action="{{ route('loginPost') }}" method="POST">
                @csrf
                <div class="input">
                    <label for="gebruikersnaam">Gebruikersnaam</label>
                    <input type="text" name="gebruikersnaam" id="gebruikersnaam" placeholder="Vul hier je gebruikersnaam in...">
                </div>
                <div class="input">
                    <label for="password">Wachtwoord</label>
                    <input type="password" name="password" id="password" placeholder="Vul hier je wachtwoord in...">
                </div>
                <button type="submit">Log in</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>