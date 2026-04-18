<?php

namespace App\Services;

use App\Models\FeePayment;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class DarajaService
{
    public function initiateStkPush(FeePayment $payment, string $phoneNumber, ?float $amount = null, ?string $accountReference = null, ?string $description = null): array
    {
        $shortcode = config('services.daraja.shortcode');
        $passkey = config('services.daraja.passkey');

        if (! $shortcode || ! $passkey) {
            throw new RuntimeException('Daraja credentials are not configured.');
        }

        $amount = $this->normalizeAmount($amount ?? max(((float) $payment->amount_due) - ((float) $payment->amount_paid), 0));
        $timestamp = now('Africa/Nairobi')->format('YmdHis');
        $password = base64_encode($shortcode . $passkey . $timestamp);
        $callbackUrl = config('services.daraja.callback_url');

        if (! $callbackUrl) {
            throw new RuntimeException('Daraja callback URL is not configured.');
        }

        $payload = [
            'BusinessShortCode' => $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $this->normalizePhoneNumber($phoneNumber),
            'PartyB' => $shortcode,
            'PhoneNumber' => $this->normalizePhoneNumber($phoneNumber),
            'CallBackURL' => $callbackUrl,
            'AccountReference' => $accountReference ?: ('FEE-' . $payment->id),
            'TransactionDesc' => $description ?: 'School fee payment',
        ];

        $response = Http::acceptJson()
            ->withToken($this->accessToken())
            ->post($this->baseUrl() . '/mpesa/stkpush/v1/processrequest', $payload);

        if (! $response->successful()) {
            throw new RuntimeException('Daraja STK push failed: ' . $response->body());
        }

        return $response->json() ?? [];
    }

    public function accessToken(): string
    {
        $key = config('services.daraja.consumer_key');
        $secret = config('services.daraja.consumer_secret');

        if (! $key || ! $secret) {
            throw new RuntimeException('Daraja consumer credentials are not configured.');
        }

        $response = Http::withBasicAuth($key, $secret)
            ->get($this->baseUrl() . '/oauth/v1/generate', [
                'grant_type' => 'client_credentials',
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Unable to retrieve Daraja access token: ' . $response->body());
        }

        $token = $response->json('access_token');

        if (! $token) {
            throw new RuntimeException('Daraja access token was not returned.');
        }

        return $token;
    }

    protected function normalizePhoneNumber(string $phoneNumber): string
    {
        $digits = preg_replace('/\D+/', '', $phoneNumber) ?? '';

        if ($digits === '') {
            throw new RuntimeException('A valid phone number is required for Daraja.');
        }

        if (str_starts_with($digits, '0')) {
            return '254' . substr($digits, 1);
        }

        if (str_starts_with($digits, '254')) {
            return $digits;
        }

        if (str_starts_with($digits, '7') || str_starts_with($digits, '1')) {
            return '254' . $digits;
        }

        return $digits;
    }

    protected function normalizeAmount(float $amount): int
    {
        return max(1, (int) round($amount));
    }

    protected function baseUrl(): string
    {
        return config('services.daraja.environment') === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }
}
