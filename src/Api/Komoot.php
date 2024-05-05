<?php

declare(strict_types=1);

namespace Woeler\KomootPhp\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use Woeler\KomootPhp\Enums\PrivacySetting;
use Woeler\KomootPhp\Enums\Sport;
use Woeler\KomootPhp\Enums\TourType;

class Komoot
{
    private Client $client;
    private CookieJar $cookies;
    private ?int $userid = null;

    public function __construct(private readonly string $email, private readonly string $password, ?Client $client = null)
    {
        $this->client = $client ?? new Client();
        $this->cookies = new CookieJar();
    }

    public function login(): void
    {
        $response = $this->client->post('https://account.komoot.com/v1/signin', [
            'json' => ['email' => $this->email, 'password' => $this->password, 'reason' => 'header'],
        ]);

        $headerSetCookies = $response->getHeader('Set-Cookie');

        foreach ($headerSetCookies as $header) {
            $this->cookies->setCookie(SetCookie::fromString($header));
        }

        $this->setAccountId();

        $response = $this->client->get('https://account.komoot.com/actions/transfer', [
            'cookies' => $this->cookies,
            'query' => [
                'type' => 'signin',
                'reason' => 'header',
            ],
        ]);

        $headerSetCookies = $response->getHeader('Set-Cookie');

        foreach ($headerSetCookies as $header) {
            $this->cookies->setCookie(SetCookie::fromString($header));
        }
    }

    private function setAccountId(): void
    {
        $response = json_decode($this->client->get('https://account.komoot.com/api/account/v1/session', [
            'cookies' => $this->cookies,
            'query' => ['hl' => 'en'],
        ])->getBody()->getContents(), true);

        $this->userid = (int) $response['_embedded']['profile']['username'];
    }

    public function getTours(int $page = 0, int $limit = 50, ?TourType $type = null): array
    {
        $params = ['page' => $page, 'limit' => $limit];

        if ($type !== null) {
            $params += ['type' => $type->value];
        }

        return json_decode($this->client->get('https://www.komoot.com/api/v007/users/'.$this->userid.'/tours/', [
            'cookies' => $this->cookies,
            'query' => $params,
        ])->getBody()->getContents(), true);
    }

    public function getAllTours(?TourType $type = null): array
    {
        $page = 0;
        $all = [];
        while (true) {
            $tours = $this->getTours($page, 50, $type);
            $all = array_merge($all, $tours['_embedded']['tours']);

            if ($page + 1 === $tours['page']['totalPages']) {
                break;
            }
            $page++;
        }

        return $all;
    }

    public function getTour(int $tourId): array
    {
        return json_decode($this->client->get('https://www.komoot.com/api/v007/tours/'.$tourId, [
            'cookies' => $this->cookies,
        ])->getBody()->getContents(), true);
    }

    public function getTourGpx(int $tourId): string
    {
        return $this->client->get('https://www.komoot.com/api/v007/tours/'.$tourId.'.gpx', [
            'cookies' => $this->cookies,
        ])->getBody()->getContents();
    }

    public function deleteTour(int $tourId): void
    {
        $this->client->delete('https://www.komoot.com/api/v007/tours/'.$tourId, [
            'cookies' => $this->cookies,
        ]);
    }

    public function renameTour(int $tourId, string $name): array
    {
        return json_decode($this->client->patch('https://www.komoot.com/api/v007/tours/'.$tourId, [
            'cookies' => $this->cookies,
            'json' => ['name' => $name],
        ])->getBody()->getContents(), true);
    }

    public function changeTourPrivacy(int $tourId, PrivacySetting $privacy): array
    {
        return json_decode($this->client->patch('https://www.komoot.com/api/v007/tours/'.$tourId, [
            'cookies' => $this->cookies,
            'json' => ['status' => $privacy->value],
        ])->getBody()->getContents(), true);
    }

    public function changeTourSport(int $tourId, Sport $sport): array
    {
        return json_decode($this->client->patch('https://www.komoot.com/api/v007/tours/'.$tourId, [
            'cookies' => $this->cookies,
            'json' => ['sport' => $sport->value],
        ])->getBody()->getContents(), true);
    }

    public function getUser(int $userId): array
    {
        return json_decode($this->client->get('https://www.komoot.com/api/v007/users/'.$userId, [
            'cookies' => $this->cookies,
        ])->getBody()->getContents(), true);
    }

    public function getSelfUser(): array
    {
        return $this->getUser($this->userid);
    }

    public function getCollection(int $collectionId): array
    {
        return json_decode($this->client->get('https://www.komoot.com/api/v007/collections/'.$collectionId, [
            'cookies' => $this->cookies,
        ])->getBody()->getContents(), true);
    }

    public function getCollectionTours(int $collectionId): array
    {
        return json_decode($this->client->get('https://www.komoot.com/api/v007/collections/'.$collectionId.'/compilation/', [
            'cookies' => $this->cookies,
        ])->getBody()->getContents(), true);
    }

    public function renameCollection(int $collectionId, string $name): array
    {
        return json_decode($this->client->patch('https://www.komoot.com/api/v007/collections/'.$collectionId, [
            'cookies' => $this->cookies,
            'json' => ['name' => $name],
        ])->getBody()->getContents(), true);
    }

    public function changeCollectionDescription(int $collectionId, ?string $description): array
    {
        return json_decode($this->client->patch('https://www.komoot.com/api/v007/collections/'.$collectionId, [
            'cookies' => $this->cookies,
            'json' => ['intro_plain' => $description ?? ''],
        ])->getBody()->getContents(), true);
    }

    public function changeCollectionPrivacy(int $collectionId, PrivacySetting $privacy): array
    {
        return json_decode($this->client->patch('https://www.komoot.com/api/v007/collections/'.$collectionId, [
            'cookies' => $this->cookies,
            'json' => ['status' => $privacy->value],
        ])->getBody()->getContents(), true);
    }

    public function deleteCollection(int $collectionId): void
    {
        $this->client->delete('https://www.komoot.com/api/v007/collections/'.$collectionId, [
            'cookies' => $this->cookies,
        ]);
    }

    public function customRequest(string $endpoint, array $guzzleConfig = [], string $method = 'GET'): array
    {
        if (! in_array(strtolower($method), ['get', 'delete', 'head', 'options', 'patch', 'post', 'put'])) {
            throw new \InvalidArgumentException('Invalid HTTP method given', 1714987334);
        }

        return json_decode($this->client->{strtolower($method)}($endpoint, [
            'cookies' => $this->cookies,
        ] + $guzzleConfig)->getBody()->getContents(), true);
    }
}
