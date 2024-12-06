<?php
namespace Application\Form\Misc\Validator;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * @package Application\Form\Element\Validator
 */
class LibelleValidatorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return LibelleValidator|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : LibelleValidator
    {
        return new LibelleValidator();
    }
}