<?php

namespace App\Controller;

use LogicException;
use App\Entity\User;
use App\Form\UserType;
use DateTimeImmutable;
use App\Repository\UserRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/inscription', name: 'inscription')]
    public function index(EntityManagerInterface $manager, Request $query, UserRepository $repo, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User;
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($query);
        if($form->isSubmitted() && $form->isValid())
        {
            $roles[] = 'ROLE_ADMIN';
            $password = $form->get('password')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);
            $user->setDateEnregistrement(new DateTimeImmutable);
            $user->setRoles($roles);
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('app_produit');
        }
        return $this->render('user/inscription.html.twig', [
            'formUser' => $form
        ]);
    }
    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $utils, Membre $membre = null)
    {
        if($membre)
        {
            return $this->redirectToRoute('home');
        }
        $error = $utils->getLastAuthenticationError();
        $lastUsername = $utils->getLastUsername();
        $this->addFlash('success', "Vous êtes correctement connecté !");
        return $this->render('security/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
        ]);
    }
    #[Route(path: '/logout', name: 'logout')]
    public function logout(): void
    {
        $this->addFlash('deco', 'Vous êtes correctement déconnecté !');
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    #[Route('/user/profil/{id}', name: 'user_profil')]
    public function profil(CommandeRepository $repo, EntityManagerInterface $manager, User $user)
    {
        $user = $this->getUser();
        $commandes = $repo->findBy(['user' => $user->getId()]);

        return $this->render('user/profil.html.twig', [
            'commandes' => $commandes,
            'user' => $user
        ]);
    }
}
