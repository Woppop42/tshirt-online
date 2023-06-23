<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Form\AdminUserType;
use App\Form\NewProduitType;
use App\Form\AdminProduitType;
use App\Form\AdminCommandeType;
use App\Repository\UserRepository;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            
        ]);
    }
    #[Route('/admin/user', name: 'admin_user')]
    public function adminUser(UserRepository $repo)
    {
        $users = $repo->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users
        ]);
    }
    #[Route('/admin/user/modif/{id}', name: 'user_update')]
    public function userUpdate(UserRepository $repo, Request $req, EntityManagerInterface $manager, User $user = null)
    {

        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($req);
        $role = [];
        if($form->isSubmitted() && $form->isValid())
        {
            $data = $form->get('roles')->getData();
            if($form->get('roles')->getData() == "Administrateur")
            {
                $role = ["ROLE_ADMIN"];
            }else 
            {
                $role = ["ROLE_USER"];
            }
            $user->setRoles($role);
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('app_admin');
        }
        
        return $this->render('admin/userModif.html.twig', [
            'form' => $form,
            'user' => $user
        ]);

    }
    #[Route('/admin/user/delete/{id}', name: 'user_delete')]
    public function deleteUser(UserRepository $repo, EntityManagerInterface $manager, User $user)
    {
        $manager->remove($user);
        $manager-flush();

        return $this->redirectToRoute('admin_user');
    }
    #[Route('/admin/commandes', name: 'admin_commandes')]
    public function adminCommande(CommandeRepository $repo)
    {
        $commandes = $repo->findAll();

        return $this->render('admin/commandes.html.twig', [
            'commandes' => $commandes,
        ]);
    }
    #[Route('/admin/commande/modif/{id}', name: 'commandes_modif')]
    public function modifCommande(Request $req, CommandeRepository $repo, EntityManagerInterface $manager, Commande $commande)
    {
        $form = $this->createForm(AdminCommandeType::class, $commande);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($commande);
            $manager->flush();

            return $this->redirectToRoute('admin_commandes');
        }

        return $this->render('/admin/commandeModif.html.twig', [
            'form' => $form,
            'commande' => $commande
        ]);
    }
    #[Route('/admin/produit', name: 'admin_produit')]
    public function adminProduit(ProduitRepository $repo)
    {
        $produits = $repo->findAll();

        return $this->render('/admin/produit.html.twig', [
            'produits' => $produits,
        ]);
    }
    #[Route('/admin/produit/modif/{id}', name: 'produit_modif')]
    public function produitModif(Request $req, ProduitRepository $repo, EntityManagerInterface $manager, SluggerInterface $slugger, Produit $produit)
    {
        $form = $this->createForm(AdminProduitType::class, $produit);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid())
        {
            $brochureFile = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) 
            {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
            $vehicule->setPhoto($newFilename);
            }
            $produit->setDateEnregistrement(new \DateTimeImmutable);
            $manager->persist($produit);
            $manager->flush();

            return $this->redirectToRoute('admin_produit');
        }

        return $this->render('admin/produitModif.html.twig', [
            'form' => $form
        ]);
    }
    #[Route('/admin/produit/new', name: 'produit_new')]
    public function newProduit(Request $req, ProduitRepository $repo, EntityManagerInterface $manager, SluggerInterface $slugger)
    {
        $produit = new Produit();
        $form = $this->createForm(NewProduitType::class, $produit);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid())
        {
            $brochureFile = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) 
            {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
            $produit->setPhoto($newFilename);
            }
            $produit->setDateEnregistrement(new \DateTimeImmutable);
            $manager->persist($produit);
            $manager->flush();

            return $this->redirectToRoute('admin_produit');
        }

        return $this->render('admin/newProduit.html.twig', [
            'form' => $form
        ]);
    }
    #[Route('admin/produit/delete/{id}', name: 'produit_delete')]
    public function deleteProduit(EntityManagerInterface $manager, Produit $produit)
    {
        $manager->remove($produit);
        $manager->flush();

        return $this->redirectToRoute('admin_produit');
    }
}
