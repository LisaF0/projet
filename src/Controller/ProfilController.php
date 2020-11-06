<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil", name="profil")
     */
    public function index(): Response
    {
        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
        ]);
    }
        /**
     * @Route("/profil/{id}", name="profil_show")
     */
    public function showProfil(User $user)
    {
        $form = $this->createForm(UserType::class);
        // $form->handleRequest($request);
        return $this->render('security/profil.html.twig', [
            'formUser' => $form->createView(),
        ]);
    }
}
