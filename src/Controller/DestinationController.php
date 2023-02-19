<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\DestinationRepository;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use App\Entity\Destination;
use App\Entity\Article;
use App\Form\ArticleFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;



class DestinationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/destination', name: 'app_destination')]
    public function index(Request $request,Environment $twig, DestinationRepository $destinationRepository): Response

    {
               return new Response($twig->render('destination/index.html.twig', [
                   'destinations' => $destinationRepository->findAll(),
               ]));
    }
    #[Route('/', name: 'home')]
//    public function index(): Response
    public function caroussel(Request $request,Environment $twig, DestinationRepository $destinationRepository): Response

    {
        $test = $destinationRepository->findAll();
        usort($test,function($a,$b){
            return count($a->getUsers()) < count($b->getUsers());
        });
        //dd($destinationRepository->findAll());
        dump($test);
        return new Response($twig->render('destination/home.html.twig', [
            'destination1' => $test[0],
            'destination2' => $test[1],
            'destination3' => $test[2],
        ]));
    }

    #[Route('/destination/visit/{id}', name: 'destination_visit')]
    public function isVisited(Request $request, Destination $destination, ArticleRepository $articleRepository,#[Autowire('%photo_dir%')] string $photoDir): Response
    {
        $user = $this->getUser();
        $destination->addUser($user);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_destination');
    }

    #[Route('/destination/{id}', name: 'destination')]
    public function show(Request $request, Destination $destination, ArticleRepository $articleRepository,#[Autowire('%photo_dir%')] string $photoDir): Response
    {
        return $this->render('destination/show.html.twig', [
            'destination' => $destination,
            'articles' => $articleRepository->findBy(['destination' => $destination]),
        ]);

    }

    #[Route('/destination/article/{id}', name: 'destination_article')]
    public function articleForm(Request $request, Destination $destination, ArticleRepository $articleRepository,#[Autowire('%photo_dir%')] string $photoDir): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article->setDestination($destination);
            if ($photo = $form['photo']->getData()) {
                $filename = bin2hex(random_bytes(6)).'.'.$photo->guessExtension();
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
                $article->setPhoto($filename);
            }

            $this->entityManager->persist($article);
            $this->entityManager->flush();

            return $this->redirectToRoute("app_destination");
        }

        return $this->render('destination/articleForm.html.twig', [
            'article_form' => $form,
        ]);

    }






}
