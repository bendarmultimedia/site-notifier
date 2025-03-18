<?php

namespace App\Telegram;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class API
{
    private const BOT_API_TOKEN = 'CHANGE ME';
    private const BASE_URI = 'https://api.telegram.org';
    private const CHANNEL_ID = '';
    private HttpClientInterface $client;

    private string $currentBotToken = '';

    public function __construct()
    {
        $this->client = HttpClient::create(['base_uri' => self::BASE_URI]);
        $this->currentBotToken = self::BOT_API_TOKEN;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendMessage(string $message, ?string $channelId): ResponseInterface
    {
        $channelId = $channelId ?? self::CHANNEL_ID;
        return $this->get('sendMessage', [
            'chat_id' => $channelId,
            'text' => $message
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function get(string $endpoint, array $queryArray): ResponseInterface
    {
        return $this->client->request('GET', $this->endpoint($endpoint), [
            'query' => $queryArray,
        ]);
    }

    private function endpoint(string $endpoint): string
    {
        return '/bot'.$this->currentBotToken.'/'.$endpoint;
    }

    public function getCurrentBotToken(): string
    {
        return $this->currentBotToken;
    }

    public function setCurrentBotToken(string $currentBotToken): void
    {
        $this->currentBotToken = $currentBotToken;
    }
}