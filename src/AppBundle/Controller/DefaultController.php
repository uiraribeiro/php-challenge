<?php


namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    /**
     * @return RedirectResponse
     * @Route("", name="route")
     */
    public function indexAction() :RedirectResponse
    {
        return $this->redirect('/api/doc');
    }
}