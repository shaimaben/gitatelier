<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorFormType;
use App\Form\MinMaxFormType;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/showauthor', name: 'showauthor')]
    public function showauthor(AuthorRepository $authorRepository, Request $req): Response
    {
        $author = $authorRepository->findAll();
        $form = $this->createForm(MinMaxFormType::class);
        $form->handleRequest($req);
    
        if ($form->isSubmitted()) {
            $min = $form->get('min')->getData();
            $max = $form->get('max')->getData();
            $authors = $authorRepository->searchAuthorByNbBooks($min, $max);
    
            return $this->renderForm('author/showauthor.html.twig', [
                'author' => $authors,
                'f' => $form,
            ]);
        }
    
        return $this->renderForm('author/showauthor.html.twig', [
            'author' => $author,
            'f' => $form,
        ]);
    }

    #[Route('/authordelete', name: 'authordelete')]
    public function authordelete(AuthorRepository $authorRepository , Request $req): Response
    {    
        $authorRepository->deleteAuthorsWithZeroBooks();
          
         $author = $authorRepository->findAll();
         
             return $this->renderForm('author/showauthor.html.twig', [

             ]);}
        
    

    #[Route('/addauthor', name: 'addauthor')]
    public function addauthor(ManagerRegistry $managerRegistry, Request $req): Response
    {
        $em = $managerRegistry->getManager();
        $author = new Author();
        $form = $this->createForm(AuthorFormType::class, $author);
        $form->handleRequest($req);
        if ($form->isSubmitted() and $form->isValid()) {
            $em->persist($author);
            $em->flush();
            return $this->redirect('showauthor');
        }
        return $this->renderForm('author/addauthor.html.twig', [
            'f' => $form
        ]);
    }

    #[Route('/editauthor/{id}', name: 'editauthor')]
    public function editauthor($id, AuthorRepository $authorRepository, ManagerRegistry $managerRegistry, Request $req): Response
    {
        
        $em = $managerRegistry->getManager();
        $dataid = $authorRepository->find($id);
        $form = $this->createForm(AuthorFormType::class, $dataid);
        $form->handleRequest($req);
        if ($form->isSubmitted() and $form->isValid()) {
            $em->persist($dataid);
            $em->flush();
            return $this->redirectToRoute('showauthor');
        }

        return $this->renderForm('author/editauthor.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/deletauthor/{id}', name: 'deletauthor')]
    public function deletauthor($id, ManagerRegistry $managerRegistry, AuthorRepository $repo): Response
    {
        $em = $managerRegistry->getManager();
        $id = $repo->find($id);
        $em->remove($id);
        $em->flush();
        return $this->redirectToRoute('showauthor');
    }
}
