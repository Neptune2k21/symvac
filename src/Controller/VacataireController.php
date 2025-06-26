<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
Use App\Repository\VacataireRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\VacataireTypeForm;
use App\Entity\Vacataire;
use Doctrine\ORM\EntityManagerInterface;

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
    #[Route('/vacataire/nouveau', name: 'vacataire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $vacataire = new Vacataire();
        $form = $this->createForm(VacataireTypeForm::class, $vacataire);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $vacataire = $form->getData();
            $manager ->persist($vacataire);
            $manager->flush();
            $this->addFlash(
                'success',
                'Vos changements ont été enregistrés !'
            );

            return $this->redirectToRoute('app_vacataire');
        }
        
        return $this->render('pages/vacataire/new.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route('/vacataire/{id}/modifier', name: 'vacataire_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, EntityManagerInterface $manager, VacataireRepository $repository): Response
    {
        $vacataire = $repository->find($id);
        
        if (!$vacataire) {
            throw $this->createNotFoundException('Vacataire non trouvé');
        }
        
        $form = $this->createForm(VacataireTypeForm::class, $vacataire);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            $this->addFlash(
                'success',
                'Le vacataire a été modifié avec succès !'
            );

            return $this->redirectToRoute('app_vacataire');
        }
        
        return $this->render('pages/vacataire/edit.html.twig', [
            'form' => $form,
            'vacataire' => $vacataire,
        ]);
    }
}
