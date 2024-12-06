<?php

namespace  Fichier\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends  AbstractActionController {

    const ROUTE_INDEX = 'fichier';
    const ACTION_INDEX = 'index';

    public function indexAction() : ViewModel
    {
        return new ViewModel([]);
    }
}