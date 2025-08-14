<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\EbillsToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EbillsService
{
    protected $client;
    protected $baseUrl;
    protected $username;
    protected $password;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = env('EBILLS_API_URL');
        $this->username = env('EBILLS_USERNAME');
        $this->password = env('EBILLS_PASSWORD');

        Log::info('EbillsService initialized', [
            'baseUrl' => $this->baseUrl,
            'username' => $this->username,
            'password' => $this->password ? '****' : null
        ]);
    
    }

    public function getToken()
    {
        $token = EbillsToken::first();

        if ($token && Carbon::now()->lessThan($token->expires_at)) {
            return $token->token;
        }

        return $this->refreshToken();
    }

    public function refreshToken()
    {
        try {
            $response = $this->client->post("{$this->baseUrl}/jwt-auth/v1/token", [
                'json' => [
                    'username' => $this->username,
                    'password' => $this->password,
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            $token = $data['token'];

            EbillsToken::updateOrCreate(
                ['id' => 1],
                [
                    'token' => $token,
                    'expires_at' => Carbon::now()->addDays(7),
                ]
            );

            return $token;
        } catch (\Exception $e) {
            Log::error('eBills Token Refresh Failed: ' . $e->getMessage());
            throw new \Exception('Unable to authenticate with eBills API');
        }
    }

    public function makeApiRequest($endpoint, $data = [], $method = 'POST')
    {
        $token = $this->getToken();
        try {
            $options = [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type' => 'application/json',
                ],
            ];
            if ($method === 'POST') {
                $options['json'] = $data;
            }
            $response = $this->client->request($method, "{$this->baseUrl}/{$endpoint}", $options);
            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 401) {
                $this->refreshToken();
                return $this->makeApiRequest($endpoint, $data, $method); // Retry with new token
            }
            throw $e;
        }
    }
}