<?php

namespace App\Controller\Account;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use MongoDB\Driver\Manager;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/inscription", name="app_register")
     */
    public function index(Request $request, UserPasswordHasherInterface $hasher): Response
    {

        if ($this->getUser()) {
            return $this->redirectToRoute('account');
        }

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $user = $form->getData();


            try {
                $password = $hasher->hashPassword($user, $user->getPassword());
                $user->setPassword($password);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                //code rajouté
                return $this->redirectToRoute('app_login');
            } catch (UniqueConstraintViolationException $e) {
                $form->get('email')->addError(new FormError('Cet email est déjà utilisé'));
            }




        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
