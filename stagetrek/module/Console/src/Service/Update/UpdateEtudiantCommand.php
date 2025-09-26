<?php

namespace Console\Service\Update;

use Application\Entity\Db\Etudiant;
use Application\Service\Etudiant\EtudiantService;
use Console\Service\Update\Interfaces\AbstractUpdateEntityCommand;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateEtudiantCommand extends AbstractUpdateEntityCommand
{
    protected static $defaultName = 'update:annees';

    /**
     * @throws \Exception
     */
    protected function configure() : static
    {
        $this->setDescription("Mise à jours automatiques des étudiants");
        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //TODO : generer et envoyer un mail de rapport
        $this->initEntityService(EtudiantService::class);
        $io = $this->initInputOut($input, $output);
        $io->title("Maj des étudiants");
        $io->section("Etats");
        try{
            /** @var EtudiantService $service */
            $service = $this->getEntityService();
            $etudiants = $service->findAll();
            $io->progressStart(sizeof($etudiants));
            /** @var Etudiant $etudiant */
            foreach ($etudiants as $etudiant) {

                $etat = $etudiant->getEtatActif();
                $service->updateEtat($etudiant);
                $newEtat = $etudiant->getEtatActif();
                if(!isset($etat) || $newEtat->getId() != $etat->getId()){
                    $msg = sprintf("Etudiant %s : %s", $etudiant->getNumEtu(), $newEtat->getType()->getLibelle());
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
            $io->success("Maj terminée");

        }catch (Exception $e){
            $io->error($e->getMessage());
            return self::FAILURE;
        }
        return self::SUCCESS;
    }

}