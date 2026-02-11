<?php

namespace Console\Service\Evenement\Factory;

use Application\Provider\Misc\EnvironnementProvider;
use Console\Service\Evenement\EvenementTraiterCommand;
use DateInterval;
use Evenement\Service\Evenement\EvenementService;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use UnicaenEvenement\Service\Etat\EtatService;
use UnicaenEvenement\Service\EvenementGestion\EvenementGestionService;
use UnicaenEvenement\Service\Journal\JournalService;

class EvenementTraiterCommandFactory extends Command
{
    /**
     * @param ContainerInterface $container
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \DateMalformedIntervalStringException
     */
    public function __invoke(ContainerInterface $container): EvenementTraiterCommand
    {
        $command = new EvenementTraiterCommand();
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

        $config = $container->get('Configuration');
        if (isset($config['unicaen-evenement']['max_time_execution'])) {
            $max_time_execution = $config['unicaen-evenement']['max_time_execution'];
            $command->setMaxExecutionTime($max_time_execution);
        }
        if (isset($config['unicaen-evenement']['delai-peremption'])) {
            $delai = $config['unicaen-evenement']['delai-peremption'];
            $command->setDelaiPeremption($delai);
        }
        if (isset($config['unicaen-evenement']['simulate_execution'])) {
            $command->setHasToSimulatate($config['unicaen-evenement']['simulate_execution']);
        }
        $env = ($config['console-cli']['console_env']) ??  EnvironnementProvider::TEST;
        $command->setEnvironnement($env);
        return $command;
    }
}