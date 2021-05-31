<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Customer;
use AppBundle\Lib\Customer\CustomerService;
use AppBundle\Lib\Customer\Form\CreateUpdateFormType;
use AppBundle\Lib\Customer\Form\SearchDto;
use AppBundle\Lib\Customer\Form\SearchFormType;
use AppBundle\Lib\ValidationException;
use AppBundle\Lib\ViewHelperTrait;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CustomersController extends Controller
{

    use ViewHelperTrait;

    /**
     * Returns a list of customers, the list can be filtered and sorted
     * @ApiDoc(
     *  resource=true,
     *  resourceDescription="Customer management",
     *  section="Customer",
     *  statusCodes={
     *          200="Returned when successful",
     *          403="Returned when the user is not authorized to say hello"
     *  },
     *  input="AppBundle\Lib\Customer\Form\SearchFormType",
     *  output={"class"="Appbundle\Entity\Customer", "collection"=true, "groups"={"customer", "meta"}}
     * )
     *
     * @Rest\Get("customers",
     *     name="api_customers_index",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     *
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
        $res = $this->getService()->search($data)->getQuery()->getResult();

        return $this->createView($res, 200, [], ['customer', 'meta']);
    }

    /**
     * Creates a new customer, after creation you will find the link to the new customer in the header or will redirected if your client supports that.
     * @ApiDoc(
     *  section="Customer",
     *  description="Creates a new customer",
     *  statusCodes={
     *          201="Returned when successful",
     *          403="Returned when the user is not authorized to say hello",
     *          400="Returned in case of invalid input"
     *  },
     *  input="AppBundle\Lib\Customer\Form\CreateUpdateFormType"
     * )
     *
     * @Rest\Post("customers",
     *     name="api_customers_create",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     *
     * @param Request $request
     * @return View
     */
    public function createAction(Request $request) :View
    {
        $customer = new Customer();
        $form = $this->createForm(CreateUpdateFormType::class, $customer, ['method' => 'POST']);
        $form->handleRequest($request);

        $service = $this->getService();

        try {
            $customer = $service->create($form->getData());

            return View::createRouteRedirect('api_customers_show', ['customer' => $customer->getId()], 201);
        } catch (ValidationException $v) {
            return $this->createView($v->getValidationErrors(), 400);
        }
    }

    /**
     * Displays the properties of the given customer
     * @ApiDoc(
     *  section="Customer",
     *  description="Details of the given customer",
     *  statusCodes={
     *          200="Returned when successful",
     *          403="Returned when the user is not authorized to say hello"
     *  },
     *  output={"class"="AppBundle\Entity\Customer", "groups"={"customer", "meta", "customer_stores", "store"}}
     * )
     *
     * @Rest\Get("customers/{customer}",
     *     name="api_customers_show",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     *
     * @param Customer $customer
     * @param Request $request
     * @return View
     */
    public function showAction(Customer $customer, Request $request) :View
    {
        return $this->createView($customer, 200, [], ['customer', 'meta', 'customer_stores', 'store']);
    }

    /**
     * Updates the given customer
     * @ApiDoc(
     *  section="Customer",
     *  statusCodes={
     *          201="Returned when successful",
     *          403="Returned when the user is not authorized to say hello",
     *          400="Returned in case of invalid input"
     *  },
     *  input="AppBundle\Lib\Customer\Form\CreateUpdateFormType",
     *  output={"class"="AppBundle\Entity\Customer", "groups"={"customer", "meta", "customer_stores", "store"}}
     * )
     *
     * @Rest\Put("customers/{customer}",
     *     name="api_customers_update",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     *
     * @param Customer $customer
     * @param Request $request
     * @return View
     */
    public function updateAction(Customer $customer, Request $request) :View
    {
        $form = $this->createForm(CreateUpdateFormType::class, $customer, ['method' => 'PUT']);
        $form->handleRequest($request);

        $service = $this->getService();

        try {
            $customer = $service->update($form->getData());

            return View::createRouteRedirect('api_customers_show', ['customer' => $customer->getId()], 200);

        } catch (ValidationException $v) {
            return $this->createView($v->getValidationErrors(), 400);
        }
    }

    /**
     * @return CustomerService|object
     */
    private function getService() :CustomerService
    {
        return $this->get('app.customer.service');
    }
}