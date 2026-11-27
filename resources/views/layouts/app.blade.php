<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Lost and Found Fasilkom')</title>

        @fonts

        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-[#F4F6FA] text-[#1B2430] antialiased">
        @yield('content')
    </body>
</html>
