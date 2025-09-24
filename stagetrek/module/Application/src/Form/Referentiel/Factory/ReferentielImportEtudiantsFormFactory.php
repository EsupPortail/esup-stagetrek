<?php

namespace Application\Form\Referentiel\Factory;

use Application\Form\Referentiel\Hydrator\ReferentielImportEtudiantsHydrator;
use Application\Form\Referentiel\Interfaces\AbstractImportEtudiantsForm;
use Application\Form\Referentiel\ReferentielImportEtudiantsForm;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\Exception;

class ReferentielImportEtudiantsFormFactory extends ImportEtudiantsFormFactory
{

    /**
     * @param AbstractImportEtudiantsForm $form
     * @param ContainerInterface $container
     * @return AbstractImportEtudiantsForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function initForm(AbstractImportEtudiantsForm $form, ContainerInterface $container): AbstractImportEtudiantsForm
    {
        $form = parent::initForm($form, $container);
        if(!$form instanceof ReferentielImportEtudiantsForm){
            throw new Exception("Le formulaire n'est pas de type ".ReferentielImportEtudiantsForm::class);
        }

        /** @var ReferentielImportEtudiantsHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(ReferentielImportEtudiantsHydrator::class);
        $form->setHydrator($hydrator);

        return $form;
    }

}