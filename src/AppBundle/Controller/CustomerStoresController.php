<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Customer;
use AppBundle\Entity\Store;
use AppBundle\Entity\Subscription;
use AppBundle\Lib\Product\ProductService;
use AppBundle\Lib\Store\Form\CreateUpdateFormType;
use AppBundle\Lib\Store\Form\SearchDto;
use AppBundle\Lib\Store\Form\SearchFormType;
use AppBundle\Lib\Store\StoreService;
use AppBundle\Lib\Subscription\Form\SearchDto AS SubscriptionSearchDto;
use AppBundle\Lib\Product\Form\SearchDto AS ProductSearchDto;
use AppBundle\Lib\Product\Form\SearchFormType AS ProductSearchForm;
use AppBundle\Lib\Subscription\Form\StoreSubscriptionSearchFormType;
use AppBundle\Lib\Subscription\Form\SubscriptionCancellationRequestDto;
use AppBundle\Lib\Subscription\Form\SubscriptionCancellationRequestFormType;
use AppBundle\Lib\Subscription\Form\SubscriptionRequestDto;
use AppBundle\Lib\Subscription\Form\SubscriptionRequestFormType;
use AppBundle\Lib\Subscription\SubscriptionService;
use AppBundle\Lib\ValidationException;
use AppBundle\Lib\ViewHelperTrait;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CustomerStoresController
 * @package AppBundle\Controller
 */
class CustomerStoresController extends Controller
{

    use ViewHelperTrait;

    /**
     * Returns a list of stores for the given customer
     * @ApiDoc(
     *  section="Customer",
     *  statusCodes={
     *          200="Returned when successful",
     *          403="Returned when the user is not authorized to say hello"
     *  },
     *  input="AppBundle\Lib\Stores\Form\SearchFormType",
     *  output={"class"="Appbundle\Entity\Stores", "collection"=true, "groups"={"store", "meta"}}
     * )
     * @Rest\Get("/{customer}/stores",
     *     name="api_customer_stores_index",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     *
     * @param Customer $customer
     * @param Request $request
     * @return View
     */
    public function indexAction(Customer $customer, Request $request) :View
    {
        $search = new SearchDto();
        $form = $this->createForm(SearchFormType::class, $search, ['method' => 'GET']);
        $form->handleRequest($request);

        $repo = $this->getDoctrine()->getManager()->getRepository(Customer::class);
        /**
         * @var SearchDto $data
         */
        $data = $form->getData();
        $data->getFilter()->setCustomer([$customer->getId()]);
        $res = $this->getService()->search($data)->getQuery()->getResult();

        return $this->createView($res, 200, [], ['store', 'meta']);
    }

    /**
     * Creates a new store
     * @ApiDoc(
     *  section="Customer",
     *  statusCodes={
     *          201="Returned when successful",
     *          403="Returned when the user is not authorized to say hello",
     *          400="Returned in case of invalid input"
     *  },
     *  input="AppBundle\Lib\Store\Form\CreateUpdateFormType",
     *  output={"class"="AppBundle\Entity\Store", "groups"={"store", "store_subscriptions", "subscription", "meta"}}
     * )
     *
     * @Rest\Post("/{customer}/stores",
     *     name="api_customer_stores_create",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     *
     * @param Customer $customer
     * @param Request $request
     * @return View
     */
    public function createAction(Customer $customer, Request $request) :View
    {
        $store = StoreService::createPrototype($customer);
        $form = $this->createForm(CreateUpdateFormType::class, $store, ['method' => 'POST']);
        $form->handleRequest($request);

        $service = $this->getService();

        try {
            $data = $form->getData();
            $data->setCustomer($customer);
            $store = $service->create($data);

            return View::createRouteRedirect('api_customer_stores_show', ['customer' => $customer->getId(), 'store' => $store->getId()], 201);

        } catch (ValidationException $v) {
            return $this->createView($v->getValidationErrors(), 400);
        }
    }

    /**
     * Updates the given store
     * @ApiDoc(
     *  section="Customer",
     *  statusCodes={
     *          201="Returned when successful",
     *          403="Returned when the user is not authorized to say hello",
     *          400="Returned in case of invalid input"
     *  },
     *  input="AppBundle\Lib\Store\Form\CreateUpdateFormType",
     *  output={"class"="AppBundle\Entity\Store", "groups"={"store", "store_subscriptions", "subscription", "meta"}}
     * )
     *
     * @Rest\Put("/{customer}/stores/{store}",
     *     name="api_customer_stores_update",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     *
     * @param Customer $customer
     * @param Request $request
     * @return View
     */
    public function updateAction(Customer $customer, Store $store, Request $request) :View
    {
        $form = $this->createForm(CreateUpdateFormType::class, $store, ['method' => 'PUT']);
        $form->handleRequest($request);

        $service = $this->getService();

        try {
            $data = $form->getData();
            $data->setCustomer($customer);
            $store = $service->update($data);

            return View::createRouteRedirect('api_customer_stores_show', ['customer' => $customer->getId(), 'store' => $store->getId()], 204);

        } catch (ValidationException $v) {
            return $this->createView($v->getValidationErrors(), 400);
        }
    }

