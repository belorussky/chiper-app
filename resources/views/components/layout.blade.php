<!DOCTYPE html>
<html lang="en" data-theme="lofi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($title) ? $title . ' - Chirper' : 'Chirper' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <link herf="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <body class="min-h-screen flex flex-col bg-base-200 font-sans">
        <nav class="bg-base-100 navbar">
            <div class="navbar-start">
                <a href="/" class="btn btn-ghost text-xl">Chirper</a>
            </div>
            <div class="navbar-end gap-2">
                <a href="#" class="btn btn-ghost btn-sm">Sign In</a>
                <a href="#" class="btn btn-primary btn-sm">Sign Up</a>
            </div>
        </nav>

        <main class="flex-1 container mx-auto px-4 py-8">
            {{ $slot }}
        </main>

        <footer class="footer footer-center bg-base-300 text-base-content p-5 text-xs">
            <div>
                <p>&copy; {{ date('Y') }} Chirper. All rights reserved.</p>
            </div>
        </footer>
    </body>
</body>
</html>