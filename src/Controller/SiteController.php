<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\MarketService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ProductRepository $productRepo): Response
    {
        $products = $productRepo->findAll();
        // dd($products[0]);

        return $this->render('index.html.twig', [
            'products'=>$products
        ]);
    }
}
