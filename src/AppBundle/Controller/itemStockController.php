<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Item;
use AppBundle\Lib\Stock\Form\SearchDto;
use AppBundle\Lib\Stock\Form\SearchFormType;
use AppBundle\Lib\Stock\StockService;
use AppBundle\Lib\ViewHelperTrait;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class itemStockController extends Controller
{
    use ViewHelperTrait;

    /**
     * @param Item $item
     * @param Request $request
     * @return View
     */
    public function indexAction(Item $item, Request $request) :View
    {
        $search = new SearchDto();
        $form = $this->createForm(SearchFormType::class, $search, ['method' => 'GET']);
        $form->get('filter')->remove('item');
        $form->handleRequest($request);
        /**
         * @var SearchDto $data
         */
        $data = $form->getData();
        $data->getFilter()->setItem([$item->getId()]);

        $res = $this->getService()->search($data)->execute()->fetchAll();

        return $this->createView($res);
    }

    public function stockUpAction(Item $item, Request $request) :View
    {

    }

    /**
     * @return StockService
     */
    public function getService() :StockService
    {
        return $this->get('app.stock.service');
    }
}