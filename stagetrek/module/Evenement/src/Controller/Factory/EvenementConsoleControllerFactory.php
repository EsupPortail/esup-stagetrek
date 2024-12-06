<?php

namespace Evenement\Controller\Factory;

use Evenement\Controller\EvenementConsoleController;
use Interop\Container\ContainerInterface;
use UnicaenEvenement\Service\Etat\EtatService;
use Evenement\Service\Evenement\EvenementService;
use UnicaenEvenement\Service\EvenementGestion\EvenementGestionService;
use UnicaenEvenement\Service\Journal\JournalService;

class EvenementConsoleControllerFactory
{

    /**
     * @param ContainerInterface $container
     * @return EvenementConsoleController
     */
    public function __invoke(ContainerInterface $container): EvenementConsoleController
    {
        /**
         * @var EvenementService $evenementService
         * @var EvenementGestionService $evenementGestionService
         * @var EtatService $etatService
         * @var JournalService $journalService
         */
        $evenementService = $container->get(EvenementService::class);
        $evenementGestionService = $container->get(EvenementGestionService::class);
        $etatService = $container->get(EtatService::class);
        $journalService = $container->get(JournalService::class);
        /** @var EvenementConsoleController $controller */
        $controller = new EvenementConsoleController();
        $controller->setEvenementService($evenementService);
        $controller->setEvenementGestionService($evenementGestionService);
        $controller->setEtatEvenementService($etatService);
        $controller->setJournalService($journalService);
        $config = $container->get('Configuration');
        if (isset($config['unicaen-evenement']['max_time_execution'])) {
            $max_time_execution = $config['unicaen-evenement']['max_time_execution'];
            $controller->setMaxExecutionTime(intval($max_time_execution));
        }
//        if (isset($config['console-cli'])) {
//            if (isset($config['console-cli']['console_env'])) {
//                $env = ($config['console-cli']['console_env']) ?? EnvironnementProvider::PRODUCTION;
//                putenv('APPLICATION_ENV=' . $env);
//            }
//            $controller->setConsoleConfig($config['console-cli']);
//        }
        // On injecte un HttpRouter pour pouvoir utiliser le plugin de contrôleur "Url"
        $event = $controller->getEvent();
        $httpRouter = $container->get('HttpRouter');
        $event->setRouter($httpRouter);
        $controller->setRouter($httpRouter);
//        $service = $container->get('ServiceManager')->get(MailService::class);
//        $controller->setMailService($service);
        return $controller;
    }
}