    /**
     * Shows the properties of the given store
     * @ApiDoc(
     *  section="Customer",
     *  description="Details of the given store",
     *  statusCodes={
     *          200="Returned when successful",
     *          403="Returned when the user is not authorized to say hello"
     *  },
     *  output={"class"="AppBundle\Entity\Customer", "groups"={"store", "store_subscriptions", "subscription", "meta"}}
     * )
     *
     * @Rest\Get("/{customer}/stores/{store}",
     *     name="api_customer_stores_show",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     *
     * @param Customer $customer
     * @param Store $store
     * @return View
     */
    public function showAction(Customer $customer, Store $store) :View
    {
        $this->matchStoreToCustomer($customer, $store);

        return $this->createView($store, 200, [], ['store', 'store_subscriptions', 'subscription', 'meta']);
    }

    /**
     * List the subscriptions of the given store
     * @ApiDoc(
     *  section="Customer",
     *  statusCodes={
     *          201="Returned when successful",
     *          403="Returned when the user is not authorized to say hello",
     *          400="Returned in case of invalid input"
     *  },
     *  input="AppBundle\Lib\Subscription\Form\StoreSubscriptionSearchFormType",
     *  output={"class"="Appbundle\Entity\Subscription", "collection"=true, "groups"={"subscription", "meta"}}
     * )
     *
     * @Rest\Get("{customer}/stores/{store}/subscriptions",
     *     name="api_customer_stores_subscriptions_list",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     *
     * @param Customer $customer
     * @param Store $store
     * @param Request $request
     * @return View
     */
    public function listSubscriptionsAction(Customer $customer, Store $store, Request $request) :View
    {
        $this->matchStoreToCustomer($customer, $store);

        $searchDto = new SubscriptionSearchDto();
        $form = $this->createForm(StoreSubscriptionSearchFormType::class, $searchDto, ['method' => 'GET']);

        $form->handleRequest($request);

        /**
         * @var SubscriptionSearchDto $data
         */
        $data = $form->getData();
        $data->getFilter()->setCustomer([$customer->getId()]);
        $data->getFilter()->setStore([$store->getId()]);

        $res = $this->getSubscriptionService()->search($data)->getQuery()->getResult();

        return $this->createView($res, 200, [], ['subscription', 'meta']);
    }

    /**
     * Creates a new subscription in the given store
     * @ApiDoc(
     *  section="Customer",
     *  statusCodes={
     *          201="Returned when successful",
     *          403="Returned when the user is not authorized to say hello",
     *          400="Returned in case of invalid input"
     *  },
     *  input="AppBundle\Lib\Subscription\Form\SubscriptionRequestFormType",
     *  output={"class"="AppBundle\Entity\Subscription", "groups"={"subscription", "meta", "include_shipments", "shipment"}}
     * )
     *
     * @Rest\Post("{customer}/stores/{store}/subscriptions",
     *     name="api_customer_stores_subscription_create",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     *
     * @param Customer $customer
     * @param Store $store
     * @param Request $request
     * @return View
     */
    public function createSubscriptionAction(Customer $customer, Store $store, Request $request) :View
    {
        $this->matchStoreToCustomer($customer, $store);
        $subscriptionRequest = new SubscriptionRequestDto();
        $subscriptionRequest->setStore($store);
        $form = $this->createForm(SubscriptionRequestFormType::class, $subscriptionRequest, ['method' => 'POST']);
        $form->remove('store');
        $form->handleRequest($request);

        $subscriptionRequest = $form->getData();
        $subscriptionRequest->setStore($store);

        try {
            $subscription = $this->getSubscriptionService()->create($subscriptionRequest);
            return View::createRouteRedirect('api_customer_stores_subscriptions_show',
                ['customer' => $customer->getId(), 'store' => $store->getId(), 'subscription' => $subscription->getId()], 201);

        } catch(ValidationException $v) {
            return $this->createView($v->getValidationErrors(), 400);
        }
    }

