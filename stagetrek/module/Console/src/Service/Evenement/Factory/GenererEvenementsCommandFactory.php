<?php

namespace Console\Service\Evenement\Factory;

use Console\Service\Evenement\GenererEvenementCommand;
use Console\Service\Evenement\TraiterEvenementCommand;
use Evenement\Service\Evenement\EvenementService;
use Evenement\Service\MailAuto\MailAutoStageDebutChoixEvenementService;
use Evenement\Service\MailAuto\MailAutoStageDebutValidation;
use Evenement\Service\MailAuto\MailAutoStageRappelChoixEvenementService;
use Evenement\Service\MailAuto\MailAutoStageRappelValidationEvenementService;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use UnicaenEvenement\Service\Etat\EtatService;
use UnicaenEvenement\Service\EvenementGestion\EvenementGestionService;
use UnicaenEvenement\Service\Journal\JournalService;

class GenererEvenementsCommandFactory extends Command
{
    /**
     * @param ContainerInterface $container
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): GenererEvenementCommand
    {
        $command = new GenererEvenementCommand();
        /**
         * @var EvenementService $evenementService
         * @var EvenementGestionService $evenementGestionService
         * @var EtatService $etatService
         * @var JournalService $journalService
         */
        $evenementService = $container->get(ServiceManager::class)->get(EvenementService::class);
        $evenementGestionService = $container->get(ServiceManager::class)->get(EvenementGestionService::class);
        $etatService = $container->get(ServiceManager::class)->get(EtatService::class);
        $journalService = $container->get(ServiceManager::class)->get(JournalService::class);
        $command->setEvenementService($evenementService);
        $command->setEvenementGestionService($evenementGestionService);
        $command->setEtatEvenementService($etatService);
        $command->setJournalService($journalService);

        /** @var MailAutoStageDebutValidation $mailAutoService */
        $mailAutoService = $container->get(ServiceManager::class)->get(MailAutoStageDebutValidation::class);
        $command->setMailAutoStageDebutValidationService($mailAutoService);
        /** @var MailAutoStageRappelValidationEvenementService $mailAutoService */
        $mailAutoService = $container->get(ServiceManager::class)->get(MailAutoStageRappelValidationEvenementService::class);
        $command->setMailAutoStageRappelValidationService($mailAutoService);
        /** @var MailAutoStageDebutChoixEvenementService $mailAutoService */
        $mailAutoService = $container->get(ServiceManager::class)->get(MailAutoStageDebutChoixEvenementService::class);
        $command->setMailAutoStageDebutChoixEvenementService($mailAutoService);
        /** @var MailAutoStageRappelChoixEvenementService $mailAutoService */
        $mailAutoService = $container->get(ServiceManager::class)->get(MailAutoStageRappelChoixEvenementService::class);
        $command->setMailAutoStageRappelChoixEvenementService($mailAutoService);



////        // On injecte un HttpRouter pour pouvoir utiliser le plugin de contrôleur "Url"
//        $event = $command->getEvent();
//        $httpRouter = $container->get('HttpRouter');
//        $event->setRouter($httpRouter);
//        $command->setRouter($httpRouter);

        return $command;
    }


}