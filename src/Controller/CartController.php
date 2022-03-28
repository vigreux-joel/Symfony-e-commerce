<?php

namespace App\Controller;

use App\Form\CartType;
use App\Services\Cart\CartService;
use App\Services\Cart\CartManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{

    /**
     * @Route("/mon-panier", name="cart")
     */
    public function index(CartManagerService $cartManager, Request $request): Response
    {
        $cart = $cartManager->getCurrentCart($this->getUser());

        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cart->setUpdatedAt(new \DateTime());
            $cartManager->save($cart, $this->getUser());

            return $this->redirectToRoute('cart');
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mon-panier/ajouter/{id}", name="cart_add")
     */
    public function add(CartService $cart, $id): Response
    {
        $cart->add($id);
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/mon-panier/vider", name="cart_remove_all")
     */
    public function removeAll(CartService $cart): Response
    {
        $cart->remove();
        return $this->redirectToRoute('products');
    }

    /**
     * @Route("/mon-panier/vider/{id}", name="cart_remove_by_id")
     */
    public function removeById(CartService $cart, $id): Response
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
    public function decreaseById(CartService $cart, $id): Response
    {
        $cart->decrease($id);
        if(!$cart->get()){
            return $this->redirectToRoute('products');
        }

        return $this->redirectToRoute('cart');
    }
}
