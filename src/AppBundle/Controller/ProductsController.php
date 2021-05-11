<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductsController
{
    /**
     * @Route("/products")
     */
    public function listProducts()
    {
        return new JsonResponse(
            array(array('name' => 'Cloud on demand'))
        );
    }
}