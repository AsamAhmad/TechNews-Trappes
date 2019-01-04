<?php

namespace App\Controller\TechNews;


use App\Entity\Article;
use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    /**
     * Page d'Accueil
     * @return Response
     */
    public function index()
    {

        $repository = $this->getDoctrine()
            ->getRepository(Article::class);

        $articles = $repository->findBy([], ['id' => 'DESC']);
        $spotlight = $repository->findBySpotlight();

        # return new Response("<html><body><h1>PAGE D'ACCUEIL</h1></body></html>");
        return $this->render('front/index.html.twig', [
            'articles' => $articles,
            'spotlight' => $spotlight
        ]);
    }

    /**
     * Page de Contact
     * @return Response
     */
    public function contact()
    {
        return new Response("<html><body><h1>PAGE DE CONTACT</h1></body></html>");
    }

    /**
     * Page permettant d'afficher les articles
     * d'une catégorie
     * @Route("/{slug<[a-zA-Z0-9\-_\/]+>}",
     *     methods={"GET"}, name="front_categorie")
     * @param $slug
     * @param Categorie|null $categorie
     * @return Response
     */
    public function categorie($slug, Categorie $categorie = null)
    {

        # Méthode 1 :
        # $categorie = $this->getDoctrine()
        #     ->getRepository(Categorie::class)
        #     ->findOneBy(['slug' => $slug]);
        # $articles = $categorie->getArticles();

        # Méthode 2:
        # $articles = $this->getDoctrine()
        #     ->getRepository(Categorie::class)
        #    ->findOneBySlug($slug)
        #     ->getArticles();

        # On s'assure que la catégorie ne soit pas null
        if (null === $categorie) {
            return $this->redirectToRoute('index', [],
                Response::HTTP_MOVED_PERMANENTLY);
        }

        # Méthode 3
        # return new Response("<html><body><h1>PAGE CATEGORIE : $slug</h1></body></html>");
        return $this->render('front/categorie.html.twig', [
            'categorie' => $categorie,
            'articles' => $categorie->getArticles()
        ]);
    }

    /**
     * @Route("/{categorie<[a-zA-Z0-9\-_\/]+>}/{slug<[a-zA-Z0-9\-_\/]+>}_{id<\d+>}.html",
     *     name="front_article")
     * @param Article $article
     * @return Response
     */
    public function article($categorie, $slug, Article $article = null)
    {
        # Exemple d'URL
        # /politique/vinci-autoroutes-va-envoyer-une-facture-aux-automobilistes_9841.html

        # $article = $this->getDoctrine()
        #     ->getRepository(Article::class)
        #     ->find($id);

        # On s'assure que l'article ne soit pas null
        if (null === $article) {
            return $this->redirectToRoute('index', [],
                Response::HTTP_MOVED_PERMANENTLY);
        }

        # Vérification du SLUG
        if ($article->getSlug() !== $slug
            || $article->getCategorie()->getSlug() !== $categorie) {
            return $this->redirectToRoute('front_article', [
                'categorie' => $article->getCategorie()->getSlug(),
                'slug' => $article->getSlug(),
                'id' => $article->getId()
            ]);
        }

        # Récupération des suggestions
        $suggestions = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findArticlesSuggestions(
                $article->getId(),
                $article->getCategorie()->getId()
            );

        # return new Response("<html><body><h1>PAGE ARTICLE : $id</h1></body></html>");
        return $this->render('front/article.html.twig', [
            'article' => $article,
            'suggestions' => $suggestions
        ]);
    }

    /**
     * Gérer l'affichage
     * de la sidebar.
     */
    public function sidebar()
    {
        # Récupération du Repository
        $repository = $this->getDoctrine()
            ->getRepository(Article::class);

        # Récupérer les 5 derniers articles
        $articles = $repository->findLatestArticles();

        # Récupérer les articles "specials"
        $specials = $repository->findBySpecial();

        # Rendu de la vue
        return $this->render('components/_sidebar.html.twig', [
           'articles' => $articles,
           'specials' => $specials
        ]);
    }

}