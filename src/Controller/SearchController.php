<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\AdCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search', methods: ['POST'])]
    public function search(Request $request, EntityManagerInterface $em): Response
    {
        // Collect search parameters from the request
        $searchText = $request->get('keyword');

        // Perform optimized search query
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder
            ->select('ad')
            ->from(Ad::class, 'ad')
            ->where('ad.title LIKE :searchText OR ad.description LIKE :searchText')
            ->setParameter('searchText', '%'.$searchText.'%');

        $searchResults = $queryBuilder->getQuery()->getResult();

        // Get all parent categories
        $parentCategories = $em->getRepository(AdCategory::class)->findBy(['parentCategory' => null]);

        // dd($searchResults);

        // Render search results
        return $this->render('pages/search/item-listing-list.html.twig', [
            'searchResults' => $searchResults,
            'parentCategories' => $parentCategories,
            'searchKeyword' => $searchText ?? 'All',
        ]);
    }
}