<?php

namespace App\Controller;

use App\Enum\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("IS_AUTHENTICATED_FULLY")]
final class SettingController extends AbstractController
{
    /**
     * This route serves the page when refresh happens (F5 or copy/paste in address bar)
     *
     * If user may access the page, it informs vue router to go to this page
     * If user may not access the page, he is being redirected to menu
     *
     * @return Response
     */
    #[Route('/setting', name: 'app.setting.index')]
    public function index(): Response
    {
        // only accessible for admins
        if ($this->isGranted(Role::ADMIN->value)) {
            return $this->render('base.html.twig', ['route' => 'setting']);
        }

        return $this->redirectToRoute('app.home.index',  ['route' => 'app.home.index']);
    }
}
