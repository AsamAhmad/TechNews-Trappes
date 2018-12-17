<?php

namespace App\Controller\TechNews;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController
{
    /**
     * Page d'Accueil
     * @return Response
     */
    public function index()
    {
        return new Response("<html><body><h1>PAGE D'ACCUEIL</h1></body></html>");
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
     * @Route("/{slug<[a-zA-Z1-9\-_\/]+>}",
     *     methods={"GET"}, name="front_categorie")
     * @param $slug
     * @return Response
     */
    public function categorie($slug)
    {
        return new Response("<html><body><h1>PAGE CATEGORIE : $slug</h1></body></html>");
    }

    /**
     * @Route("/{categorie<[a-zA-Z1-9\-_\/]+>}/{slug<[a-zA-Z1-9\-_\/]+>}_{id<\d+>}.html",
     *     name="front_article")
     * @param $id
     * @param $slug
     * @param $categorie
     * @return Response
     */
    public function article($id, $slug, $categorie)
    {
        # Exemple d'URL
        # /politique/vinci-autoroutes-va-envoyer-une-facture-aux-automobilistes_9841.html
        return new Response("<html><body><h1>PAGE ARTICLE : $id</h1></body></html>");
    }

}