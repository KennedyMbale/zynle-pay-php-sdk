<?php

declare(strict_types=1);

namespace ZynlePay;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use ZynlePay\Exception\ApiException;
use ZynlePay\Exception\InvalidConfigurationException;

class Client
{
    private const SANDBOX_URL = 'https://sandbox.zynlepay.com/zynlepay/jsonapi';
    private const PRODUCTION_URL = 'https://payments.zynlepay.com/zynlepay/jsonapi/';

    private GuzzleClient $httpClient;
    private array $auth;
    private string $baseUrl;

    /**
     * @throws InvalidConfigurationException
     */
    public function __construct(
        string $merchantId,
        string $apiId,
        string $apiKey,
        string $channel,
        string $serviceId = '1002',
        bool $sandbox = true,
        ?string $baseUrl = null
    ) {
        $this->validateConfiguration($merchantId, $apiId, $apiKey, $channel, $serviceId, $sandbox);

        $this->baseUrl = $baseUrl ?? ($sandbox ? self::SANDBOX_URL : self::PRODUCTION_URL);

        $this->auth = [
            'merchant_id' => $merchantId,
            'api_id' => $apiId,
            'api_key' => $apiKey,
            'service_id' => $serviceId,
            'channel' => $channel,
        ];

        $this->httpClient = new GuzzleClient([
            'base_uri' => $this->baseUrl,
            'timeout' => 30.0,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'ZynlePay-PHP-SDK/1.0',
            ],
        ]);
    }

    /**
     * @throws InvalidConfigurationException
     */
    private function validateConfiguration(
        string $merchantId,
        string $apiId,
        string $apiKey,
        string $channel,
        string $serviceId,
        bool $sandbox
    ): void {
        if (empty($merchantId)) {
            throw new InvalidConfigurationException('Merchant ID cannot be empty');
        }
        if (empty($apiId)) {
            throw new InvalidConfigurationException('API ID cannot be empty');
        }
        if (empty($apiKey)) {
            throw new InvalidConfigurationException('API Key cannot be empty');
        }
        if (empty($channel)) {
            throw new InvalidConfigurationException('Channel cannot be empty');
        }
        if (empty($serviceId)) {
            throw new InvalidConfigurationException('Service ID cannot be empty');
        }
    }

    /**
     * @throws ApiException
     */
    public function request(string $method, array $data, string $endpoint = ''): array
    {
        $payload = [
            'auth' => $this->auth,
            'data' => $data,
            'userdata' => [
                'udf1' => '',
                'udf2' => '',
                'udf3' => '',
                'udf4' => '',
                'udf5' => '',
            ],
        ];

        try {
            $response = $this->httpClient->post('', [
                'json' => $payload,
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ApiException('Invalid JSON response from API');
            }

            return $responseData;
        } catch (RequestException $e) {
            $message = 'API request failed';
            if ($e->hasResponse()) {
                $responseBody = $e->getResponse()->getBody()->getContents();
                $message .= ': ' . $responseBody;
            } else {
                $message .= ': ' . $e->getMessage();
            }

            throw new ApiException($message, $e->getCode(), $e);
        } catch (\Exception $e) {
            throw new ApiException('Unexpected error: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getAuth(): array
    {
        return $this->auth;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}
