<?php

namespace App\Controller;

/*
 * MasterAdminController handles admin functionality.
 *
 * Extends AbstractController and defines the '/master/admin' route.
 *
 * The index() method checks for the 'ROLE_SUPER_ADMIN' role and fetches
 * all Ads and Users if granted. Otherwise it gets the current user's Ads.
 *
 * Renders the 'master_admin/index.html.twig' template with Ads, Users
 * and activity logs data.
 */

use App\Entity\ActivityLog;
use App\Entity\Ad;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master')]
class MasterAdminController extends AbstractController
{
    #[Route('/admin', name: 'app_master_admin', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        $AdRepository = $em->getRepository(Ad::class);
        $UserRepository = $em->getRepository(User::class);
        $logsRepository = $em->getRepository(ActivityLog::class);

        $latestAdsQuery = $AdRepository->createQueryBuilder('a')->orderBy('a.updatedAt', 'DESC')->getQuery(); // Order by createdAt in descending order
        $latestUsersQuery = $UserRepository->createQueryBuilder('u')->orderBy('u.updatedAt', 'DESC')->getQuery();
        $latestLogsQuery = $logsRepository->createQueryBuilder('l')->orderBy('l.updatedAt', 'DESC')->getQuery();

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $ads = $paginator->paginate(
                $latestAdsQuery,
                $request->query->getInt('AdminAds', 1),
                10,
                ['pageParameterName' => 'AdminAds']
            );

            $users = $paginator->paginate(
                $latestUsersQuery,
                $request->query->getInt('AdminUsers', 1),
                10,
                ['pageParameterName' => 'AdminUsers']
            );

            $logs = $paginator->paginate($latestLogsQuery, $request->query->getInt('AdminLogs', 1), 10, ['pageParameterName' => 'AdminLogs']);
            // dd($logsRepository->findAll());
        } else {
            $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

            return $this->redirectToRoute('app_ad_index');
        }

        return $this->render('master_admin/index.html.twig', [
            'ads' => $ads ?? [],
            'users' => $users ?? [],
            'activities' => $logs ?? [],
        ]);
    }
}