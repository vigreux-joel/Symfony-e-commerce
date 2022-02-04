<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountSettingsController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/compte/mes-informations", name="account_settings")
     */
    public function index(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $notification = null;

        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $old_pwd = $form->get('old_password')->getData();
            if($hasher->isPasswordValid($user, $old_pwd)){
                $new_pwd = $form->get('new_password')->getData();

                if(isset($new_pwd)){
                    $password = $hasher->hashPassword($user, $new_pwd);
                    $user->setPassword($password);
                }

                $this->entityManager->flush();
                $notification = 'Votre compte a bien été mis à jour';
            } else {
                $form->get('old_password')->addError(new FormError('Mot de passe incorrect'));
            }
        }

        return $this->render('account/settings.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
