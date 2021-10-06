<?php
namespace App\Controller;

use App\Form\UserRegisterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user/register", name= "user_register")
     */
    public function register(EntityManagerInterface $em, Request $request)
    {
        $form = $this->createForm(UserRegisterFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
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
