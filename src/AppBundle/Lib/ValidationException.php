<?php


namespace AppBundle\Lib;


use Symfony\Component\Validator\ConstraintViolationList;
use Throwable;

class ValidationException extends \Exception
{
    /**
     * @var ConstraintViolationList
     */
    protected $errors;

    public function __construct($message = "", $errors = null, $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if ($errors instanceof ConstraintViolationList) {
            $this->errors = $errors;
        }
    }

    public function getValidationErrors():?ConstraintViolationList
    {
        return $this->errors;
    }
}