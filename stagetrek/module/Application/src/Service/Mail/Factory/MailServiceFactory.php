<?php

namespace Application\Service\Mail\Factory;


use Application\Service\Mail\MailService;
use Application\Service\Parametre\ParametreService;
use Application\Service\Renderer\MacroService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Mail\Transport\Smtp;
use Laminas\Mail\Transport\SmtpOptions;
use Laminas\Mail\Transport\TransportInterface;
use Laminas\ServiceManager\ServiceManager;
use UnicaenRenderer\Service\Template\TemplateService;
use UnicaenUtilisateur\Service\User\UserService;

class MailServiceFactory
{
    public function __invoke(ContainerInterface $container)
    {

        $config = $container->get('Configuration')['unicaen-mail'];
        /** @var TransportInterface */
        $transport = new Smtp(new SmtpOptions($config['transport_options']));

        /**
         * @var EntityManager $entityManager
         * @var UserService $userService
         */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        /** @var MailService $service */
        $serviceProvider = new MailService($transport);
        $serviceProvider->setObjectManager($entityManager);
        $serviceProvider->setEntityClass($config['mail_entity_class']);
        $serviceProvider->setConfig($config);


        /** @var TemplateService $templateService */
        $templateService = $container->get(ServiceManager::class)->get(TemplateService::class);
        $serviceProvider->setTemplateService($templateService);

        /** @var MacroService $macroService */
        $macroService = $container->get(ServiceManager::class)->get(MacroService::class);
        $serviceProvider->setMacroService($macroService);


        /** @var ParametreService $parametreService */
        $parametreService = $container->get(ServiceManager::class)->get(ParametreService::class);
        $serviceProvider->setParametreService($parametreService);

        return $serviceProvider;
    }
}