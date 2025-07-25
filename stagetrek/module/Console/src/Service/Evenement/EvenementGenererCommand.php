<?php

namespace Console\Service\Evenement;

use Evenement\Provider\TypeEvenementProvider;
use Evenement\Service\MailAuto\Abstract\MailAutoEvenementServiceInterface;
use Evenement\Service\MailAuto\Traits\MailAutoEvenementServiceAwareTrait;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use UnicaenEvenement\Service\Etat\EtatServiceAwareTrait;
use UnicaenEvenement\Service\Evenement\EvenementServiceAwareTrait;
use UnicaenEvenement\Service\EvenementGestion\EvenementGestionServiceAwareTrait;
use UnicaenEvenement\Service\Journal\JournalServiceAwareTrait;

class EvenementGenererCommand extends Command
{
    protected static $defaultName = 'evenement:generer';

    use EvenementGestionServiceAwareTrait;
    use EvenementServiceAwareTrait;
    use EtatServiceAwareTrait;
    use JournalServiceAwareTrait;

    use MailAutoEvenementServiceAwareTrait;

    protected function configure() : static
    {
        $this->setDescription("Générations automatiques des événements");
        return $this;
    }


    protected ?SymfonyStyle $io = null;
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //TODO : generer et envoyer un mail de rapport
        $this->io = new SymfonyStyle($input, $output);
        try {
            $this->io->title("Générations des événements");
            $this->generateEvents(TypeEvenementProvider::MAIL_AUTO_STAGE_DEBUT_CHOIX);
            $this->generateEvents(TypeEvenementProvider::MAIL_AUTO_RAPPEL_STAGE_CHOIX);
            $this->generateEvents(TypeEvenementProvider::MAIL_AUTO_DEBUT_VALIDATION_STAGE);
            $this->generateEvents(TypeEvenementProvider::MAIL_AUTO_RAPPEL_STAGE_VALIDATION);
            $this->io->success("Générations des événements terminées");
        }
        catch (Exception $e){
            $this->io->error($e->getMessage());
            return self::FAILURE;
        }
        return self::SUCCESS;
    }

    protected function getMailEventService(string $typeEventCode) : ?MailAutoEvenementServiceInterface
    {
        return match($typeEventCode){
            TypeEvenementProvider::MAIL_AUTO_STAGE_DEBUT_CHOIX =>  $this->getMailAutoStageDebutChoixEvenementService(),
            TypeEvenementProvider::MAIL_AUTO_RAPPEL_STAGE_CHOIX =>  $this->getMailAutoStageRappelChoixEvenementService(),
            TypeEvenementProvider::MAIL_AUTO_DEBUT_VALIDATION_STAGE =>  $this->getMailAutoStageDebutValidationService(),
            TypeEvenementProvider::MAIL_AUTO_RAPPEL_STAGE_VALIDATION =>  $this->getMailAutoStageRappelValidationService(),
            default => null
        };
    }

    /**
     * @throws Exception
     */
    protected function generateEvents(string $typeEventCode) : void
    {
        $this->io->section("Evenements de type ".$typeEventCode);
        $eventService = $this->getMailEventService($typeEventCode);
        if (!$eventService) {
            throw new Exception(sprintf("Le service de gestion des evenements de type %s n'est pas definie", $typeEventCode));
        }
        $entities = $eventService->findEntitiesForNewEvent();
        if(sizeof($entities) == 0){
            $this->io->text("Aucun événement généré");
            return;
        }
        $this->io->progressStart(sizeof($entities));
        $nbOp = 0;
        $nbErreur = 0;
        foreach ($entities as $entity) {
            try {
                $this->io->progressAdvance();
                $eventService->create($entity);
                $nbOp++;
            }
            catch (Exception $e){
                    $nbErreur++;
                    $this->io->writeln("");
                    $this->io->error($e->getMessage());
                    if($nbErreur>=10){
                        $raport = "Arrets de la générétion des événenemts : trop d'erreurs générées";
                        $raport .= sprintf("\n * Nombre d'événements générés : %s", $nbOp);
                        $raport .= sprintf("\n * Nombre d'erreurs : %s", $nbErreur);
                        throw new Exception($raport);
                    }
                }
            }
        $this->io->progressFinish();
        $raport = sprintf("* Nombre d'événements générés : %s", $nbOp);
        if($nbErreur>0) {
            $raport .= sprintf("\n * Nombre d'erreurs : %s", $nbErreur);
        }
        $this->io->info($raport);

    }
}