    /**
     * Shows the details of the given subscription
     * @ApiDoc(
     *  section="Customer",
     *  description="Details of the given subscription",
     *  statusCodes={
     *          200="Returned when successful",
     *          403="Returned when the user is not authorized to say hello"
     *  },
     *  output={"class"="AppBundle\Entity\Customer", "groups"={"subscription", "include_shipments", "shipment", "meta"}}
     * )
     * @Rest\Get("{customer}/stores/{store}/subscriptions/{subscription}",
     *     name="api_customer_stores_subscriptions_show",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     *
     * @param Customer $customer
     * @param Store $store
     * @param Subscription $subscription
     * @param Request $request
     * @return View
     */
    public function showSubscriptionAction(Customer $customer, Store $store, Subscription $subscription, Request $request) :View
    {
        $this->matchStoreToCustomer($customer, $store);
        $this->matchSubscriptionToStore($subscription, $store);

        return $this->createView($subscription, 200, [], ['subscription', 'meta', 'include_shipments', 'shipment']);
    }

    /**
     * Cancels the given subscription
     * @ApiDoc(
     *  section="Customer",
     *  statusCodes={
     *          204="Returned when successful",
     *          403="Returned when the user is not authorized to say hello",
     *          400="Returned in case of invalid input"
     *  },
     *  input="AppBundle\Lib\Subscription\Form\SubscriptionCancellationRequestFormType",
     *  output={"class"="AppBundle\Entity\Subscription", "groups"={"subscription", "meta", "include_shipments", "shipment"}}
     * )
     *
     * @Rest\Post("{customer}/stores/{store}/subscriptions/{subscription}/cancel",
     *     name="api_customer_stores_subscriptions_cancel",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     *
     * @param Customer $customer
     * @param Store $store
     * @param Subscription $subscription
     * @param Request $request
     * @return View
     */
    public function cancelSubscriptionAction(Customer $customer, Store $store, Subscription $subscription, Request $request) :View
    {
        $this->matchStoreToCustomer($customer, $store);
        $this->matchSubscriptionToStore($subscription, $store);

        $cancellationRequest = new SubscriptionCancellationRequestDto();
        $form = $this->createForm(SubscriptionCancellationRequestFormType::class, $cancellationRequest, ['method' => 'POST']);
        $form->handleRequest($request);
        /**
         * @var SubscriptionCancellationRequestDto $data
         */
        $data = $form->getData();
        $data->setSubscription($subscription);

        try {
            $this->getSubscriptionService()->cancel($cancellationRequest);

            return View::createRouteRedirect('api_customer_stores_subscriptions_show',
                ['customer' => $customer->getId(), 'store' => $store->getId(), 'subscription' => $subscription->getId()], 204);

        } catch (ValidationException $ve) {

            return $this->createView($ve->getValidationErrors(), 400);
        }
    }

    /**
     * Lists the available products for the given store
     * @ApiDoc(
     *  section="Customer",
     *  statusCodes={
     *          201="Returned when successful",
     *          403="Returned when the user is not authorized to say hello",
     *          400="Returned in case of invalid input"
     *  },
     *  input="AppBundle\Lib\Product\Form\SearchFormType",
     *  output={"class"="Appbundle\Entity\Product", "collection"=true, "groups"={"product", "meta"}}
     * )
     * @Rest\Get("{customer}/stores/{store}/products",
     *     name="api_customer_stores_products_show",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     *
     * @param Customer $customer
     * @param Store $store
     * @param Request $request
     * @return View
     */
    public function listAvailableProductsAction(Customer $customer, Store $store, Request $request) :View
    {
        $this->matchStoreToCustomer($customer, $store);

        $search = new ProductSearchDto();
        $form = $this->createForm(ProductSearchForm::class, $search, ['method' => 'GET']);
        $form->get('filter')->remove('store');
        $form->get('filter')->remove('country');
        $form->handleRequest($request);

        /**
         * @var ProductSearchDto $data
         */
        $data = $form->getData();
        $data->getFilter()->setStore(null);
        $data->getFilter()->setCountry([$store->getCountryCode()]);

        $res = $this->getProductService()->search($data)->getQuery()->getResult();

        return $this->createView($res, 200, [], ['product', 'meta']);
    }


    /**
     * @return StoreService
     */
    private function getService() :StoreService
    {
        return $this->get('app.store.service');
    }

    /**
     * @return SubscriptionService
     */
    private function getSubscriptionService() :SubscriptionService
    {
        return $this->get('app.subscription.service');
    }

    /**
     * @return ProductService
     */
    private function getProductService() :ProductService
    {
        return $this->get('app.product.service');
    }

    private function matchStoreToCustomer(Customer $customer, Store $store) :void
    {
        if ($customer->getId() !== $store->getCustomerId()) {
            throw $this->createNotFoundException('Store not found');
        }
    }

    private function matchSubscriptionToStore(Subscription $subscription, Store $store) :void
    {
        if ($subscription->getStore()->getId() !== $store->getId()) {
            throw $this->createNotFoundException('Subscription not found');
        }
    }

}