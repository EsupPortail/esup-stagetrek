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

//TODO : faire une option "anonymasation" qui serait sans doute préférable.
//TODO : a revoir : est-ce que la suppression de l'étudiant en temps qu'utilisateur ne risque pas de supprimer d'autre donnée ?
//TODO : a revoir : cas des conventions : suppression du fichier ?
//TODO : a vérifier : est-ce que supprimer le stage supprime également les instances d'état ? pas sur


class RemoveEtudiantCommand extends Command implements ObjectManagerAwareInterface
{
    use EtudiantServiceAwareTrait;
    use StageServiceAwareTrait;
    use HasEtudiantTrait;
    use ProvidesObjectManager;

    protected static $defaultName = 'etudiant:delete';
    protected ?SymfonyStyle $io = null;

    protected function configure(): static
    {
        $this->setDescription("Suppression des données relative à un etudiant précis");
        $this->addOption('etudiant', '-e', InputOption::VALUE_OPTIONAL, "Id de l'étudiant");
        return $this;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        try {
            $this->io = new SymfonyStyle($input, $output);
            $etudiantId = $input->getOption('etudiant');
            if ($etudiantId !== null) {
                $etudiantId = intval($etudiantId, 0);
                if ($etudiantId <= 0) {
                    throw new Exception("L'identifiant de l'étudiant n'est pas valide");
                }
                /** @var Etudiant $etudiant */
                $etudiant = $this->getEtudiantService()->find($etudiantId);
                if (!isset($etudiant)) {
                    throw new Exception("L'étudiant d'identifiant " . $etudiantId . " n'existe pas");
                }
                $this->etudiant = $etudiant;
            } else {
                throw new Exception("Préciser l'étudiant à supprimer");
            }
        } catch (Exception $e) {
            $this->io->error($e->getMessage());
            exit(-1);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        try {
            $this->io->warning("Attention cette action est irréversible");
            $stages = $this->etudiant->getStages()->toArray();
            if (!empty($stages) > 0) {
                $this->io->warning(sprintf("L'étudiant est inscrit dans %s stages", sizeof($stages)));
            }
            $msg = sprintf("Vous-êtes sur le point de supprimer toutes les données liées à %s. Voulez-vous vraiment continuer ?", $this->etudiant->getDisplayName());
            $continue = $this->io->confirm($msg, false);
            if (!$continue) {
                $this->io->text("Abandon de la suppression de l'étudiant");
                return Command::SUCCESS;
            }
            /** @var Stage $stage */
            foreach ($stages as $stage) {
                if ($stage->hasAffectationStage()) {
                    $this->getObjectManager()->remove($stage->getAffectationStage());
                    $this->getObjectManager()->flush();
                }
                $this->getStageService()->delete($stage);
            }
            $this->io->writeln("Suppression des stages : OK");

            /** @var Groupe $groupe */
            foreach ($this->etudiant->getGroupes()->toArray() as $groupe) {
                $this->getEtudiant()->removeGroupe($groupe);
            }
            $this->io->writeln("Retrait de l'étudiant des groupes : OK");


            /** @var ContrainteCursusEtudiant $contrainte */
            foreach ($this->etudiant->getContraintesCursusEtudiants()->toArray() as $contrainte) {
                $this->getObjectManager()->remove($contrainte);
                $this->getObjectManager()->flush();
            }
            $this->io->writeln("Retrait des contraintes de cursus l'étudiant : OK");

            /** @var Disponibilite $dispo */
            foreach ($this->etudiant->getDisponibilites()->toArray() as $dispo) {
                $this->getObjectManager()->remove($dispo);
                $this->getObjectManager()->flush();
            }
            $this->io->writeln("Retrait des disponibilités l'étudiant : OK");

            $user = $this->etudiant->getUser();
            $this->getEtudiantService()->delete($this->etudiant);
            $this->io->writeln("Suppression l'étudiant : OK");

            if(isset($user) && $user->getRoles()->isEmpty()){
                $this->getObjectManager()->remove($user);
                $this->getObjectManager()->flush();
                $this->io->writeln("Suppresion du compte utilisateur : OK");
            }

        } catch (Exception $e) {
            $this->io->error($e->getMessage());
            return self::FAILURE;
        }
        return self::SUCCESS;
    }
}
