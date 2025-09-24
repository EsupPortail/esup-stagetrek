<?php

namespace Application\Form\Referentiel\Interfaces;

interface ImportEtudiantsFormInterface
{
    public static function getKey() : string;
    public function getFormLabel() : string;
    public function isActif() : bool;

}