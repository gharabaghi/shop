<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductController extends AbstractController
{
    /**
    * @Route("/products/{id}", name="product_show")
    */
    public function show(Product $product, EntityManagerInterface $em, ProductRepository $productRepo): Response
    {
        $product->increaseVisit();
        $em->flush();

        $similarProducts = $productRepo->findBy([], ['createdAt'=> 'DESC'], 4);

        return $this->render('product.html.twig', [
            'product' => $product,
            'similarProducts' => $similarProducts
        ]);
    }
}
