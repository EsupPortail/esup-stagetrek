<?php

namespace Application\Form\Referentiel\Factory;

use Application\Form\Referentiel\CSVImportEtudiantsForm;
use Application\Form\Referentiel\Hydrator\CSVImportEtudiantsHydrator;
use Application\Form\Referentiel\Interfaces\AbstractImportEtudiantsForm;
use Application\Validator\Import\EtudiantCsvImportValidator;
use Interop\Container\ContainerInterface;
use Laminas\Validator\ValidatorPluginManager;
use PHPUnit\Framework\Exception;

class CSVImportEtudiantsFormFactory extends ImportEtudiantsFormFactory
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
        if(!$form instanceof CSVImportEtudiantsForm){
            throw new Exception("Le formulaire n'est pas de type ".CSVImportEtudiantsForm::class);
        }

        $etudiantCsvImportValidator =  $container->get(ValidatorPluginManager::class)->get(EtudiantCsvImportValidator::class);
        $form->setImportValidator($etudiantCsvImportValidator);

        /** @var CSVImportEtudiantsHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(CSVImportEtudiantsHydrator::class);
        $form->setHydrator($hydrator);

        return $form;
    }

}