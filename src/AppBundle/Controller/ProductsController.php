<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Products\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProductsController extends Controller
{
    /**
     * @Route("/products")
     */
    public function listProducts()
    {
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();
        $products = array_map(function($p) {return array('name' => $p.getName, 'price' => $p.getPrice(), 'description' => $p.getDescription());}, $products);
        return new JsonResponse($products);
    }
}