<?php


namespace AppBundle\Tests\Form;


use AppBundle\Tests\ContainerAwareTestCase;
use Symfony\Component\Validator\ConstraintValidator;

class SearchSortDtoTest extends ContainerAwareTestCase
{
    public function testSearchSortDtoHandling() :void
    {
        $validator = $this->getContainer()->get('validator');

        $dto = new SearchSortDtoDummy(['f1', 'f2', 'f3']);
        $dto->setDirection('ASC');
        $dto->setSortBy('f1');

        static::assertEquals(['f1' => 'ASC'], $dto->toArray());
        $errors = $validator->validate($dto);
        static::assertCount(0, $errors);

        $dto->setSortBy('f4');
        static::assertEquals([], $dto->toArray());
        $errors = $validator->validate($dto);
        static::assertCount(1, $errors);

        $dto->setSortBy('f1');
        $dto->setDirection('bogus');
        static::assertEquals(['f1' => 'ASC'], $dto->toArray());
        $errors = $validator->validate($dto);
        static::assertCount(1, $errors);


    }
}