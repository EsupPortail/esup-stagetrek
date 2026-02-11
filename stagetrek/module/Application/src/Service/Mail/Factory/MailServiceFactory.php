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
use UnicaenMail\Exception\NotFoundConfigException;
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
        $config = $container->get('Configuration')['unicaen-mail'];
        $host  = ($config['transport_options']['host']) ?? null;
        $port  = ($config['transport_options']['port']) ?? null;

        //TODO : a voir si l'on garde tls a true par défaut et si l'on peut merger avec use_auth
        //A priori non car on peut utiliser le protocole starttls qui requiére que tls soit a false mais que l'on envoie quand même les paramétres d'authentifiation
        //Gestion de cas ou l'auto_tls s'active pour une raison a déterminer, ce qui skip le paramétre tls=true. TODO : a revoir
        $tls = ($config['transport_options']['tls']) ?? null;
        if(!isset($tls)){
            throw new NotFoundConfigException("La clé d'accès aux paramètres de configuration ['unicaen-mail']['transport_options']['tls'] n'est pas définie.");
        }
        $transport = new EsmtpTransport(host: $host, port: $port, tls: $tls);
        $auto_tls = ($config['transport_options']['auto_tls']) ?? null;
        if(isset($auto_tls)){
            $transport->setAutoTLS($auto_tls);
        }
        $useAuth =  ($config['transport_options']['connection_config']['use_auth']) ?? false;

        if($useAuth){
            $username = ($config['transport_options']['connection_config']['username']) ?? null;
            $password = ($config['transport_options']['connection_config']['password']) ?? null;
            if(!isset($username) || !isset($password)){
                throw new NotFoundConfigException("Les paramétres d'authentifications ne sont pas définies.");
            }
            $transport->setUsername($username);
            $transport->setPassword($password);
        }

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