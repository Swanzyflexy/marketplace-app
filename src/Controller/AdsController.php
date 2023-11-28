<?php

namespace App\Controller;

use App\Entity\Ads;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AdsController extends AbstractController
{
    /**
     * Generates the function comment for the given function body in a markdown code block with the correct language syntax.
     *
     * @return JsonResponse
     */
    #[Route('/ads', name: 'app_ads')]
    public function index(): JsonResponse
    {
        $ads = new Ads;
        $ads->setTitle('Brand New Iphone 15 Pro');
        $ads->setPrice(1000);

        return $this->json([
            'Title' => $ads->getTitle(),
        ]);
    }

    /**
     * Creates ads and returns a JSON response.
     *
     * @return JsonResponse
     */
    public function createAds(): JsonResponse
    {
        $ads = new Ads;
        $ads->setTitle('Brand New Iphone 15 Pro');
        $ads->setPrice(1000);

        return $this->json($ads);
    }

    /**
     * Edit the ads.
     *
     * @return JsonResponse
     */
    public function editAds(): JsonResponse
    {
        $ads = new Ads;
        $ads->setTitle('Brand New Iphone 15 Pro');
        $ads->setPrice(1000);

        return $this->json($ads);
    }

    /**
     * Delete ads from the system.
     *
     * This function deletes ads from the system by creating a new instance of the Ads class,
     * setting the title to 'Brand New Iphone 15 Pro', and setting the price to 1000.
     *
     * @throws Some_Exception_Class If there is an error deleting the ads.
     * @return void
     */
    public function deleteAds(): JsonResponse
    {
        $ads = new Ads;
        $ads->setTitle('Brand New Iphone 15 Pro');
        $ads->setPrice(1000);

        return $this->json($ads);
    }
}