<?php

namespace App\Service;

use App\Entity\Ad;
use App\Entity\AdImage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface as Client;
use Twig\Environment as TwigEnvironment;

class AdService
{
    // private $em;
    // private $validator;
    // private $httpClient;
    // private $twig;

    public function __construct(
        private ParameterBagInterface $parameterBag,
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
        private Client $httpClient,
        private TwigEnvironment $twig,
    ) {
        $this->em = $em;
        $this->validator = $validator;
    }

    public function createAd(array $request, array $adImagesData, $user, $adCategory)
    {
        $ad = new Ad();

        $ad->setTitle($request['title']);
        $ad->setDescription($request['description']);
        $ad->setPrice($request['price']);
        $ad->setAdCity($request['city']);
        $ad->setAdState($request['state']);
        $ad->setAdCountry($request['country']);
        $ad->setStatus('Pending Approval');
        $ad->setCategory($adCategory);
        $ad->setUser($user);
        $ad->setCreatedAt();

        $errors = $this->validator->validate($ad);

        if (count($errors) === 0) {
            $uploadDirectory = $this->parameterBag->get('kernel.project_dir').'/public/uploads';
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0777, true);
            }
            // dd($uploadDirectory);
            foreach ($adImagesData as $imageData) {
                $originalFilename = pathinfo($imageData->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageData->guessExtension();
                try {
                    $imageData->move(
                        $uploadDirectory,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                    // $requestStack->getSession()->getFlashBag()->add('error', 'System Encountered Error while Uploading Ad Images!');
                    throw new \RuntimeException('Error uploading ad images: '.$e->getMessage());
                }
                $adImageUrl = '/uploads/'.$newFilename;
                $adImage = new AdImage();
                $adImage->setAdImageName($newFilename);
                $adImage->setAdImagePath($adImageUrl);
                $adImage->setAd($ad);
                $adImage->setCreatedAt();
                $this->em->persist($adImage);
            }

            $activitylog = $ad->getUser()->getFullName().' Posted a new ad: '.$ad->getTitle().'at:'.$ad->getCreatedAt()->format('l dS F Y');
            $ad->getUser()->logActivity((string) $activitylog);

            $this->em->persist($ad);
            $this->em->flush();

            return $ad;
        }

        $validationErrors = [];
        foreach ($errors as $error) {
            $propertyPath = $error->getPropertyPath();
            $validationErrors[$propertyPath] = $error->getMessage();
        }

        return $validationErrors;
    }

    public function editAd(Ad $ad, array $request)
    {
        return $ad;
    }
}