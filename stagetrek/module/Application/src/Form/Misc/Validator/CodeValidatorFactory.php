<?php
namespace Application\Form\Misc\Validator;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class CodeValidatorFactory
 * @package Application\Form\Factory\Validator
 */
class CodeValidatorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return CodeValidator|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : CodeValidator
    {
        return new CodeValidator();
    }
}