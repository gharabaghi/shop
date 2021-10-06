<?php
namespace App\Controller;

use App\Form\UserRegisterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user/register", name= "app_register")
     */
    public function register(EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $hasher)
    {
        $form = $this->createForm(UserRegisterFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword($hasher->hashPassword($user,$user->getPassword()));
            $em->persist($user);
            $em->flush();
        }

        return $this->render(
            'user/register.html.twig',
            [
                'UserRegisterForm' => $form->createView()
            ]
        );
    }
}
