<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    private $session;

    public function __construct(SessionInterface $session){
        $this->session = $session;
    }

    public function add($id){
        $cart = $this->session->get('cart', []);
        $cart[$id]??=0;
        $cart[$id]++;
        $this->session->set('cart', $cart);
    }
    public function get(){
        return $this->session->get('cart');
    }

    public function remove(): void
    {
        $this->session->remove('cart');
    }

}