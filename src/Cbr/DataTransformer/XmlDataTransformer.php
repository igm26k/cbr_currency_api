<?php

namespace App\Cbr\DataTransformer;

use SimpleXMLElement;
use Symfony\Component\Form\DataTransformerInterface;

class XmlDataTransformer implements DataTransformerInterface
{
    /**
     * Transforms XML string to SimpleXMLElement
     *
     * @param $xml
     * @return array|false|SimpleXMLElement
     */
    public function transform($xml): SimpleXMLElement|bool|array
    {
        if (!is_string($xml) || empty($xml)) {
            return [];
        }

        return simplexml_load_string($xml);
    }

    /**
     * Transforms SimpleXMLElement to XML string
     *
     * @param SimpleXMLElement $obj
     * @return string
     * @throws \Exception
     */
    public function reverseTransform($obj): string
    {
        if (!is_a($obj, SimpleXMLElement::class)) {
            return '';
        }

        return $obj->asXML();
    }
}