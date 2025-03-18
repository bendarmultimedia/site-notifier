<?php

namespace App\Scraper;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;

final class WebScraper
{
    private const URL_TO_CHECK = 'https://www.example.com';
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(['verify' => false]);
    }

    public function getContentByCss(string $cssSelector, ?string $url = null): array
    {
        try {
            $checkedUrl = $url ?? self::URL_TO_CHECK;
            $response = $this->client->get($checkedUrl);
            $html = (string)$response->getBody();

            $crawler = new Crawler($html);
            return $crawler->filter($cssSelector)->each(fn(Crawler $node) => $node->text());
        } catch (GuzzleException $e) {
            return [];
        }
    }

    public function contentExists(string $selector, string $url): bool
    {
        $content = $this->getContentByCss($selector, $url);

        return !empty($content);
    }
}