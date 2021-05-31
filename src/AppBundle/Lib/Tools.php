<?php


namespace AppBundle\Lib;


use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class Tools
{
    public static function getLastDayDateTimeForMonth(\DateTime $date) :\DateTime
    {
        $d = self::getFirstDayDateTimeForMonth($date);
        $d->add(new \DateInterval('P1M'));
        $d->sub(new \DateInterval('PT1S'));

        return $d;
    }

    public static function getFirstDayDateTimeForMonth(\DateTime $date) :\DateTime
    {
        return new \DateTime($date->format('Y-m-01 00:00:00'));
    }

    public static function getFirstDayDateForNextMonth(\DateTime $date) :\DateTime
    {
        $date->add(new \DateInterval('P1M'));

        return self::getFirstDayDateTimeForMonth($date);
    }

    public static function getLastDayDateTimeForPreviousMonth(\DateTime $date) :\DateTime
    {
        $date->sub(new \DateInterval('P1M'));

        return self::getLastDayDateTimeForMonth($date);
    }

    /**
     * @param \DateTime $date
     * @param null $format
     * @return array
     */
    public static function getFirstAndLastDayOfMonth(\DateTime $date, $format = null) :array
    {
        $first = self::getFirstDayDateTimeForMonth($date);
        $last  = self::getLastDayDateTimeForMonth($date);

        if ($format !== null) {
            return [
                'start_date' => $first->format($format),
                'end_date' => $last->format($format)
            ];
        }

        return [
            'start_date' => $first,
            'end_date' => $last
        ];
    }

    public static function getErrorsByPath(ConstraintViolationList $errors, string $path) :array
    {
        $data = iterator_to_array($errors);

        $f = function (ConstraintViolation $error) use ($path) {
            return $error->getPropertyPath() === $path;
        };

        return array_values(array_filter($data, $f));
    }
}