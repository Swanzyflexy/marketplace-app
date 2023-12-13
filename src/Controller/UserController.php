<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    // #[Route('/{id}/delete', name: 'app_user_delete_form', methods: ['GET'])]
    // public function deleteForm()
    // {
    //     return $this->render('profile/delete-account.html.twig');
    // }

    #[Route('{id}/delete', name: 'app_user_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $entityManager->remove($user);
            $entityManager->flush();

            if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
                $entityManager->remove($user);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_userx_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profile/delete-account.html.twig');
    }
}