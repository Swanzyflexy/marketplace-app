<?php

namespace App\Twig;

use App\Service\CountryService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CountryExtension extends AbstractExtension
{
    private CountryService $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('countries', [$this, 'getCountries']),
        ];
    }

    public function getCountries(): array
    {
        return $this->countryService->getCountries();
    }
}