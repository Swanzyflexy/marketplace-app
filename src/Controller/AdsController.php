<?php

namespace App\Controller;

use App\Entity\Ads;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdsController extends AbstractController
{
    /**
     * Generates the function comment for the given function body in a markdown code block with the correct language syntax.
     */
    #[Route('/ads', name: 'app_ads', methods: ['GET', 'HEAD'])]
    public function index(): Response
    {
        $ads = new Ads();
        $ads->setTitle('Brand New Iphone 15 Pro');
        $ads->setPrice(1000);
        $ads->setDescription('Brand New Iphone 15 Pro with 24mp tri-camera');

        return $this->render('index.html.twig', [
            'ads' => $ads,
        ]);
    }

    /**
     * Show Ads Form.
     */
    #[Route('/ads/create', name: 'app_ads_create', methods: ['GET', 'HEAD'])]
    public function showAdsForm(): Response
    {
        $ads = new Ads();
        $ads->setTitle('Brand New Iphone 15 Pro');
        $ads->setPrice(1000);

        return $this->render('addnewlisting.html.twig', [
            'ads' => $ads,
        // 'methods' => $methods,
        ]);
    }

    #[Route('/ads/create', name: 'app_ads_create', methods: ['POST'])]
    public function CreateAdsForm(): Response
    {
        $ads = new Ads();
        $ads->setTitle('Brand New Iphone 15 Pro');
        $ads->setPrice(1000);

        return $this->render('addnewlisting.html.twig', [
            'ads' => $ads,
        ]);
    }

    /**
     * Edit the ads.
     */
    public function editAds(): Response
    {
        $ads = new Ads();
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
     * @return void
     *
     * @throws Some_Exception_Class if there is an error deleting the ads
     */
    public function deleteAds(): Response
    {
        $ads = new Ads();
        $ads->setTitle('Brand New Iphone 15 Pro');
        $ads->setPrice(1000);

        return $this->render('index.html.twig', [
            'ads' => $ads,
        ]);
    }
}