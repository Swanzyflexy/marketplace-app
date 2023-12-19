<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\AdCategory;
use App\Entity\User;
use App\Repository\AdRepository;
use App\Service\AdService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface as Client;
use Twig\Environment as TwigEnvironment;

#[Route('/ad')]
class AdController extends AbstractController
{
    public function __construct(
        private AdService $adService,
        private EntityManagerInterface $em,
        private RequestStack $requestStack,
        private Client $httpClient,
        private TwigEnvironment $twig,
        private PaginatorInterface $paginator
    ) {
    }

    #[Route('/myAds', name: 'app_ad_index', methods: ['GET'])]
    public function index(AdRepository $adRepository, Request $request): Response
    {
        $user = $this->getUser();
        if ($user instanceof User) {
            // Fetch ads belonging to the authenticated user
            $adsQuery = $adRepository->createQueryBuilder('a')
            ->where('a.user = :user')
            ->setParameter('user', $user)
            ->getQuery();

            // Paginate the results
            $ads = $this->paginator->paginate(
                $adsQuery, // Query to paginate
                $request->query->getInt('page', 1), // Current page number, default is 1
                5 // Number of items per page
            );
        }

        return $this->render('profile/listAds.html.twig', [
            'ads' => $ads,
        ]);
    }

    #[Route('/new', name: 'app_ad_new', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
    ): Response {
        $user = $this->getUser();
        // Only Allow Verified User to post Ad, so we check if the user is verified
        if (!$user->isActive() || !$user->isVerified()) {
            $this->addFlash('error', 'Your account is not active or verified');

            // You can customize the error message and page according to your needs
            return $this->redirectToRoute('app_dashboard');
            // return $this->render('error/inactive_verified.html.twig');
        }

        $categories = $this->em->getRepository(AdCategory::class)
            ->findBy(['parentCategory' => null]);

        if ($request->isMethod('POST')) {
            $adCategory = $this->em->getRepository(AdCategory::class)->find($request->request->get('category'));
            $adImagesData = $request->files->get('ad_images');
            $result = $this->adService->createAd(
                $request->request->all(),
                $adImagesData,
                $user,
                $adCategory
            );

            // $result = $adService->createAd($request->request->all(), $adImagesData, $user, $request->request->get('category'));

            if ($result instanceof Ad) {
                // Ad creation was successful, do what you need
                $this->addFlash('success', 'Ad created successfully');

                return $this->redirectToRoute('app_ad_index');
            } elseif (is_array($result)) {
                // Validation errors occurred
                // Handle errors, you can pass them to your template or perform other actions
                $request->attributes->set('_validation_errors', $result);
                $request->attributes->set('_old_values', $request->request->all());

                return $this->render('profile/addnewlisting.html.twig', [
                    'categories' => $categories,
                    '_validation_errors' => $result, // Pass validation errors to the template
                    '_old_values' => $request->attributes->get('_old_values'),
                ]);
            } else {
                // Handle other types of errors, e.g., file upload error
                $this->addFlash('error', 'An error occurred while creating the ad.');

                return $this->redirectToRoute('app_ad_new');
            }
        }

        return $this->render('profile/addnewlisting.html.twig', [
            // 'ad' => $ad,
            // 'adImages' => $ad->getAdImages(),
            'categories' => $categories,
            '_validation_errors' => $request->request->get('_validation_errors'),
            '_old_values' => $request->request->all(),
         ]);
    }

    #[Route('/{id}/view-ad', name: 'app_ad_show', methods: ['GET'])]
    public function show(Ad $ad): Response
    {
        return $this->render('profile/ad-details.html.twig', [
            'ad' => $ad,
        ]);
    }

    // TODO: Update this route to submit edit request
    #[Route('/{id}/edit', name: 'app_ad_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ad $ad): Response
    {
        $categories = $this->em->getRepository(AdCategory::class)
            ->findBy(['parentCategory' => null]);

        // TODO: get ad by id

        if ($request->isMethod('POST')) {
            $adCategory = $this->em->getRepository(AdCategory::class)->find($request->request->get('category'));

            // Handle file uploads
            $uploadedImages = $request->files->get('ad_images');

            // Call the AdService to handle the edit logic, passing $ad instance
            $result = $this->adService->editAd($ad, $request->request->all(), $uploadedImages, $adCategory);

            if ($result instanceof Ad) {
                // Edit successful, redirect or show success message
                $this->addFlash('success', 'Ad updated successfully.');

                return $this->redirectToRoute('app_ad_show', ['id' => $ad->getId()]);
            } elseif (is_array($result)) {
                // Validation errors, render the form with errors
                return $this->render('profile/addnewlisting.html.twig', [
                    'ad' => $ad,
                    'categories' => $categories,
                    '_validation_errors' => $result,
                ]);
            }
            // Handle other error cases if needed
        }

        // Render the edit form
        return $this->render('profile/editlisting.html.twig', [
            'ad' => $ad,
            'categories' => $categories,
        ]);
    }

    // TODO Make this delete function work, add confirmation message
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