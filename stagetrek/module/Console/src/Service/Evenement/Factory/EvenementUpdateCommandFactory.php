<?php

namespace Console\Service\Evenement\Factory;

use Console\Service\Evenement\EvenementUpdateCommand;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use UnicaenEvenement\Service\Etat\EtatService;
use UnicaenEvenement\Service\Evenement\EvenementService;
use UnicaenEvenement\Service\EvenementGestion\EvenementGestionService;
use UnicaenEvenement\Service\Journal\JournalService;

class EvenementUpdateCommandFactory extends Command
{

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): EvenementUpdateCommand
    {
        $command = new EvenementUpdateCommand();

        /**
         * @var EvenementService $evenementService
         * @var EvenementGestionService $evenementGestionService
         * @var EtatService $etatService
         * @var JournalService $journalService
         */
        $evenementService = $container->get(EvenementService::class);
        $etatService = $container->get(EtatService::class);
        $journalService = $container->get(JournalService::class);
        $command->setEvenementService($evenementService);
        $command->setEtatEvenementService($etatService);
        $command->setJournalService($journalService);

        $config = $container->get('Configuration');
        if (isset($config['unicaen-evenement']['max_time_execution'])) {
            $max_time_execution = $config['unicaen-evenement']['max_time_execution'];
            $command->setMaxExecutionTime(intval($max_time_execution));
        }
        if (isset($config['unicaen-evenement']['delai-peremption'])) {
            $delai = $config['unicaen-evenement']['delai-peremption'];
            $command->setDelaiPeremption($delai);
        }

        return $command;
    }
}