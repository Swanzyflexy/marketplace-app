<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\AdCategory;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/category/{id}', name: 'app_category_show', methods: ['GET', 'POST'])]
    public function ShowCategories(Request $request, $id)
    {
        $category = $this->em->getRepository(AdCategory::class)->find($id);

        if ($category === null) {
            // Handle the case where the category with the given $id is not found.
            // You might want to redirect or show an error message.
            // For now, let's return a simple error response.
            return new Response('Category not found', Response::HTTP_NOT_FOUND);
        }

        $parentCategory = $category->getParentCategory();
        if ($parentCategory === null) {
            // $category is a parent category
            $subCategories = $category->getSubCategories();
            $ads = [];
            foreach ($subCategories as $subCategory) {
                $subCategoryAds = $this->em->getRepository(Ad::class)->findBy(['category' => $subCategory]);
                $ads = array_merge($ads, $subCategoryAds);
            }
        } else {
            // $category is a sub category
            $parentCategory = $category->getParentCategory();
            $subCategories = $parentCategory->getSubCategories();
            $ads = $this->em->getRepository(Ad::class)->findBy(['category' => $category]);
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

    #[Route('/category/electronics', name: 'app_electronics', methods: ['GET'])]
    public function ShowElectronics()
    {
        $parentCategories = $this->em->getRepository(AdCategory::class)->findBy(['parentCategory' => null]);
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
            $parentCategory = $category->getParentCategory();
            $subCategories = $parentCategory->getSubCategories();
            $ads = $this->em->getRepository(Ad::class)->findBy(['category' => $category]);
        }

        return $this->render('pages/display-Categories.html.twig', [
          'ads' => $ads,
          'subCategories' => $subCategories,
          'category' => $category,
          'parentCategory' => $parentCategory ?? '',
          'parentCategories' => $parentCategories,
        ]);
    }

    #[Route('/category/estates', name: 'app_estates', methods: ['GET'])]
    public function ShowRealEstates()
    {
        $parentCategories = $this->em->getRepository(AdCategory::class)->findBy(['parentCategory' => null]);
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
            $parentCategory = $category->getParentCategory();
            $subCategories = $parentCategory->getSubCategories();
            $ads = $this->em->getRepository(Ad::class)->findBy(['category' => $category]);
        }

        return $this->render('pages/display-Categories.html.twig', [
          'ads' => $ads,
          'subCategories' => $subCategories,
          'category' => $category,
          'parentCategory' => $parentCategory ?? '',
          'parentCategories' => $parentCategories, ]);
    }

    #[Route('/category/autos', name: 'app_autos', methods: ['GET'])]
    public function ShowAutos()
    {
        $parentCategories = $this->em->getRepository(AdCategory::class)->findBy(['parentCategory' => null]);
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
            $parentCategory = $category->getParentCategory();
            $subCategories = $parentCategory->getSubCategories();
            $ads = $this->em->getRepository(Ad::class)->findBy(['category' => $category]);
        }

        return $this->render('pages/display-Categories.html.twig', [
          'ads' => $ads,
          'subCategories' => $subCategories,
          'category' => $category,
          'parentCategory' => $parentCategory ?? '',
          'parentCategories' => $parentCategories,
        ]);
    }

    #[Route('/category/homes', name: 'app_homes', methods: ['GET'])]
    public function ShowHomes()
    {
        $parentCategories = $this->em->getRepository(AdCategory::class)->findBy(['parentCategory' => null]);
        $category = $this->em->getRepository(AdCategory::class)->findOneBy(['name' => 'Furniture']);
        if ($category === null) {
            // Handle the case where the category with the name 'Homes' is not found.
            // You might want to redirect or show an error message.
            // For now, let's return a simple error response.
            return new Response('Category not found', Response::HTTP_NOT_FOUND);
        }
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
            $parentCategory = $category->getParentCategory();
            $subCategories = $parentCategory->getSubCategories();
            $ads = $this->em->getRepository(Ad::class)->findBy(['category' => $category]);
        }

        return $this->render('pages/display-Categories.html.twig', [
          'ads' => $ads,
          'subCategories' => $subCategories,
          'category' => $category,
          'parentCategory' => $parentCategory ?? '',
          'parentCategories' => $parentCategories,
          ]);
    }
}