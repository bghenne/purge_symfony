<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class HomeController
 *
 * @package App\Controller
 * @category
 * @author Benjamin Ghenne <benjamin.ghenne@gfptech.fr>
 * @license
 * @copyright GFP Tech 2024
 */
class HomeController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('home/home.html.twig');
    }
}