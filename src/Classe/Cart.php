<?php

namespace App\Classe;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @ApiResource()
 */
class Cart
{
    private $session;
    private $entityManager;

    public function __construct(SessionInterface $session, EntityManagerInterface $entityManager){
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    public function add($id): void
    {
        $cart = $this->session->get('cart', []);
        $cart[$id]??=0;
        $cart[$id]++;
        $this->session->set('cart', $cart);
    }
    public function get(){
        return $this->session->get('cart')??[];
    }
    public function getAll(){
        $cartComplete = [];

        foreach ($this->get() as $id => $quantity){
            $productObject = $this->entityManager->getRepository(Product::class)->findOneById($id);

            if(!$productObject){
                $this->remove($id);
                continue;
            }

            $cartComplete[] = [
                'product' => $productObject,
                'quantity' => $quantity,
            ];
        }
        return $cartComplete;
    }

    public function remove($id = false): void
    {
        if(!$id){
            $this->session->remove('cart');
            return;
        }
        $cart = $this->get();
        unset($cart[$id]);
        $this->session->set('cart', $cart);
    }

    public function decrease($id): void
    {
        $cart = $this->get();

        if($cart[$id] >1){
            $cart[$id]--;
            $this->session->set('cart', $cart);
        } else {
            $this->remove($id);
        }
    }

}