<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
Use App\Repository\VacataireRepository;
use Knp\Component\Pager\PaginatorInterface;

final class VacataireController extends AbstractController
{
    #[Route('/vacataire', name: 'app_vacataire')]
    public function index(VacataireRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $vacataires = $repository->findAll();
        
        $pagination = $paginator->paginate(
            $vacataires,
            $request->query->getInt('page', 1),
            10 
        );

        return $this->render('pages/vacataire/index.html.twig', [
            'vacataires' => $pagination,
        ]);
    }
}
