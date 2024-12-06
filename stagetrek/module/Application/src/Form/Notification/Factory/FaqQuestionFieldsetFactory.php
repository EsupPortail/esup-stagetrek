<?php


namespace Application\Form\Notification\Factory;

use Application\Entity\Db\Faq;
use Application\Form\Notification\Fieldset\FaqQuestionFieldset;
use Application\Form\Notification\Hydrator\FaqQuestionHydrator;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class FaqQuestionFieldsetFactory
 * @package Application\Form\Factory\Fieldset
 */
class FaqQuestionFieldsetFactory implements FactoryInterface
{
    /**
     * Create fieldset
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return FaqQuestionFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FaqQuestionFieldset
    {
        $fieldset = new FaqQuestionFieldset('faqQuestion');

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $fieldset->setObjectManager($entityManager);


        /** @var FaqQuestionHydrator $hydrator */
        $hydrator = $container->get('HydratorManager')->get(FaqQuestionHydrator::class);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new Faq());
        return $fieldset;
    }
}