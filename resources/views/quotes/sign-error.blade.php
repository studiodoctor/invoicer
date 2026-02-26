<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quote Cannot Be Signed</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    <div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
                <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <h2 class="mt-6 text-3xl font-bold text-gray-900">Cannot Sign Quote</h2>
            <p class="mt-2 text-sm text-gray-600">{{ $message }}</p>
            <div class="mt-8 bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-900">Quote Details</h3>
                <dl class="mt-4 space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Quote Number</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $quote->quote_number }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Status</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $quote->status->label() }}</dd>
                    </div>
                    @if($quote->signed_at)
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Signed On</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $quote->signed_at->format('M d, Y') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            <p class="mt-6 text-sm text-gray-500">
                If you believe this is an error, please contact us.
            </p>
        </div>
    </div>
</body>
</html>
