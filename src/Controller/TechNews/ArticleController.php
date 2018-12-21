<?php

namespace App\Controller\TechNews;

use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Membre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
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
     */
    public function newArticle()
    {
        # Récupération d'un Membre
        $membre = $this->getDoctrine()
            ->getRepository(Membre::class)
            ->find(1);

        # Création d'un Nouvel Article
        $article = new Article();
        $article->setMembre($membre)
            ->setTitre('Momo est fatigué');

        # Création du Formulaire
        $form = $this->createFormBuilder($article)
            ->add('titre', TextType::class, [
                'required' => true,
                'label' => "Titre de l'Article",
                'attr' => [
                    'placeholder' => "Titre de l'Article"
                ]
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'expanded' => false,
                'multiple' => false,
                'label' => false
            ])
            ->add('contenu', TextareaType::class, [
                'label' => false
            ])
            ->add('featuredImage', FileType::class, [
                'attr' => [
                    'class' => 'dropify'
                ]
            ])
            ->add('special', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'data-toggle' => 'toggle',
                    'data-on' => 'Oui',
                    'data-off' => 'Non'
                ]
            ])
            ->add('spotlight', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'data-toggle' => 'toggle',
                    'data-on' => 'Oui',
                    'data-off' => 'Non'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Publier mon Article'
            ])
            ->getForm()
        ;

        # Affichage dans la vue
        return $this->render('article/form.html.twig', [
           'form' => $form->createView()
        ]);

    }

}