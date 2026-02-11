<?php

namespace Console\Service\Nettoyage;

use Application\Entity\Db\ContrainteCursusEtudiant;
use Application\Entity\Db\Disponibilite;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\Groupe;
use Application\Entity\Db\Stage;
use Application\Entity\Traits\Etudiant\HasEtudiantTrait;
use Application\Service\Etudiant\Traits\EtudiantServiceAwareTrait;
use Application\Service\Stage\Traits\StageServiceAwareTrait;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * TODO : Commande a revoir pour
 * - archiver les données en "anonysant" plutot qu'en supprimant réelement les données
 */
//TODO : faire une option "anonymasation" qui serait sans doute préférable.
//TODO : faire une option "Archivage" en ammont qui mettrait a jours les champs "histo_destruction"
//TODO : a revoir : est-ce que la suppression de l'étudiant en temps qu'utilisateur ne risque pas de supprimer d'autre donnée ?
//TODO : a revoir : cas des conventions : suppression du fichier

class CleanDataCommand extends Command implements ObjectManagerAwareInterface
{
    use EtudiantServiceAwareTrait;
    use StageServiceAwareTrait;
    use HasEtudiantTrait;
    use ProvidesObjectManager;

    protected static $defaultName = 'data:clear';
    protected ?SymfonyStyle $io = null;

    protected function configure(): static
    {
        $this->setDescription("Suppression des données qui ne doivent pas être conservées");
        return $this;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        try {
            $this->io = new SymfonyStyle($input, $output);
        } catch (Exception $e) {
            $this->io->error($e->getMessage());
            exit(-1);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        try {
            $this->io->warning("Attention cette action est irréversible et peut provoquer la perte de données");
            $continue = $this->io->confirm("Voulez-vous vraiment continuer ?", false);
            if (!$continue) {
                $this->io->text("Abandon du nettoyage des données");
                return Command::SUCCESS;
            }

        } catch (Exception $e) {
            $this->io->error($e->getMessage());
            return self::FAILURE;
        }
        return self::SUCCESS;
    }

    //TODo : supprimer les données
}
