<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile')]
class UserController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('profile/dashboard.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/edit-profile', name: 'app_edit_profile')]
    public function profile(): Response
    {
        return $this->render('profile/profile-settings.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}