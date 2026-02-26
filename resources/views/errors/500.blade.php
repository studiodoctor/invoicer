<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Server Error - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="h-full">
    <div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full text-center">
            <p class="text-9xl font-bold text-red-600">500</p>
            <h1 class="mt-4 text-3xl font-bold text-gray-900">Server error</h1>
            <p class="mt-2 text-gray-600">Something went wrong on our end. Please try again later.</p>
            <div class="mt-6">
                <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700">
                    Go back home
                </a>
            </div>
        </div>
    </div>
</body>
</html>
