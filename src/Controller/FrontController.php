<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\AdCategory;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/', name: 'app_homepage', methods: ['GET'])]
    public function index(): Response
    {
        // $users = $this->em->getRepository(User::class)->findAll();

        $ads = $this->em->getRepository(Ad::class)->findAll();

        $categories = $this->em->getRepository(AdCategory::class)->findBy(['parentCategory' => null]);

        return $this->render('pages/index.html.twig', [
          'ads' => $ads,
          'categories' => $categories,
        ]);
    }

    #[Route('/category/{id}', name: 'app_category_show', methods: ['GET'])]
    public function ShowCategories(AdCategory $category)
    {
        // $category = $this->em->getRepository(AdCategory::class)->find($id);
        $subCategories = $category->getSubCategories();
        $ads = [];
        foreach ($subCategories as $subCategory) {
            $subCategoryAds = $this->em->getRepository(Ad::class)->findBy(['category' => $subCategory]);
            $ads = array_merge($ads, $subCategoryAds);
        }

        // Get all parent categories
        $parentCategories = $this->em->getRepository(AdCategory::class)->findBy(['parentCategory' => null]);

        return $this->render('pages/display-Categories.html.twig', [
          'ads' => $ads,
          'subCategories' => $subCategories,
          'category' => $category,
          'parentCategories' => $parentCategories,
          ]);
    }

    #[Route('/category/autos', name: 'app_electronics', methods: ['GET'])]
    public function ShowElectronics()
    {
        $category = $this->em->getRepository(AdCategory::class)->findOneBy(['name' => 'Electronics']);
        if ($category->getParentCategory() === null) {
            // $category is a parent category
            $subCategories = $category->getSubCategories();
            $ads = [];
            foreach ($subCategories as $subCategory) {
                $subCategoryAds = $this->em->getRepository(Ad::class)->findBy(['category' => $subCategory]);
                $ads = array_merge($ads, $subCategoryAds);
            }
        } else {
            // $category is a sub category
            $parentCategory = $this->em->getRepository(AdCategory::class)->findBy(['parentCategory' => null]);
            $ads = $this->em->getRepository(Ad::class)->findBy(['category' => $category]);
        }

        return $this->render('pages/display-Categories.html.twig', [
          'ads' => $ads,
          'subCategories' => $subCategories,
          'category' => $category,
          'parentCategory' => $parentCategory,
          ]);
    }

    #[Route('/category/autos', name: 'app_estates', methods: ['GET'])]
    public function ShowRealEstates()
    {
        $category = $this->em->getRepository(AdCategory::class)->findOneBy(['name' => 'Real Estate']);
        if ($category->getParentCategory() === null) {
            // $category is a parent category
            $subCategories = $category->getSubCategories();
            $ads = [];
            foreach ($subCategories as $subCategory) {
                $subCategoryAds = $this->em->getRepository(Ad::class)->findBy(['category' => $subCategory]);
                $ads = array_merge($ads, $subCategoryAds);
            }
        } else {
            // $category is a sub category
            $parentCategory = $this->em->getRepository(AdCategory::class)->findBy(['parentCategory' => null]);
            $ads = $this->em->getRepository(Ad::class)->findBy(['category' => $category]);
        }

        return $this->render('pages/display-Categories.html.twig', [
          'ads' => $ads,
          'subCategories' => $subCategories,
          'category' => $category,
          'parentCategory' => $parentCategory,
          ]);
    }

    #[Route('/category/autos', name: 'app_autos', methods: ['GET'])]
    public function ShowAutos()
    {
        $category = $this->em->getRepository(AdCategory::class)->findOneBy(['name' => 'Vehicles']);
        if ($category->getParentCategory() === null) {
            // $category is a parent category
            $subCategories = $category->getSubCategories();
            $ads = [];
            foreach ($subCategories as $subCategory) {
                $subCategoryAds = $this->em->getRepository(Ad::class)->findBy(['category' => $subCategory]);
                $ads = array_merge($ads, $subCategoryAds);
            }
        } else {
            // $category is a sub category
            $parentCategory = $this->em->getRepository(AdCategory::class)->findBy(['parentCategory' => null]);
            $ads = $this->em->getRepository(Ad::class)->findBy(['category' => $category]);
        }

        return $this->render('pages/display-Categories.html.twig', [
          'ads' => $ads,
          'subCategories' => $subCategories,
          'category' => $category,
          'parentCategory' => $parentCategory,
          ]);
    }

    #[Route('/category/autos', name: 'app_homes', methods: ['GET'])]
    public function ShowHomes()
    {
        $category = $this->em->getRepository(AdCategory::class)->findOneBy(['name' => 'Homes']);
        if ($category->getParentCategory() === null) {
            // $category is a parent category
            $subCategories = $category->getSubCategories();
            $ads = [];
            foreach ($subCategories as $subCategory) {
                $subCategoryAds = $this->em->getRepository(Ad::class)->findBy(['category' => $subCategory]);
                $ads = array_merge($ads, $subCategoryAds);
            }
        } else {
            // $category is a sub category
            $parentCategory = $this->em->getRepository(AdCategory::class)->findBy(['parentCategory' => null]);
            $ads = $this->em->getRepository(Ad::class)->findBy(['category' => $category]);
        }

        return $this->render('pages/display-Categories.html.twig', [
          'ads' => $ads,
          'subCategories' => $subCategories,
          'category' => $category,
          'parentCategory' => $parentCategory,
          ]);
    }
}