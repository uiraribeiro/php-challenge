<?php


namespace AppBundle\Lib\Stock\Validator;


use AppBundle\Entity\Country;
use AppBundle\Entity\Item;
use AppBundle\Lib\Stock\Form\StockUpRequestDto;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class StockUpValidValidator extends ConstraintValidator
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $manager)
    {
        $this->entityManager = $manager;
    }

    public function validate($value, Constraint $constraint) :void
    {
        if (!$value instanceof StockUpRequestDto) {
            return;
        }

        if (!$value->getItem() instanceof Item) {
            $this->context->buildViolation('You must provide an item')
                ->atPath('item')
                ->addViolation();
        }

        $this->validateCountries($value->getCountries());

        $quantity = $value->getQuantity();
        if ($quantity === null || $quantity < 1) {
            $this->context->buildViolation('You must provide a valid quantity')
                ->atPath('quantity')
                ->addViolation();
        }
        $mode = $value->getMode();
        if ($mode === null || !in_array($mode, [StockUpRequestDto::MODE_REPLACE, StockUpRequestDto::MODE_UPDATE], true)) {
            $this->context->buildViolation('You must provide a valid mode')
                ->atPath('from')
                ->addViolation();
        }

        if (!$value->getFrom() instanceof \DateTime) {
            $this->context->buildViolation('You must provide a from date')
                ->atPath('from')
                ->addViolation();
        }

        if (!$value->getUntil() instanceof \DateTime) {
            $this->context->buildViolation('You must provide an until date')
                ->atPath('until')
                ->addViolation();
        }

        if (null === $value->getFrom() || null === $value->getUntil()) {
            return;
        }

        if ($value->getFrom()->getTimestamp() >= $value->getUntil()->getTimestamp()) {
            $this->context->buildViolation('until must be greater than from')
                ->atPath('until')
                ->addViolation();
        }

        return;
    }

    protected function validateCountries(array $countries) :void
    {
        if (0 === count($countries)) {
            $this->context
                ->buildViolation('You must provide at least one country')
                ->atPath('countries')->addViolation();
            return;
        }

        $repo = $this->getEntityManager()->getRepository(Country::class);

        foreach ($countries AS $country) {
            $c = $repo->find($country);
            if (!$country) {
                $this->context->buildViolation('Country '.$country.' is not valid')
                    ->atPath('country')
                    ->addViolation();
            }
        }

        return;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManager $entityManager
     * @return StockUpValidValidator
     */
    public function setEntityManager(EntityManager $entityManager): StockUpValidValidator
    {
        $this->entityManager = $entityManager;
        return $this;
    }


}