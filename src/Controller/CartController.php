<?php

namespace App\Controller;

use App\Classe\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{

    /**
     * @Route("/mon-panier", name="cart")
     */
    public function index(Cart $cart): Response
    {

        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getAll()
        ]);
    }

    /**
     * @Route("/mon-panier/ajouter/{id}", name="cart_add")
     */
    public function add(Cart $cart, $id): Response
    {
        $cart->add($id);
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/mon-panier/vider", name="cart_remove_all")
     */
    public function removeAll(Cart $cart): Response
    {
        $cart->remove();
        return $this->redirectToRoute('products');
    }

    /**
     * @Route("/mon-panier/vider/{id}", name="cart_remove_by_id")
     */
    public function removeById(Cart $cart, $id): Response
    {
        $cart->remove($id);
        if(!$cart->get()){
            return $this->redirectToRoute('products');
        }


        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/mon-panier/retirer/{id}", name="cart_decrease")
     */
    public function decreaseById(Cart $cart, $id): Response
    {
        $cart->decrease($id);
        if(!$cart->get()){
            return $this->redirectToRoute('products');
        }

        return $this->redirectToRoute('cart');
    }
}
