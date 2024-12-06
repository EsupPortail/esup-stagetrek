<?php
namespace Application\Form\Misc\Validator;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * @package Application\Form\Element\Validator
 */
class AcronymeValidatorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AcronymeValidator
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : AcronymeValidator
    {
        return new AcronymeValidator();
    }
}