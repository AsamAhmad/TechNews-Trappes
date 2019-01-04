<?php

namespace App\Controller\TechNews;


use App\Entity\Membre;
use App\Form\MembreFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MembreController extends AbstractController
{

    /**
     * Inscription d'un utilisateur
     * @Route("/inscription.html", name="membre_inscription")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function inscription(Request $request,
                                UserPasswordEncoderInterface $passwordEncoder)
    {
        # Création d'un Utilisateur
        $membre = new Membre();
        $membre->setRoles(['ROLE_MEMBRE']);

        # Création du Formulaire MembreFormType
        $form = $this->createForm(MembreFormType::class, $membre)
            ->handleRequest($request);

        # Si le formulaire est soumis et valide
        if($form->isSubmitted() && $form->isValid()) {

            # Encodage du mot de passe
            $membre->setPassword($passwordEncoder
            ->encodePassword($membre, $membre->getPassword()));

            # Sauvegarde en BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($membre);
            $em->flush();

            # Notification
            $this->addFlash('notice',
                'Félicitation, vous pouvez vous connecter !');

            # Redirection
            return $this->redirectToRoute('security_connexion');

        }

        # Affichage dans la vue
        return $this->render('membre/inscription.html.twig', [
            'form' => $form->createView()
        ]);
    }
}