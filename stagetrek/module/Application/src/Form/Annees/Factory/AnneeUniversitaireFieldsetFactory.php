<?php


namespace Application\Form\Annees\Factory;

use Application\Entity\Db\AnneeUniversitaire;
use Application\Form\Annees\Fieldset\AnneeUniversitaireFieldset;
use Application\Form\Misc\Validator\CodeValidator;
use Application\Form\Misc\Validator\LibelleValidator;
use Application\Service\AnneeUniversitaire\AnneeUniversitaireService;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;

class AnneeUniversitaireFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AnneeUniversitaireFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : AnneeUniversitaireFieldset
    {
        $fieldset = new AnneeUniversitaireFieldset('anneeUniversitaire');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /** @var DoctrineObject $hydrator */
        $hydrator = $container->get('HydratorManager')->get(DoctrineObject::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new AnneeUniversitaire());

        /**
         * @var AnneeUniversitaireService $anneeUniversitaireService
         */
        $anneeUniversitaireService = $container->get(ServiceManager::class)->get(AnneeUniversitaireService::class);

        /** @var CodeValidator $codeValidator */
        $codeValidator = $container->get(ValidatorPluginManager::class)->get(CodeValidator::class);
        $codeValidator->setEntityService($anneeUniversitaireService);
        $fieldset->setCodeValidator($codeValidator);
        /** @var LibelleValidator $libelleValidator */
        $libelleValidator = $container->get(ValidatorPluginManager::class)->get(LibelleValidator::class);
        $libelleValidator->setEntityService($anneeUniversitaireService);
        $fieldset->setLibelleValidator($libelleValidator);

        return $fieldset;
    }
}