<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookFormType;
use App\Form\SearchType;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    #[Route('/showabook', name: 'showabook')]
    public function showabook(BookRepository $bookRepository, Request $req): Response
    {

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($req);
        $scienceFictionSomme = $bookRepository->sommebook();
        

    
        if ($form->isSubmitted() && $form->isValid()) {
            $datainput = $form->get('ref')->getData();
            $book = $bookRepository->searchbyreferance($datainput);
        } else {
            
            //$book = $bookRepository->affichagebyauthor();
            //$book = $bookRepository->findbookbyauth();
            $book = $bookRepository->updatecategory();
            $publishedCount = $bookRepository->publishedbooks();
            $nonPublishedCount = $bookRepository->publishedbooks();
            $scienceFictionSomme = $bookRepository->sommebook();
            $books = $bookRepository->findBooksbyDate();


            
        }
    
        return $this->renderform('book/showabook.html.twig', [
            'book' => $book,
            'publishedCount' => $publishedCount,
            'nonPublishedCount' =>$nonPublishedCount ,
            'scienceFictionSomme' => $scienceFictionSomme,
            'f' => $form
        ]);

    }

    #[Route('/addbook', name: 'addbook')]
    public function addbook(ManagerRegistry $managerRegistry, Request $req): Response
    {
        $em = $managerRegistry->getManager();
        $book = new Book();
        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest($req);
        if ($form->isSubmitted() and $form->isValid()) {
            $em->persist($book);
            $em->flush();
            return $this->redirect('showabook');
        }
        return $this->renderForm('book/showabook.html.twig', [
            'f' => $form
        ]);
    }


    #[Route('/editbook/{id}', name: 'editbook')]
    public function editbook($id, BookRepository $rep, ManagerRegistry $managerRegistry, Request $req): Response
    {
        
        $em = $managerRegistry->getManager();
        $dataid = $rep->find($id);
        $form = $this->createForm(BookFormType::class, $dataid);
        $form->handleRequest($req);
        if ($form->isSubmitted() and $form->isValid()) {
            $em->persist($dataid);
            $em->flush();
            return $this->redirectToRoute('showabook');
        }

        return $this->renderForm('book/editbook.html.twig', [
            'form' => $form
        ]);

        
    }

    #[Route('/deletbook/{id}', name: 'deletbook')]
    public function deletbook($id, ManagerRegistry $managerRegistry, BookRepository $repo): Response
    {
        $em = $managerRegistry->getManager();
        $id = $repo->find($id);
        $em->remove($id);
        $em->flush();
        return $this->redirectToRoute('showabook');
    }


}
