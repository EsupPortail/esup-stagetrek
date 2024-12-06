<?php

namespace Application\View\Helper\Interfaces;

use Laminas\Form\Form;

interface EtudiantActionViewHelperInterface
{
   public function vueEtudianteActive() : bool;
   public function setVueEtudiante(bool $active) : static;
}