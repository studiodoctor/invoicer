<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign Quote {{ $quote->quote_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    <div class="min-h-full py-12 px-4 sm:px-6 lg:px-8" x-data="signatureForm()">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Quote {{ $quote->quote_number }}</h1>
                <p class="mt-2 text-sm text-gray-600">From {{ $settings->company_name }}</p>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden mb-8">
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Prepared For</h4>
                            <p class="mt-1 text-sm font-medium text-gray-900">{{ $quote->client->company_name }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">Valid Until</div>
                            <div class="text-sm font-medium text-gray-900">{{ $quote->expiry_date->format('M d, Y') }}</div>
                        </div>
                    </div>

                    <table class="min-w-full mb-6">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="py-3 text-left text-sm font-semibold text-gray-900">Description</th>
                                <th class="py-3 text-right text-sm font-semibold text-gray-900">Qty</th>
                                <th class="py-3 text-right text-sm font-semibold text-gray-900">Price</th>
                                <th class="py-3 text-right text-sm font-semibold text-gray-900">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quote->items as $item)
                            <tr class="border-b border-gray-100">
                                <td class="py-4 text-sm text-gray-900">{{ $item->description }}</td>
                                <td class="py-4 text-right text-sm text-gray-500">{{ number_format($item->quantity, 2) }}</td>
                                <td class="py-4 text-right text-sm text-gray-500">R{{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-4 text-right text-sm font-medium text-gray-900">R{{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="flex justify-end">
                        <div class="w-64 space-y-2">
                            <div class="flex justify-between text-sm"><span class="text-gray-500">Subtotal</span><span>R{{ number_format($quote->subtotal, 2) }}</span></div>
                            @if($quote->tax_amount > 0)
                            <div class="flex justify-between text-sm"><span class="text-gray-500">Tax ({{ $quote->tax_rate }}%)</span><span>R{{ number_format($quote->tax_amount, 2) }}</span></div>
                            @endif
                            <div class="flex justify-between text-lg font-semibold border-t pt-2"><span>Total</span><span>R{{ number_format($quote->total, 2) }} {{ $quote->currency }}</span></div>
                        </div>
                    </div>

                    @if($quote->notes)<div class="mt-6 pt-6 border-t"><h4 class="text-sm font-medium text-gray-500">Notes</h4><p class="mt-1 text-sm text-gray-900">{{ $quote->notes }}</p></div>@endif
                    @if($quote->terms)<div class="mt-4"><h4 class="text-sm font-medium text-gray-500">Terms & Conditions</h4><p class="mt-1 text-sm text-gray-900">{{ $quote->terms }}</p></div>@endif
                </div>
            </div>

            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Accept & Sign Quote</h3>
                    <form action="{{ route('quotes.process-sign', $quote->sign_token) }}" method="POST" @submit.prevent="submitForm">
                        @csrf
                        <div class="mb-6">
                            <label for="signer_name" class="block text-sm font-medium text-gray-700">Your Name *</label>
                            <input type="text" name="signer_name" id="signer_name" x-model="signerName" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Your Signature *</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4">
                                <canvas id="signature-pad" class="w-full bg-white rounded" style="height: 200px; touch-action: none;"></canvas>
                                <input type="hidden" name="signature" x-ref="signatureInput">
                            </div>
                            <button type="button" @click="clearSignature" class="mt-2 text-sm text-gray-600 hover:text-gray-900">Clear signature</button>
                        </div>

                        <div class="mb-6">
                            <label class="flex items-start">
                                <input type="checkbox" x-model="agreed" required class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 mt-1">
                                <span class="ml-2 text-sm text-gray-600">I agree to the terms and conditions outlined in this quote and authorize the work to proceed.</span>
                            </label>
                        </div>

                        <button type="submit" :disabled="!canSubmit" class="w-full rounded-md bg-primary-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 disabled:opacity-50 disabled:cursor-not-allowed">Accept & Sign Quote</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script>
    function signatureForm() {
        return {
            signerName: '',
            agreed: false,
            signaturePad: null,

            init() {
                const canvas = document.getElementById('signature-pad');
                this.signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)'
                });

                const resizeCanvas = () => {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext('2d').scale(ratio, ratio);
                    this.signaturePad.clear();
                };

                window.addEventListener('resize', resizeCanvas);
                resizeCanvas();
            },

            get canSubmit() {
                return this.signerName.trim() !== '' && 
                       this.agreed && 
                       this.signaturePad && 
                       !this.signaturePad.isEmpty();
            },

            clearSignature() {
                this.signaturePad.clear();
            },

            submitForm(event) {
                if (!this.canSubmit) {
                    alert('Please fill in all required fields and sign the document.');
                    return;
                }
                this.$refs.signatureInput.value = this.signaturePad.toDataURL();
                event.target.submit();
            }
        }
    }
    </script>
</body>
</html>
