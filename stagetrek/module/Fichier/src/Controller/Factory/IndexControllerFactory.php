<?php

namespace  Fichier\Controller\Factory;;

use Fichier\Controller\IndexController;
use Interop\Container\ContainerInterface;

class IndexControllerFactory {

    public function __invoke(ContainerInterface $container) : IndexController
    {
        return new IndexController();
    }
}