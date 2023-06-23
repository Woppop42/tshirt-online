<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(RequestStack $rs, ProduitRepository $repo): Response
    {
        $session = $rs->getSession();
        $cart = $session->get('cart', []);
        $cartWithData = [];
        $total = 0;

        foreach($cart as $id => $quantity)
        {
            $produit = $repo->find($id);
            $cartWithData[] = [
                'produit' => $produit,
                'quantity' => $quantity
            ];
            $total += $produit->getPrix() * $quantity;
        }
        return $this->render('cart/index.html.twig', [
            'items' => $cartWithData,
            'total' => $total
        ]);
    }
    #[Route('/cart/add/{id}', name: 'add_cart')]
    public function add($id, RequestStack $rs, ProduitRepository $repo): Response
    {
        $session = $rs->getSession();
        $cart = $session->get('cart', []);
        $qt = $session->get('qt', 0);

        if(!empty($cart[$id]))
        {
            $cart[$id]++;
            $qt ++;
        }else 
        {
            $cart[$id] = 1;
            $qt ++;
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_produit');
    }
    #[Route('/cart/remove/{id}', name: 'remove_cart')]
    public function remove($id, RequestStack $rs)
    {
        $session = $rs->getSession();
        $cart = $session->get('cart', []);
        $qt = $session->get('qt', 0);
        if(!empty($cart[$id]))
        {
            $qt -= $cart[$id];
            unset($cart[$id]);
        }
        if($qt < 0)
        {
            $qt = 0;    
        }
        $session->set('qt', $qt);
        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }
}
