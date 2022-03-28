<?php

namespace App\Services\Cart;
use App\Entity\Order;
use App\Services\OrderFactoryService;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class CartManager
 * @package App\Manager
 */
class CartManagerService
{
    /**
     * @var CartSessionStorageService
     */
    private CartSessionStorageService $cartSessionStorage;

    /**
     * @var OrderFactoryService
     */
    private OrderFactoryService $cartFactory;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * CartManager constructor.
     *
     * @param CartSessionStorageService $cartStorage
     * @param OrderFactoryService $orderFactory
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        CartSessionStorageService $cartStorage,
        OrderFactoryService       $orderFactory,
        EntityManagerInterface    $entityManager
    ) {
        $this->cartSessionStorage = $cartStorage;
        $this->cartFactory = $orderFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * Gets the current cart.
     *
     * @param $user
     * @return Order
     */
    public function getCurrentCart($user): Order
    {

        $cart = $this->cartSessionStorage->getCart($user);

        if (!$cart) {
            $cart = $this->cartFactory->create();
        }

        return $cart;
    }

    /**
     * Persists the cart in database and session.
     *
     * @param Order $cart
     * @param $user
     */
    public function save(Order $cart, $user): void
    {
        // Persist in database

        $cart->setUserRef($user);
        $this->entityManager->persist($cart);
        $this->entityManager->flush();


        // Persist in session
        $this->cartSessionStorage->setCart($cart);
    }
}
