<?php

namespace Fichier\Form\Upload;

use Fichier\Service\Nature\NatureService;
use Interop\Container\ContainerInterface;
use Laminas\Form\FormElementManager;

class UploadFormFactory {

    public function __invoke(ContainerInterface $container)
    {
        /** @var UploadHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(UploadHydrator::class);

        /**
         * @var NatureService $natureService
         */
        $natureService = $container->get(NatureService::class);

        $form = new UploadForm();
        $form->setNatureService($natureService);
        $form->setHydrator($hydrator);

        $config = $container->get('Config');
        if(isset($config['fichier']['uplpoad']['max-size'])){
            $form->setMaxSize($config['fichier']['uplpoad']['max-size']);
        }
        if(isset($config['fichier']['uplpoad']['extentions'])){
            $form->setAllowedExtensions($config['fichier']['uplpoad']['extentions']);
        }
        if(isset($config['fichier']['uplpoad']['type-mine'])){
            $form->setAllowedTypeMime($config['fichier']['uplpoad']['type-mine']);
        }
//        Permet de fournir en config des validateur personalisé
        if(isset($config['fichier']['uplpoad']['validators'])){
            foreach($config['fichier']['uplpoad']['validators'] as $validator){
                $form->add($validator);
            }
        }

        return $form;
    }
}