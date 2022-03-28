<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\AddToCartType;
use App\Form\SearchType;
use App\Services\Cart\CartManagerService;
use App\Services\SearchService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/nos-produits", name="products")
     */
    public function index(Request $request): Response
    {
        $search = new SearchService();

        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $products = $this->entityManager->getRepository(Product::class)->findWithSearch($search);
        } else {
            $products = $this->entityManager->getRepository(Product::class)->findAll();
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/nos-produits/{slug}", name="product_detail")
     */
    public function detail($slug, Request $request, CartManagerService $cartManager)
    {
        //show($slug): Response
        $form = $this->createForm(AddToCartType::class);
        $form->handleRequest($request);
        $product = $this->entityManager->getRepository(Product::class)->findOneBySlug($slug);

        if ($form->isSubmitted() && $form->isValid()) {

            $item = $form->getData();
            $item->setProduct($product);

            $cart = $cartManager->getCurrentCart($this->getUser());
            $cart
                ->addItem($item)
                ->setUpdatedAt(new \DateTime());

            $cartManager->save($cart, $this->getUser());

            return $this->redirectToRoute('products');
        }


        return $this->render('product/show.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }
}
