<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PoleemploiClient
{
    private string $clientSecret;
    private string $clientId;

    const MAX_IMPORT_LOT = 149;

    public function __construct(HttpClientInterface $httpClient, string $clientId, string $clientSecret)
    {
        $this->httpClient = $httpClient;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    const SEARCH_URI = "https://api.pole-emploi.io/partenaire/offresdemploi/v2/offres/search";
    const CONNECTION = "https://entreprise.pole-emploi.fr/connexion/oauth2/access_token?realm=%2Fpartenaire";

    /**
     * @see https://pole-emploi.io/data/api/offres-emploi?tabgroup-api=documentation&doc-section=api-doc-section-caracteristiques
     */
    public function searchOffersByCity(int $cityCodeINSEE, ?string $bearer = "CZtYb7oleudz-OOGty5cuunMfqw", string $range = 'all'): array
    {
        $getEverything = false;
        if ($range == 'all') {
            $getEverything = true;
            $range = '0-' . self::MAX_IMPORT_LOT;
        }
        if ($bearer == null) {
            $token = $this->getAccessToken();
            if ($token['status'] >= 300) {
                dd($token['data']);
            } else {
                $bearer = $token['data'];
            }
        }

        try {
            $response = $this->httpClient->request('GET', self::SEARCH_URI, [
                'query' => ['commune' => $cityCodeINSEE, 'range' => $range],
                'headers' => ['Authorization' => 'Bearer ' . $bearer],
            ]);

            if ($getEverything) {
                $headers = $response->getHeaders();
                $headerData = $headers['content-range'][0];
    
                // offres 0-149/13280
                preg_match('#(\d*)-(\d*)/(\d*)#', $headerData, $matches);
                $from = (int)$matches[0];
                $to = (int)$matches[1];
                $max = (int)$matches[2];
                $data = json_decode($response->getContent(false),true)['resultats'];

                $to += self::MAX_IMPORT_LOT;
                $from += self::MAX_IMPORT_LOT;

                while ($to < $max) {
                    $response = $this->httpClient->request('GET', self::SEARCH_URI, [
                        'query' => ['commune' => $cityCodeINSEE, 'range' => $from . '-' . $to],
                        'headers' => ['Authorization' => 'Bearer ' . $bearer],
                    ]);
                    $dataTmp = json_decode($response->getContent(false))['resultats'];
                    $data = array_merge($data, $dataTmp);
                    $to += self::MAX_IMPORT_LOT;
                    $from += self::MAX_IMPORT_LOT;
                }
            }
        } catch (TransportExceptionInterface $e) {
        }
        return ['status' => $response->getStatusCode(), 'data' => $data];
    }

    /**
     * @see https://pole-emploi.io/data/documentation/utilisation-api-pole-emploi/generer-access-token
     */
    private function getAccessToken(): array
    {
        $body = "grant_type=client_credentials;client_id=" . $this->clientId . ";client_secret=" . $this->clientSecret . ";scope='api_offresdemploiv2 o2dsoffre'";
        $response = $this->httpClient->request('POST', self::CONNECTION, [
            'body' => $body,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => '*/*',
            ],
        ]);

        return [
            'status' => $response->getStatusCode(),
            'data' => $response->getContent(false),
        ];

    }
}
