<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Item;
use AppBundle\Lib\Item\Form\CreateUpdateFormType;
use AppBundle\Lib\Item\Form\SearchDto;
use AppBundle\Lib\Item\Form\SearchFormType;
use AppBundle\Lib\Item\ItemService;
use AppBundle\Lib\ValidationException;
use AppBundle\Lib\ViewHelperTrait;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ItemController extends Controller
{
    use ViewHelperTrait;

    /**
     * Display a list of items, the list can optionally be filtered by filter and sorted by sort
     *
     * @ApiDoc(
     *  resource=true,
     *  resourceDescription="Item and product managemment",
     *  section="Products",
     *  statusCodes={
     *          200="Returned when successful",
     *          403="Returned when the user is not authorized to say hello"
     *  },
     *  input="AppBundle\Lib\Item\Form\SearchFormType",
     *  output={"class"="Appbundle\Entity\Item", "collection"=true, "groups"={"item", "meta"}}
     * )
     *
     * @Rest\Get("items",
     *     name="api_items_index",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     * @param Request $request
     * @return View
     */
    public function indexAction(Request $request) :View
    {
        $search = new SearchDto();
        $form = $this->createForm(SearchFormType::class, $search, ['method' => 'GET']);
        $form->handleRequest($request);

        /**
         * @var SearchDto $data
         */
        $data = $form->getData();
        $res = $this->getService()->search($search)->getQuery()->getResult();


        return $this->createView($res, 200, [], ['item', 'meta']);
    }

    /**
     * Creates a new item, after creation you will find the link to the new item in the header or will redirected if your client supports that.
     * After creation you will need to create products based on that item.
     * @ApiDoc(
     *  section="Products",
     *  description="Creates a new item",
     *  statusCodes={
     *          201="Returned when successful",
     *          403="Returned when the user is not authorized to say hello",
     *          400="Returned in case of invalid input"
     *  },
     *  input="AppBundle\Lib\Item\Form\CreateUpdateFormType"
     * )
     *
     * @Rest\Post("items",
     *     name="api_items_create",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     * @param Request $request
     * @return View
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createAction(Request $request) :View
    {
        $item = new Item();
        $form = $this->createForm(CreateUpdateFormType::class, $item, ['method' => 'POST']);
        $form->handleRequest($request);
        /**
         * @var Item $data
         */
        $data = $form->getData();

        try {
            $item = $this->getService()->create($data);

            return View::createRouteRedirect('api_items_show', ['item' => $item->getId()], 201);

        } catch (ValidationException $ve) {
            return $this->createView($ve->getValidationErrors(), 400);
        }
    }

    /**
     * @ApiDoc(
     *  section="Products",
     *  description="Details of the given item",
     *  statusCodes={
     *          200="Returned when successful",
     *          403="Returned when the user is not authorized to say hello"
     *  },
     *  output={"class"="AppBundle\Entity\Item", "groups"={"item", "meta", "item_products", "product_minimal"}}
     * )
     *
     * @Rest\Get("items/{item}",
     *     name="api_items_show",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     *
     * @param Request $request
     * @param Item $item
     * @return View
     */
    public function showAction(Request $request, Item $item) :View
    {
        return $this->createView($item, 200, [], ['item', 'item_products', 'product_minimal', 'meta']);
    }

    /**
     * @ApiDoc(
     *  section="Products",
     *  description="Updated the given item",
     *  statusCodes={
     *          201="Returned when successful",
     *          403="Returned when the user is not authorized to say hello",
     *          400="Returned in case of invalid input"
     *  },
     *  input="AppBundle\Lib\Item\Form\CreateUpdateFormType"
     * )
     *
     * @Rest\Put("items/{item}",
     *     name="api_items_update",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     *
     * @param Request $request
     * @param Item $item
     * @return View
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateAction(Request $request, Item $item) :View
    {
        $form = $this->createForm(CreateUpdateFormType::class, $item, ['method' => 'PUT']);
        $form->handleRequest($request);
        /**
         * @var Item $data
         */
        $data = $form->getData();

        try {
            $item = $this->getService()->create($data);

            return View::createRouteRedirect('', ['item' => $item->getId()], 204);

        } catch (ValidationException $ve) {
            return $this->createView($ve->getValidationErrors(), 400);
        }
    }

    /**
     * @return ItemService
     */
    private function getService()
    {
        return $this->get('app.item.service');
    }
}