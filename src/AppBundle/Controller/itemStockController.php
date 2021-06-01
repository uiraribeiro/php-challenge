<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Item;
use AppBundle\Lib\Stock\Form\SearchDto;
use AppBundle\Lib\Stock\Form\SearchFormType;
use AppBundle\Lib\Stock\Form\StockUpRequestDto;
use AppBundle\Lib\Stock\Form\StockUpRequestFormType;
use AppBundle\Lib\Stock\StockService;
use AppBundle\Lib\ValidationException;
use AppBundle\Lib\ViewHelperTrait;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class itemStockController extends Controller
{
    use ViewHelperTrait;

    /**
     * @ApiDoc(
     *  section="Products",
     *  statusCodes={
     *          200="Returned when successful",
     *          403="Returned when the user is not authorized to say hello"
     *  },
     *  input="AppBundle\Lib\Stock\Form\SearchFormType",
     *  output={"class"="Appbundle\Lib\Stock\StockItem", "collection"=true}
     * )
     *
     * @Rest\Get("{item}/stock",
     *     name="api_item_stock_index",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     *
     * @param Request $request
     * @return View
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

    /**
     * @ApiDoc(
     *  section="Products",
     *  description="Stocks up an item",
     *  statusCodes={
     *          201="Returned when successful",
     *          403="Returned when the user is not authorized to say hello",
     *          400="Returned in case of invalid input"
     *  },
     *  input="AppBundle\Lib\Stock\Form\StockUpRequestFormType"
     * )
     *
     * @Rest\Post("{item}/stock",
     *     name="api_item_stock_up",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     *
     * @param Item $item
     * @param Request $request
     * @return View
     */
    public function stockUpAction(Item $item, Request $request) :View
    {
        $stock = new StockUpRequestDto();
        $form = $this->createForm(StockUpRequestFormType::class, $stock);
        $form->remove('item');
        $form->handleRequest($request);

        try {
            /**
             * @var StockUpRequestDto $data
             */
            $data = $form->getData();
            $data->setItem($item);

            $this->getService()->stockUp($data);

            return View::createRouteRedirect('api_item_stock_index', ['item' => $item->getId()], 201);

        } catch (ValidationException $ve) {
            return $this->createView($ve);
        }
    }

    /**
     * @return StockService
     */
    public function getService() :StockService
    {
        return $this->get('app.stock.service');
    }
}