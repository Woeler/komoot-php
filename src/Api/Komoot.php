<?php

declare(strict_types=1);

namespace Woeler\KomootPhp\Api;

use GuzzleHttp\Client;
use Woeler\KomootPhp\Enums\PrivacySetting;
use Woeler\KomootPhp\Enums\Sport;
use Woeler\KomootPhp\Enums\TourType;

class Komoot
{
    private Client $client;

    public function __construct(private readonly string $email, private readonly string $password, private readonly int $userid)
    {
        $this->client = $client ?? new Client();
    }

    public function getUserId(): ?int
    {
        return $this->userid;
    }

    public function getTours(int $page = 0, int $limit = 50, ?TourType $type = null): array
    {
        $params = ['page' => $page, 'limit' => $limit];

        if ($type !== null) {
            $params += ['type' => $type->value];
        }

        return json_decode($this->client->get('https://www.komoot.com/api/v007/users/'.$this->userid.'/tours/', [
            'auth' => [$this->email, $this->password],
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
            'auth' => [$this->email, $this->password],
        ])->getBody()->getContents(), true);
    }

    public function getTourGpx(int $tourId): string
    {
        return $this->client->get('https://www.komoot.com/api/v007/tours/'.$tourId.'.gpx', [
            'auth' => [$this->email, $this->password],
        ])->getBody()->getContents();
    }

    public function getTourPhotos(int $tourId): array
    {
        return json_decode($this->client->get('https://www.komoot.com/api/v007/tours/'.$tourId.'/cover_images/', [
            'auth' => [$this->email, $this->password],
        ])->getBody()->getContents(), true);
    }

    public function deleteTour(int $tourId): void
    {
        $this->client->delete('https://www.komoot.com/api/v007/tours/'.$tourId, [
            'auth' => [$this->email, $this->password],
        ]);
    }

    public function renameTour(int $tourId, string $name): array
    {
        return json_decode($this->client->patch('https://www.komoot.com/api/v007/tours/'.$tourId, [
            'auth' => [$this->email, $this->password],
            'json' => ['name' => $name],
        ])->getBody()->getContents(), true);
    }

    public function changeTourPrivacy(int $tourId, PrivacySetting $privacy): array
    {
        return json_decode($this->client->patch('https://www.komoot.com/api/v007/tours/'.$tourId, [
            'auth' => [$this->email, $this->password],
            'json' => ['status' => $privacy->value],
        ])->getBody()->getContents(), true);
    }

    public function changeTourSport(int $tourId, Sport $sport): array
    {
        return json_decode($this->client->patch('https://www.komoot.com/api/v007/tours/'.$tourId, [
            'auth' => [$this->email, $this->password],
            'json' => ['sport' => $sport->value],
        ])->getBody()->getContents(), true);
    }

    public function getUser(int $userId): array
    {
        return json_decode($this->client->get('https://www.komoot.com/api/v007/users/'.$userId, [
            'auth' => [$this->email, $this->password],
        ])->getBody()->getContents(), true);
    }

    public function getSelfUser(): array
    {
        return $this->getUser($this->userid);
    }

    public function getCollection(int $collectionId): array
    {
        return json_decode($this->client->get('https://www.komoot.com/api/v007/collections/'.$collectionId, [
            'auth' => [$this->email, $this->password],
        ])->getBody()->getContents(), true);
    }

    public function getCollectionTours(int $collectionId): array
    {
        return json_decode($this->client->get('https://www.komoot.com/api/v007/collections/'.$collectionId.'/compilation/', [
            'auth' => [$this->email, $this->password],
        ])->getBody()->getContents(), true);
    }

    public function renameCollection(int $collectionId, string $name): array
    {
        return json_decode($this->client->patch('https://www.komoot.com/api/v007/collections/'.$collectionId, [
            'auth' => [$this->email, $this->password],
            'json' => ['name' => $name],
        ])->getBody()->getContents(), true);
    }

    public function changeCollectionDescription(int $collectionId, ?string $description): array
    {
        return json_decode($this->client->patch('https://www.komoot.com/api/v007/collections/'.$collectionId, [
            'auth' => [$this->email, $this->password],
            'json' => ['intro_plain' => $description ?? ''],
        ])->getBody()->getContents(), true);
    }

    public function changeCollectionPrivacy(int $collectionId, PrivacySetting $privacy): array
    {
        return json_decode($this->client->patch('https://www.komoot.com/api/v007/collections/'.$collectionId, [
            'auth' => [$this->email, $this->password],
            'json' => ['status' => $privacy->value],
        ])->getBody()->getContents(), true);
    }

    public function deleteCollection(int $collectionId): void
    {
        $this->client->delete('https://www.komoot.com/api/v007/collections/'.$collectionId, [
            'auth' => [$this->email, $this->password],
        ]);
    }

    public function customRequest(string $endpoint, array $guzzleConfig = [], string $method = 'GET'): array
    {
        if (! in_array(strtolower($method), ['get', 'delete', 'head', 'options', 'patch', 'post', 'put'])) {
            throw new \InvalidArgumentException('Invalid HTTP method given', 1714987334);
        }

        return json_decode($this->client->{strtolower($method)}($endpoint, [
            'auth' => [$this->email, $this->password],
        ] + $guzzleConfig)->getBody()->getContents(), true);
    }
}
