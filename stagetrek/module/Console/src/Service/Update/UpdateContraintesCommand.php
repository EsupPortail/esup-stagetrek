<?php

namespace Console\Service\Update;

use Application\Entity\Db\ContrainteCursus;
use Application\Entity\Db\ContrainteCursusEtudiant;
use Application\Service\Contrainte\ContrainteCursusEtudiantService;
use Console\Service\Update\Interfaces\AbstractUpdateEntityCommand;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateContraintesCommand extends AbstractUpdateEntityCommand
{
    protected static $defaultName = 'update:contraintes';

    /**
     * @throws \Exception
     */
    protected function configure() : static
    {
        $this->setDescription("Mise à jours automatiques des contraintes");
        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //TODO : generer et envoyer un mail de rapport
        $this->initEntityService(ContrainteCursusEtudiantService::class);
        $io = $this->initInputOut($input, $output);
        try{
            $io->title("Maj des contraintes de cursus");
            $this->updateContraintes();
            $this->updateContraintesCursusEtudiants();
            $io->success("Maj terminée");
        }
        catch (Exception $e){
            $io->error($e->getMessage());
            return self::FAILURE;
        }
        return self::SUCCESS;
    }

    /**
     * @throws \Exception
     */
    private function updateContraintes(): void
    {
        $io = $this->getInputOutPut();
        $this->clearLog();
        $io->section("Contraintes");
        $contraintes = $this->getObjectManager()->getRepository(ContrainteCursus::class)->findAll();
        $cpt = sizeof($contraintes);
        $io->progressStart($cpt);
        $this->execProcedure('update_contraintes_cursus');
        $io->progressAdvance(round($cpt/2));
       foreach ($contraintes as $contrainte) {
            if($contrainte->isContradictoire()){
                $this->addLog(sprintf("Contrainte %s : contradiction", $contrainte->getLibelle()));
            }
           $io->progressAdvance();
        }
        $io->progressFinish();
        $this->renderLog();
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Exception
     */
    private function updateContraintesCursusEtudiants(): void
    {
        $io = $this->getInputOutPut();
        $this->clearLog();
        $io->section("Contraintes de cursus des étudiants");

        /** @var ContrainteCursusEtudiantService $service */
        $service = $this->getEntityService();
        $contraintesCursusEtudiants = $service->findAll();
        $io->progressStart(sizeof($contraintesCursusEtudiants));
        /** @var ContrainteCursusEtudiant $c */
        foreach ($contraintesCursusEtudiants as $c){
            $etat = $c->getEtatActif();
            $service->updateEtat($c);
            $newEtat = $c->getEtatActif();
            if(!isset($etat) || $newEtat->getId() != $etat->getId()){
                $etudiant = $c->getEtudiant();
                $msg = sprintf("Etudiant %s - Contrainte %s : %s", $etudiant->getNumEtu(), $c->getLibelle(), $newEtat->getType()->getLibelle());
                $infos = $newEtat->getInfos();
                if($infos !== null && $infos !== ""){
                    $msg .= PHP_EOL . $infos;
                }
                $this->addLog($msg);
            }
            $io->progressAdvance();
        }
        $io->progressFinish();
        $this->renderLog();

    }
}