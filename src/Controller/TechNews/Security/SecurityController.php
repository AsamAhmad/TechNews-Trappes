<?php

namespace App\Controller\TechNews\Security;


use App\Form\LoginFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * Connexion d'un Membre
     * @Route("/connexion.html", name="security_connexion")
     * @param AuthenticationUtils $authenticationUtils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function connexion(AuthenticationUtils $authenticationUtils)
    {
        # Récupération du formulaire de connexion
        $form = $this->createForm(LoginFormType::class, [
            'email' => $authenticationUtils->getLastUsername()
        ]);

        # Récupération du message d'erreur
        $error = $authenticationUtils->getLastAuthenticationError();

        # Affichage de la vue
        return $this->render('security/connexion.html.twig', [
            'form' => $form->createView(),
            'error' => $error
        ]);
    }

    /**
     * Déconnexion d'un Membre
     * @Route("/deconnexion.html", name="security_deconnexion")
     */
    public function deconnexion()
    {
    }

    /**
     * Vous pourriez définir ici,
     * votre logique mot de passe oublié,
     * réinitialisation du mot de passe,
     * Email de validation, ...
     */
}