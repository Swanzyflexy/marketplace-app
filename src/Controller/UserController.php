<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/profile')]
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        $user = $this->getUser();
        $activities = $user->getActivityLogs();
        if ($user instanceof User) {
            // Fetch ads belonging to the authenticated user
            $ads = $user->getAds();
        }

        return $this->render('profile/dashboard.html.twig', [
            'ads' => $ads,
            'activities' => $activities,
        ]);
    }

    // TODO make this work and add redirect to login page after delete
    #[Route('/{id}/edit-profile', name: 'app_edit_profile', methods: ['GET', 'POST'])]
    public function profile(): Response
    {
        return $this->render('profile/profile-settings.html.twig', [
        ]);
    }

    #[Route('/{id}/delete', name: 'app_user_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, User $user): Response
    {
        if ($request->isMethod('POST')) {
            $this->em->remove($user);
            $this->em->flush();

            if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
                $this->em->remove($user);
                $this->em->flush();
            }

            return $this->redirectToRoute('app_homepage', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profile/delete-account.html.twig');
    }
}