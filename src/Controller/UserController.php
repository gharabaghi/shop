<?php
namespace App\Controller;

use App\Form\UserEditProfilerFormType;
use App\Form\UserRegisterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/user/register", name= "app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $hasher)
    {
        $form = $this->createForm(UserRegisterFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword($hasher->hashPassword($user, $user->getPassword()));
            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('message', 'Registration is completed! Now you can login.');
            return $this->redirect($this->generateUrl('app_login'));
        }

        return $this->render(
            'user/register.html.twig',
            [
                'UserRegisterForm' => $form->createView()
            ]
        );
    }

    /**
     * @Route("user/editProfile", name="user_edit_profile")
     */
    public function editProfile(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserEditProfilerFormType::class,$user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $this->em->flush();

            $this->addFlash('message', 'Your data updated.');
            return $this->redirect($this->generateUrl('user_edit_profile'));
        }

        return $this->render(
            'user/edit.html.twig',
            [
                'UserEditForm' => $form->createView()
            ]
        );
    }
}
