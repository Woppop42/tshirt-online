<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'app_commande')]
    public function index(RequestStack $rs, ProduitRepository $repo, Request $query, EntityManagerInterface $manager): Response
    {
        $commande = new Commande;
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($query);
        $session = $rs->getSession();
        $cart = $session->get('cart', []);
 
        if($form->isSubmitted() && $form->isValid())
        {
            // foreach($cart as $item => $value)
            // {
            //     $idProduit = $item;
 

            // }
            // foreach($cart as $item)
            // {
            //     $qt = $item;
            // }

            foreach($cart as $item => $value)
            {
                $produit = $repo->find($item);
                $prix = $produit->getPrix() * $value;
                $commande->setUser($this->getUser());
                $commande->addProduit($produit);
                $commande->setQuantite($value);
                $commande->setPrix($prix);
                $commande->setEtat('En cours de traitement');
                $commande->setProduitId($item);
                $commande->setDateEnregistrement(new \DateTimeImmutable);
                $manager->persist($commande);
                $manager->flush();
            }

            return $this->redirectToRoute('app_produit');

        }
        

        return $this->render('commande/index.html.twig', [
            'formCommande' => $form,
            'cart' => $cart
        ]);
    }
}
