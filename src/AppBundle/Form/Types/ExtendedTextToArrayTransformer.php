<?php

namespace AppBundle\Form\Types;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class TextToArrayTransformer
 * @package SysEleven\Library\BaseBundle\Form
 */
class ExtendedTextToArrayTransformer implements DataTransformerInterface
{

    /**
     * Seperator to split string
     * @var string
     */
    public $separator = ',';

    /**
     * Trim values after splitting
     * @var bool
     */
    public $trim = true;

    /**
     * @var bool
     */
    public $removeEmptyStrings = true;

    public $asInt = false;


    public function __construct(string $separator = ',', bool $trim = true, bool $removeEmptyStrings = true, bool $asInt = false)
    {
        $this->separator = $separator;
        $this->trim = $trim;
        $this->removeEmptyStrings = $removeEmptyStrings;
        $this->asInt = $asInt;
    }


    /**
     * Transforms a value from the original representation to a transformed representation.
     *
     * This method is called on two occasions inside a form field:
     *
     * 1. When the form field is initialized with the data attached from the datasource (object or array).
     * 2. When data from a request is submitted using {@link Form::submit()} to transform the new input data
     *    back into the renderable format. For example if you have a date field and submit '2009-10-10'
     *    you might accept this value because its easily parsed, but the transformer still writes back
     *    "2009/10/10" onto the form field (for further displaying or other purposes).
     *
     * This method must be able to deal with empty values. Usually this will
     * be NULL, but depending on your implementation other empty values are
     * possible as well (such as empty strings). The reasoning behind this is
     * that value transformers must be chainable. If the transform() method
     * of the first value transformer outputs NULL, the second value transformer
     * must be able to process that value.
     *
     * By convention, transform() should return an empty string if NULL is
     * passed.
     *
     * @param mixed $value The value in the original representation
     *
     * @return mixed The value in the transformed representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function transform($value)
    {
        return (is_array($value))? implode($this->separator, $value):$value;
    }

    /**
     * Transforms a value from the transformed representation to its original
     * representation.
     *
     * This method is called when {@link Form::submit()} is called to transform the requests tainted data
     * into an acceptable format for your data processing/model layer.
     *
     * This method must be able to deal with empty values. Usually this will
     * be an empty string, but depending on your implementation other empty
     * values are possible as well (such as NULL). The reasoning behind
     * this is that value transformers must be chainable. If the
     * reverseTransform() method of the first value transformer outputs an
     * empty string, the second value transformer must be able to process that
     * value.
     *
     * By convention, reverseTransform() should return NULL if an empty string
     * is passed.
     *
     * @param mixed $value The value in the transformed representation
     *
     * @return mixed The value in the original representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function reverseTransform($value)
    {
        if (is_string($value)) {
            $value = explode($this->separator, $value);
        }

        if (!is_array($value)) {
            return $value;
        }

        if ($this->trim === true) {
            $value = array_map('trim', $value);
        }

        if ($this->removeEmptyStrings === true) {
            $f = static function ($v) {
                return $v !== '';
            };

            $value = array_values(array_filter($value, $f));
        }

        if ($this->asInt) {
            $value = array_map('intval', $value);
        }

        return 0 === count($value)? null:$value;
    }
}