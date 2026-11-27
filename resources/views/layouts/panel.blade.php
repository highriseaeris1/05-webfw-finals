<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Panel | Lost and Found Fasilkom')</title>

        @fonts

        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-[#F4F6FA] text-[#1B2430] antialiased">
        <div class="flex min-h-screen flex-col md:flex-row">
            @include('partials.sidebar')

            <main class="min-w-0 flex-1 px-4 py-6 sm:px-8 sm:py-7">
                @include('partials.flash')

                @yield('content')
            </main>
        </div>
    </body>
</html>
