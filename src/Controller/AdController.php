<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\AdCategory;
use App\Entity\AdImage;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface as Client;
use Twig\Environment as TwigEnvironment;

#[Route('/ad')]
class AdController extends AbstractController
{
    private $httpClient;
    private $twig;

    public function __construct(Client $httpClient, TwigEnvironment $twig)
    {
        $this->httpClient = $httpClient;
        $this->twig = $twig;
    }

    #[Route('/', name: 'app_ad_index', methods: ['GET'])]
    public function index(AdRepository $adRepository): Response
    {
        return $this->render('profile/listAds.html.twig', [
            'ads' => $adRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_ad_new', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $em
    ): Response {
        $gistUrl = 'https://gist.githubusercontent.com/puzzo-dev/63d33cfb842ff15a9a47de4e7055dfac/raw/83c6cf21d82d8f97deb38fbb88ee40f633ab6f89/countries.json';
        try {
            $response = $this->httpClient->request('GET', $gistUrl);
            $statusCode = $response->getStatusCode();
            if ($statusCode === Response::HTTP_OK) {
                $jsonData = $response->toArray();
                $this->twig->addGlobal('countries', $jsonData);
                // dd($request);
                // dd($request, $jsonData);
                // Now $jsonData contains your remote JSON data as an array
            } else {
                // Handle error - the request was not successful
                $jsonData = [];
            }
        } catch (\Exception $e) {
            // Handle exception - there was an issue with the request
            $jsonData = [];
        }
        $ad = new Ad();

        $categories = $em->getRepository(AdCategory::class)
            ->findBy(['parentCategory' => null]);

        if ($request->isMethod('POST')) {
            $ad->setTitle($request->request->get('title'));
            $ad->setDescription($request->request->get('description'));
            $ad->setPrice($request->request->get('price'));
            $ad->setAdCity($request->request->get('city'));
            $ad->setAdState($request->request->get('state'));
            $ad->setAdCountry($request->request->get('country'));
            $ad->setStatus('Pending Approval');
            $ad->setCategory($em->getRepository(AdCategory::class)->find($request->request->get('category')));
            $ad->setUser($this->getUser());
            $ad->setCreatedAt(new \DateTimeImmutable());
            $adImagesData = $request->files->get('ad_images');
            $errors = $validator->validate($ad);
            // $errors[] = null;
            if (count($errors) === 0) {
                $uploadDirectory = $this->getParameter('kernel.project_dir').'/public/uploads';
                dd($uploadDirectory);

                foreach ($adImagesData as $imageData) {
                    $originalFilename = pathinfo($imageData->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $originalFilename.'-'.uniqid().'.'.$imageData->guessExtension();
                    try {
                        $imageData->move(
                            $uploadDirectory,
                            $newFilename
                        );
                        $adImage = new AdImage();
                        $adImage->setAdImageName($newFilename);
                        $adImage->setAdImagePath($uploadDirectory);
                        $adImage->setAd($ad);
                        $adImage->setCreatedAt(new \DateTimeImmutable());
                        $em->persist($adImage);
                        $em->flush();
                    } catch (FileException $e) {
                        // Handle file upload error
                        $this->addFlash('error', 'System Encountered Error while creating Ad!');

                        return $this->redirectToRoute('app_ad_new');
                    }
                }
                $em->persist($ad);
                $em->flush();
                $this->addFlash('success', 'Ad created successfully!');

                return $this->redirectToRoute('app_ad_index');
            }

            $validationErrors = [];
            foreach ($errors as $error) {
                $propertyPath = $error->getPropertyPath();
                $validationErrors[$propertyPath] = $error->getMessage();
            }

            $this->addFlash('errors', $validationErrors);
            $request->attributes->set('_old_values', $request->request->all());
        }

        return $this->render('profile/addnewlisting.html.twig', [
            'adImages' => $ad->getAdImages(),
            'categories' => $categories,
            '_old_values' => $request->attributes->get('_old_values'),
        ]);
    }

    #[Route('/{id}', name: 'app_ad_show', methods: ['GET'])]
    public function show(Ad $ad): Response
    {
        return $this->render('ad/show.html.twig', [
            'ad' => $ad,
        ]);
    }

    // #[Route('/{id}/edit', name: 'app_ad_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Ad $ad, EntityManagerInterface $entityManager): Response
    // {
    //     $form = $this->createForm(AdType::class, $ad);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_ad_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('ad/edit.html.twig', [
    //         'ad' => $ad,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/{id}', name: 'app_ad_delete', methods: ['POST'])]
    public function delete(Request $request, Ad $ad, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ad->getId(), $request->request->get('_token'))) {
            $entityManager->remove($ad);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ad_index', [], Response::HTTP_SEE_OTHER);
    }
}