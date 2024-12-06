<?php


namespace Application\Form\Notification\Factory;

use Application\Entity\Db\FaqCategorieQuestion;
use Application\Form\Misc\Validator\LibelleValidator;
use Application\Form\Notification\Fieldset\FaqCategorieQuestionFieldset;
use Application\Service\Notification\FaqCategorieQuestionService;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\ValidatorPluginManager;

/**
 * Class FaqCategorieQuestionFieldsetFactory
 * @package Application\Form\Factory\Fieldset
 */
class FaqCategorieQuestionFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return FaqCategorieQuestionFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FaqCategorieQuestionFieldset
    {
        $fieldset = new FaqCategorieQuestionFieldset('faqCategorieQuestion');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);

        /** @var DoctrineObject $hydrator */
        $hydrator = $container->get('HydratorManager')->get(DoctrineObject::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new FaqCategorieQuestion());

        /** @var LibelleValidator $libelleValidator */
        $libelleValidator = $container->get(ValidatorPluginManager::class)->get(LibelleValidator::class);
        /** @var FaqCategorieQuestionService $faqCategorieQuestionService */
        $faqCategorieQuestionService = $container->get(ServiceManager::class)->get(FaqCategorieQuestionService::class);
        $libelleValidator->setEntityService($faqCategorieQuestionService);
        $fieldset->setLibelleValidator($libelleValidator);

        return $fieldset;
    }
}