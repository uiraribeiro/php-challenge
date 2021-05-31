<?php


namespace AppBundle\Controller;



use AppBundle\Entity\Item;
use AppBundle\Entity\Product;
use AppBundle\Lib\Entity\NotTrackedException;
use AppBundle\Lib\Product\Form\CreateUpdateFormType;
use AppBundle\Lib\Product\Form\SearchDto;
use AppBundle\Lib\Product\Form\SearchFormType;
use AppBundle\Lib\Product\ProductService;
use AppBundle\Lib\ValidationException;
use AppBundle\Lib\ViewHelperTrait;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ItemProductsController extends Controller
{

    use ViewHelperTrait;

    /**
     * Display a list of products relating to the given item, the list can optionally be filtered by filter
     * and sorted by sort
     *
     * @ApiDoc(
     *  section="Products",
     *  statusCodes={
     *          200="Returned when successful",
     *          403="Returned when the user is not authorized to say hello"
     *  },
     *  input="AppBundle\Lib\Product\Form\SearchFormType",
     *  output={"class"="Appbundle\Entity\Product", "collection"=true, "groups"={"product", "meta"}}
     * )
     *
     * @Rest\Get("{item}/products",
     *     name="api_item_products_index",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     *
     * @param Request $request
     * @return View
     */
    public function indexAction(Request $request, Item $item) :View
    {
        $search = new SearchDto();
        $form = $this->createForm(SearchFormType::class, $search, ['method' => 'GET']);
        $form->handleRequest($request);

        /**
         * @var SearchDto $data
         */
        $data = $form->getData();
        $data->getFilter()->setItem([$item->getId()]);
        $res = $this->getService()->search($data)->getQuery()->getResult();


        return $this->createView($res, 200, [], ['product', 'meta']);
    }

    /**
     * Creates a new product, after creation you will find the link to the new product in the header or will redirected if your client supports that.
     * @ApiDoc(
     *  section="Products",
     *  description="Creates a new item",
     *  statusCodes={
     *          201="Returned when successful",
     *          403="Returned when the user is not authorized to say hello",
     *          400="Returned in case of invalid input"
     *  },
     *  input="AppBundle\Lib\Product\Form\CreateUpdateFormType"
     * )
     *
     * @Rest\Post("{item}/products",
     *     name="api_item_products_create",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     *
     * @param Request $request
     * @return View
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createAction(Request $request, Item $item) :View
    {
        $product = ProductService::getPrototype($item);
        $form = $this->createForm(CreateUpdateFormType::class, $product);
        $form->remove('item');
        $form->handleRequest($request);
        /**
         * @var Product $data
         */
        $data = $form->getData();

        try {
            $product = $this->getService()->create($data);

            return View::createRouteRedirect('api_item_products_show',
                ['item' => $item->getId(), 'product' => $product->getId()],
                201);

        } catch (ValidationException $ve) {
            return $this->createView($ve);
        }
    }

    /**
     * @ApiDoc(
     *  section="Products",
     *  description="Details of the given product",
     *  statusCodes={
     *          200="Returned when successful",
     *          403="Returned when the user is not authorized to say hello"
     *  },
     *  output={"class"="AppBundle\Entity\Product", "groups"={"product", "meta"}}
     * )
     *
     * @Rest\Get("{item}/products/{product}",
     *     name="api_item_products_show",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     * @param Product $product
     * @return View
     */
    public function showAction(Product $product) :View
    {
        return $this->createView($product, 200, [], ['product', 'meta']);
    }

    /**
     * Updates the given product, depending on the state not all of the properties can be edited
     * @ApiDoc(
     *  section="Products",
     *  description="Creates a new item",
     *  statusCodes={
     *          201="Returned when successful",
     *          403="Returned when the user is not authorized to say hello",
     *          400="Returned in case of invalid input"
     *  },
     *  input="AppBundle\Lib\Product\Form\CreateUpdateFormType"
     * )
     *
     * @Rest\Patch("{item}/products/{product}",
     *     name="api_item_products_update",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     * @param Request $request
     * @return View
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws NotTrackedException
     */
    public function updateAction(Request $request, Item $item, Product $product) :View
    {
        $form = $this->createForm(CreateUpdateFormType::class, $product, ['method' => 'PATCH']);
        $form->remove('item');
        $form->handleRequest($request);
        /**
         * @var Product $data
         */
        $data = $form->getData();

        try {
            $product = $this->getService()->update($data);

            return View::createRouteRedirect('api_item_products_show',
                ['item' => $item->getId(), 'product' => $product->getId()],
                204);

        } catch (ValidationException $ve) {
            return $this->createView($ve);
        }
    }

    /**
     * @return ProductService
     */
    private function getService() :ProductService
    {
        return $this->get('app.product.service');
    }
}