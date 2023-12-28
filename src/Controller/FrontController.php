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

        // dd($ads, $categories);

        return $this->render('pages/index.html.twig', [
          'ads' => $ads,
          'categories' => $categories,
        ]);
    }
}