<?php

namespace App\Cbr\DataTransformer;

use DateTime;
use Symfony\Component\Form\DataTransformerInterface;

class DateTransformer implements DataTransformerInterface
{
    /**
     * Transforms date to d/m/Y format
     *
     * @param $date
     * @return string
     * @throws \Exception
     */
    public function transform($date): string
    {
        if (!is_string($date) || empty($date)) {
            return '';
        }

        $date = new DateTime($date);

        return $date->format('d/m/Y');
    }

    /**
     * Transforms date to Y-m-d format
     *
     * @param $date
     * @return string
     * @throws \Exception
     */
    public function reverseTransform($date): string
    {
        if (!is_string($date) || empty($date)) {
            return '';
        }

        $date = new DateTime($date);

        return $date->format('Y-m-d');
    }
}