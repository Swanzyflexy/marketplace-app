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
    private function uploadAdImages(Ad $ad, array $adImagesData)
    {
        $uploadDirectory = $this->parameterBag->get('kernel.project_dir').'/public/uploads';
        foreach ($adImagesData as $imageData) {
            $originalFilename = pathinfo($imageData->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename.'-'.uniqid().'.'.$imageData->guessExtension();
            try {
                $imageData->move($uploadDirectory, $newFilename);
            } catch (FileException $e) {
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
        // Remove old images that are not in the new data
    }

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
        $ad->setCurrency($request['currency']);
        $ad->setUser($user);
        $ad->setCreatedAt();
        $ad->setUpdatedAt();

        $errors = $this->validator->validate($ad);

        if (count($errors) === 0) {
            if ($adImagesData !== null) {
                $this->uploadAdImages($ad, $adImagesData);
            }

            $activitylog = $ad->getUser()->getFullName().' Posted a new ad: '.$ad->getTitle().' at: '.$ad->getCreatedAt()->format('l dS F Y');
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

    public function editAd(Ad $ad, array $request, array $adImagesData = null, $adCategory)
    {
        $ad->setTitle($request['title'] ?? $ad->getTitle());
        $ad->setDescription($request['description'] ?? $ad->getDescription());
        $ad->setPrice($request['price'] ?? $ad->getPrice());
        $ad->setAdCity($request['city'] ?? $ad->getAdCity());
        $ad->setAdState($request['state'] ?? $ad->getAdState());
        $ad->setAdCountry($request['country'] ?? $ad->getAdCountry());
        $ad->setCurrency($request['currency'] ?? $ad->getCurrency());
        $ad->setStatus($ad->getStatus());
        $ad->setCategory($adCategory ?? $ad->getCategory());
        $ad->setUser($ad->getUser());
        $ad->setUpdatedAt();

        $errors = $this->validator->validate($ad);

        $existingImages = $ad->getAdImages()->toArray();

        if (count($errors) === 0) {
            // Update AdImages if new data is provided
            if ($adImagesData !== null) {
                $this->uploadAdImages($ad, $adImagesData);
            }

            foreach ($existingImages as $existingImage) {
                if (!in_array($existingImage, $ad->getAdImages()->toArray(), true)) {
                    $this->em->remove($existingImage);
                }
            }

            $activitylog = $ad->getUser()->getFullName().' Updated an Ad: '.$ad->getTitle().' at: '.$ad->getUpdatedAt()->format('l dS F Y');
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

    public function updateAdStatus(Ad $ad, $status)
    {
        switch ($status) {
            case 'Approved':
                $ad->setStatus('Approved');
                $ad->setUpdatedAt();
                $this->em->persist($ad);
                $activitylog = 'Yay!!! your Ad: '.$ad->getTitle().' was approved at: '.$ad->getCreatedAt()->format('l dS F Y');
                $ad->getUser()->logActivity((string) $activitylog);
                $this->em->flush();

                return $ad;
                break;
            case 'Rejected':
                $ad->setStatus('Rejected');
                $ad->setUpdatedAt();
                $this->em->persist($ad);
                $activitylog = 'Uh Oh! your Ad '.$ad->getTitle().' was rejected at: '.$ad->getCreatedAt()->format('l dS F Y');
                $ad->getUser()->logActivity((string) $activitylog);
                $this->em->flush();

                return $ad;
                break;
            case 'Sold':
                $ad->setStatus('Sold');
                $ad->setUpdatedAt();
                $this->em->persist($ad);
                $activitylog = 'You sold an Item '.$ad->getTitle().' at: '.$ad->getCreatedAt()->format('l dS F Y');
                $ad->getUser()->logActivity((string) $activitylog);
                $this->em->flush();

                return $ad;
                break;
            case 'Pending Approval':
                $ad->setStatus('Pending Approval');
                $ad->setUpdatedAt();
                $this->em->persist($ad);
                $activitylog = 'Your Ad: '.$ad->getTitle().' is pending approval at: '.$ad->getCreatedAt()->format('l dS F Y');
                $ad->getUser()->logActivity((string) $activitylog);
                $this->em->flush();

                return $ad;
                break;
            default:
                $validationErrors[] = new \Exception('Invalid status');

                return $validationErrors;
        }
    }

    public function deleteAd(Ad $ad)
    {
        $this->em->remove($ad);
        $this->em->flush();
        $activitylog = 'Your Ad: '.$ad->getTitle().' was deleted at: '.$ad->getCreatedAt()->format('l dS F Y');
        $ad->getUser()->logActivity((string) $activitylog);
        return $ad;
    }
}