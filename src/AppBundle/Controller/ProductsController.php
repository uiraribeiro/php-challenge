<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Products\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProductsController extends Controller
{
    /**
     * @Route("/products")
     * @Method("GET")
     */
    public function listProducts()
    {
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();
        $products = array_map(function($p) {return array('id'=>$p->getId(), 'name' => $p->getName(), 'price' => $p->getPrice(), 'description' => $p->getDescription());}, $products);
        return new JsonResponse($products);
    }

    /**
     * @Route("/products")
     * @Method("POST")
     */
    public function createProduct(Request $request)
    {
        $body = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setName($body['name']);
        $product->setPrice($body['price']);
        $product->setDescription($body['description']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($product);
        $entityManager->flush();


        return new JsonResponse(array('id'=>$product->getId(), 'name' => $product->getName(), 'price' => $product->getPrice(), 'description' => $product->getDescription()));
    }
}