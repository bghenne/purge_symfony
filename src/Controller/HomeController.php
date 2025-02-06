<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @author Benjamin Ghenne <benjamin.ghenne@gfptech.fr>
 */
#[IsGranted("IS_AUTHENTICATED_FULLY")]
final class HomeController extends AbstractController
{
    #[Route('/{route}', requirements: ['route' => '.{0,999}+'], priority: -1)]
    #[IsGranted('ROLE_USER')]
    public function home(string $route): Response
    {
        return $this->render('base.html.twig', ['route' => $route]);
    }
}
