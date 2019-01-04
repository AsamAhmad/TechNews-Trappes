<?php

namespace App\Controller\TechNews;

use App\Controller\HelperTrait;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Membre;
use App\Form\ArticleFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{

    use HelperTrait;

    /**
     * Démonstration de l'ajout d'un Article
     * avec Doctrine !
     * @Route("/demo/article", name="article_demo")
     */
    public function demo()
    {
        # Création de la Catégorie
        $categorie = $this->getDoctrine()
            ->getRepository(Categorie::class)
            ->find(1);

        # Création d'un Membre
        $membre = $this->getDoctrine()
            ->getRepository(Membre::class)
            ->find(1);

        # Création de l'Article
        $article = new Article();
        $article
            ->setTitre("Paris : 400 personnes réunies pour dire adieu à Nicolas Chauvin, espoir du Stade Français décédé après un plaquage")
            ->setSlug("paris-400-personnes-reunies-dire-adieu-nicolas-chauvin-espoir-du-stade-francais-decede-apres-plaquage")
            ->setContenu("<p>Echarpes roses, tenues sombres. Environ 400 personnes, dont des personnalités du rugby, ont assisté mercredi à Paris aux obsèques du jeune espoir du Stade Français Nicolas Chauvin, décédé après un plaquage en match contre les Espoirs de Bordeaux-Bègles une semaine plus tôt.&nbsp;</p>")
            ->setFeaturedImage("000_1bp8uc-4006911.jpg")
            ->setSpotlight(1)
            ->setSpecial(0)
            ->setMembre($membre)
            ->setCategorie($categorie)
        ;

        /*
         * Récupération du Manager de Doctrine.
         * ------------------------------------
         * Le EntityManager est une classe qui sais
         * comment persister d'autres classes.
         * (Effectuer des opérations CRUD sur nos Entités)
         */
        $em = $this->getDoctrine()->getManager();
        $em->persist($categorie);
        $em->persist($membre);
        $em->persist($article);
        $em->flush();

        # Retourner une réponse à la vue
        return new Response('Nouvel Article ajouté avec ID :'
        . $article->getId()
        . 'et la nouvelle catégorie '
        . $categorie->getNom()
        . 'de Auteur : '
        . $membre->getPrenom()
        );
    }

    /**
     * Formulaire pour ajouter un Article
     * @Route("/creer-un-article",
     *  name="article_new")
     * @Security("has_role('ROLE_AUTEUR')")
     */
    public function newArticle(Request $request)
    {
        # Récupération d'un Membre
        #$membre = $this->getDoctrine()
        #    ->getRepository(Membre::class)
        #    ->find(1);

        # Création d'un Nouvel Article
        $article = new Article();
        $article->setMembre($this->getUser());

        # Création du Formulaire
        $form = $this->createForm(ArticleFormType::class, $article)
            ->handleRequest($request);

        # Traitement des données POST
        #$form->handleRequest($request);

        # Si le formulaire est soumis et valide
        if($form->isSubmitted() && $form->isValid()) {

            # dump($article);
            # 1. Traitement de l'upload de l'image

            /** @var UploadedFile $featuredImage */
            $featuredImage = $article->getFeaturedImage();

            $fileName = $this->slugify($article->getTitre())
                . '.' . $featuredImage->guessExtension();

            try {
                $featuredImage->move(
                    $this->getParameter('articles_assets_dir'),
                    $fileName
                );
            } catch (FileException $e) {

            }

            # Mise à jour de l'image
            $article->setFeaturedImage($fileName);

            # Mise à jour du Slug
            $article->setSlug($this->slugify($article->getTitre()));

            # Sauvegarde en BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            # Notification
            $this->addFlash('notice',
                'Félicitations, votre article est en ligne !');

            # Redirection
            return $this->redirectToRoute('front_article', [
                'categorie' => $article->getCategorie()->getSlug(),
                'slug' => $article->getSlug(),
                'id' => $article->getId()
            ]);
        }

        # Affichage dans la vue
        return $this->render('article/form.html.twig', [
           'form' => $form->createView()
        ]);

    }

}