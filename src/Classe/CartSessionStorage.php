<?php


namespace App\Classe;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartSessionStorage
{
    /**
     * The request stack.
     *
     * @var RequestStack
     */
    private $requestStack;

    /**
     * The cart repository.
     *
     * @var OrderRepository
     */
    private $cartRepository;

    /**
     * The cart repository.
     *
     * @var OrderItemRepository
     */
    private $orderItemRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    const CART_KEY_NAME = 'cart_id';



    /**
     * CartSessionStorage constructor.
     *
     * @param RequestStack $requestStack
     * @param OrderRepository $cartRepository
     * @param OrderItemRepository $orderItemRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(RequestStack $requestStack,
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
     * @return Order|null
     */
    public function getCart(?User $user): ?Order
    {
        $orders = [];

        if(isset($user))
        $orders = $this->cartRepository->findBy([
            'userRef' => $user,
            'status' => Order::STATUS_CART
        ]);

        $orderSession =  $this->cartRepository->findOneBy([
            'id' => $this->getCartId(),
            'status' => Order::STATUS_CART
        ]);
        if(isset($orderSession) && !in_array($orderSession, $orders, true)) {
            $orders[] = $orderSession;
        }

        if(count($orders)>1){
            //pour toutes les commandes de trop
            for($i = 1; $i<=count($orders)-1; $i++){
                $idOrder = $orders[$i]->getId();

                //pour tout les items de ces commandes
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