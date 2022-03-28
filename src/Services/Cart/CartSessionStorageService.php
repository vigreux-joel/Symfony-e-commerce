<?php


namespace App\Services\Cart;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartSessionStorageService
{
    /**
     * The request stack.
     *
     * @var RequestStack
     */
    private RequestStack $requestStack;

    /**
     * The cart repository.
     *
     * @var OrderRepository
     */
    private OrderRepository $cartRepository;

    /**
     * The cart repository.
     *
     * @var OrderItemRepository
     */
    private OrderItemRepository $orderItemRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var string
     */
    public const CART_KEY_NAME = 'cart_id';



    /**
     * CartSessionStorage constructor.
     *
     * @param RequestStack $requestStack
     * @param OrderRepository $cartRepository
     * @param OrderItemRepository $orderItemRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        RequestStack $requestStack,
        OrderRepository $cartRepository,
        OrderItemRepository $orderItemRepository,
        EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->cartRepository = $cartRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Gets the cart in session.
     *
     * @param User|null $user
     * @return Order|null
     */
    public function getCart(?User $user): ?Order
    {
        $orders = [];
        if(isset($user)) {
            $orders = $this->cartRepository->findBy([
                'userRef' => $user,
                'status' => Order::STATUS_CART
            ]);
        }

        $orderSession =  $this->cartRepository->findOneBy([
            'id' => $this->getCartId(),
            'status' => Order::STATUS_CART
        ]);
        if(isset($orderSession) && !in_array($orderSession, $orders, true)) {
            $orders[] = $orderSession;
        }

        if(count($orders)>1){
            for($i = 1; $i<=count($orders)-1; $i++){
                $idOrder = $orders[$i]->getId();


                foreach($this->orderItemRepository->findBy([
                    'orderRef' => $idOrder
                ]) as $item){
                    $orders[0]->addItem($item);
                }
                $this->entityManager->remove($orders[$i]);
                $this->entityManager->flush();
            }
        }



        return $orders[0]??null;
    }

    /**
     * Sets the cart in session.
     *
     * @param Order $cart
     */
    public function setCart(Order $cart): void
    {
        $this->getSession()->set(self::CART_KEY_NAME, $cart->getId());
    }

    /**
     * Returns the cart id.
     *
     * @return int|null
     */
    private function getCartId(): ?int
    {
        return $this->getSession()->get(self::CART_KEY_NAME);
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}