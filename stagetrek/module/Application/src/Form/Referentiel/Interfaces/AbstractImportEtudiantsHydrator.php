<?php

namespace Application\Form\Referentiel\Interfaces;


use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Misc\Traits\InputFilterProviderTrait;
use ArrayObject;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Hidden;
use Laminas\InputFilter\InputFilterProviderInterface;

abstract class AbstractImportEtudiantsHydrator implements ImportEtudiantsHydratorInterface
{
    /**
     * @param object $object
     * @return array
     */
    public function extract(object $object) : array
    {
        if(!$object instanceof ArrayObject) {
            throw new \Exception("L'hydrator requier un objet de type ArrayObject");
        }
        $data = $object->getArrayCopy();
        return [
            AbstractImportEtudiantsForm::INPUT_KEY => ($data[ AbstractImportEtudiantsForm::INPUT_KEY]) ?? null,
            AbstractImportEtudiantsForm::CSRF => ($data[ AbstractImportEtudiantsForm::CSRF]) ?? null,
        ];
    }



    /**
     * Hydrate $object with the provided $data.
     *
     * @param array $data
     * @param mixed $object
     * @return array
     */
    public function hydrate(array $data, mixed $object): ArrayObject
    {
        //controle pas vraiment utile, on retourne un tableau et non l'object
        if(!$object instanceof ArrayObject) {
            throw new \Exception("L'hydrator requiert un objet de type ArrayObject");
        }
        return $object;
    }
}