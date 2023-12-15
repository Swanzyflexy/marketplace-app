<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;

class CountryService
{
    private array $countries = [];

    public function getCountries(): array
    {
        $gistUrl = 'https://gist.githubusercontent.com/puzzo-dev/63d33cfb842ff15a9a47de4e7055dfac/raw/83c6cf21d82d8f97deb38fbb88ee40f633ab6f89/countries.json';

        try {
            $httpClient = HttpClient::create();
            $response = $httpClient->request('GET', $gistUrl);

            $statusCode = $response->getStatusCode();
            if ($statusCode === Response::HTTP_OK) {
                return $response->toArray();
            } else {
                // Handle error - the request was not successful
                return [];
            }
        } catch (\Exception $e) {
            // Handle exception - there was an issue with the request

            return [];
        }

        return $this->countries;
    }
}