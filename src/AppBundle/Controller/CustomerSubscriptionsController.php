<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Customer;
use AppBundle\Entity\Subscription;
use AppBundle\Lib\Subscription\Form\SearchDto;
use AppBundle\Lib\Subscription\Form\SearchFormType;
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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CustomerSubscriptionsController extends Controller
{
    use ViewHelperTrait;

    /**
     * Lists the subscription for the given customer
     * @ApiDoc(
     *  section="Customer",
     *  statusCodes={
     *          201="Returned when successful",
     *          403="Returned when the user is not authorized to say hello",
     *          400="Returned in case of invalid input"
     *  },
     *  input="AppBundle\Lib\Subscription\Form\SearchFormType",
     *  output={"class"="Appbundle\Entity\Subscription", "collection"=true, "groups"={"subscription", "meta", "store_info"}}
     * )
     *
     * @Rest\Get("/{customer}/subscriptions",
     *     name="api_customer_subscriptions_index",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     *
     * @param Request $request
     * @param Customer $customer
     * @return View
     */
    public function indexAction(Request $request, Customer $customer) :View
    {
        $search = new SearchDto();
        $form = $this->createForm(SearchFormType::class, $search, ['method' => 'GET']);
        $form->handleRequest($request);
        /**
         * @var SearchDto $data
         */
        $data = $form->getData();
        $data->getFilter()->setCustomer([$customer->getId()]);

        $res = $this->getService()->search($data)->getQuery()->getResult();

        return $this->createView($res, 200,[] ,['subscription', 'meta', 'store_info']);
    }

    /**
     * Creates a new subscription
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
     * @Rest\Post("/{customer}/subscriptions",
     *     name="api_customer_subscriptions_create",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     *
     * @param Request $request
     * @param Customer $customer
     * @return View
     */
    public function createAction(Request $request, Customer $customer) :View
    {
        $subscriptionRequest = new SubscriptionRequestDto();
        $form = $this->createForm(SubscriptionRequestFormType::class, $subscriptionRequest, ['method' => 'POST']);
        $form->handleRequest($request);

        /**
         * @var SubscriptionRequestDto $subscriptionRequest
         */
        $subscriptionRequest = $form->getData();
        if ($subscriptionRequest->getStore() !== null && $subscriptionRequest->getStore()->getCustomerId() !== $customer->getId()) {
            throw new AccessDeniedException('Subscription does not match customer');
        }

        try {
            $subscription = $this->getService()->create($subscriptionRequest);
            return View::createRouteRedirect('api_customer_subscriptions_show', ['customer' => $customer->getId(), 'subscription' => $subscription->getId()], 201);

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
     *  output={"class"="AppBundle\Entity\Customer", "groups"={"subscription", "store_info", "include_shipments", "shipment", "meta"}}
     * )
     *
     * @Rest\Get("/{customer}/subscriptions/{subscription}",
     *     name="api_customer_subscriptions_show",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     *
     * @param Request $request
     * @param Customer $customer
     * @param Subscription $subscription
     * @return View
     */
    public function showAction(Request $request, Customer $customer, Subscription $subscription) :View
    {
        return $this->createView($subscription, 200,[] ,['subscription', 'meta', 'store_info', 'include_shipments', 'shipment']);
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
     *  output={"class"="AppBundle\Entity\Subscription", "groups"={"subscription", "store_info", "meta", "include_shipments", "shipment"}}
     * )
     *
     * @Rest\Post("/{customer}/subscriptions/{subscription}/cancel",
     *     name="api_customer_subscriptions_cancel",
     *     defaults={"_format"="json"},
     *     requirements={"_format"="json|xml"},
     *     options={"method_prefix"=false})
     *
     * @param Request $request
     * @param Customer $customer
     * @param Subscription $subscription
     * @return View
     */
    public function cancelAction(Request $request, Customer $customer, Subscription $subscription) :View
    {
        $this->matchSubscriptionToCustomer($subscription, $customer);
        $cancellationRequest = new SubscriptionCancellationRequestDto();
        $form = $this->createForm(SubscriptionCancellationRequestFormType::class, $cancellationRequest, ['method' => 'POST']);
        $form->handleRequest($request);
        /**
         * @var SubscriptionCancellationRequestDto $data
         */
        $data = $form->getData();
        $data->setSubscription($subscription);

        try {
            $this->getService()->cancel($cancellationRequest);

            return View::createRouteRedirect('api_customer_subscriptions_show',
                ['customer' => $customer->getId(), 'subscription' => $subscription->getId()], 204);

        } catch (ValidationException $ve) {
            return $this->createView($ve->getValidationErrors(), 400);
        }
    }

    private function getService() :SubscriptionService
    {
        return $this->get('app.subscription.service');
    }

    private function matchSubscriptionToCustomer(Subscription $subscription, Customer $customer) :void
    {
        if ($subscription->getStore()->getCustomer()->getId() !== $customer->getId()) {
            throw $this->createNotFoundException('Customer store not found');
        }
    }
}