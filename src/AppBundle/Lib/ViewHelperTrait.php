<?php


namespace AppBundle\Lib;


use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;

trait ViewHelperTrait
{
    public function createView($data, int $code = 200, array $headers = [], array $groups = []) :View
    {
        if ($data instanceof ValidationException) {
            $data = $data->getValidationErrors();
            $code = 400;
        }

        if (0 === count($groups)) {
            return View::create($data, $code, $headers);
        }

        $context = SerializationContext::create()->setGroups($groups);
        $context->enableMaxDepthChecks();

        return View::create($data, $code, $headers)->setSerializationContext($context);
    }
}