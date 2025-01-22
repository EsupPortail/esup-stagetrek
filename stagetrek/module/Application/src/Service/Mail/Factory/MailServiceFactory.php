<?php

namespace Application\Service\Mail\Factory;


use Application\Service\Mail\MailService;
use Application\Service\Parametre\ParametreService;
use Application\Service\Renderer\MacroService;
use Doctrine\ORM\EntityManager;
use Exception;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceManager;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use UnicaenRenderer\Service\Rendu\RenduService;
use UnicaenRenderer\Service\Template\TemplateService;

class MailServiceFactory
{
    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container)
    {

        $config = $container->get('Configuration');
        if(!isset($config['unicaen-mail'])){
            throw new Exception("Configuration parametre 'unicaen-mail' non définie");
        }
        $config = $config['unicaen-mail'];
        if(!isset($config['transport_options'])){
            throw new Exception("Configuration parametre 'unicaen-mail/transport_options' non définie");
        }
        if(!isset($config['transport_options']['host']) || $config['transport_options']['host']==""){
            throw new Exception("Configuration parametre 'unicaen-mail/transport_options/host' non définie");
        }
        if(!isset($config['transport_options']['port']) || $config['transport_options']['port']==""){
            throw new Exception("Configuration parametre 'unicaen-mail/transport_options/port' non définie");
        }
        if(!isset($config['mail_entity_class']) || $config['mail_entity_class']==""){
            throw new Exception("Configuration parametre 'unicaen-mail/mail_entity_class' non définie");
        }

        $transport = new EsmtpTransport(host: $config['transport_options']['host'], port: $config['transport_options']['port']);
        $mailer = new Mailer($transport);

        /**
         * @var EntityManager $entityManager
         */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        $service = new MailService();
        $service->setMailer($mailer);
        $service->setConfig($config);
        $service->setEntityClass($config['mail_entity_class']);
        $service->setObjectManager($entityManager);


        //A voir si l'on peux supprimer d'ici template et macroService pour ne dépendre que de RenduService
        /** @var RenduService $renduService */
        $renduService = $container->get(ServiceManager::class)->get(RenduService::class);
        $service->setRenduService($renduService);


        //Rajout personnelle permettant de vérifier que les mails ne contiennent plus de macro ....
        /** @var TemplateService $templateService */
        $templateService = $container->get(ServiceManager::class)->get(TemplateService::class);
        $service->setTemplateService($templateService);

        /** @var MacroService $macroService */
        $macroService = $container->get(ServiceManager::class)->get(MacroService::class);
        $service->setMacroService($macroService);

        /** @var ParametreService $parametreService */
        $parametreService = $container->get(ServiceManager::class)->get(ParametreService::class);
        $service->setParametreService($parametreService);

        return $service;
    }
}