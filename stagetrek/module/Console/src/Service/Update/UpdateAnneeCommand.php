<?php

namespace Console\Service\Update;

use Application\Entity\Db\AnneeUniversitaire;
use Application\Service\AnneeUniversitaire\AnneeUniversitaireService;
use Console\Service\Update\Interfaces\AbstractUpdateEntityCommand;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateAnneeCommand extends AbstractUpdateEntityCommand
{
    protected static $defaultName = 'update-annees';

    /**
     * @throws \Exception
     */
    protected function configure() : static
    {
        $this->setDescription("Mise à jours automatiques des années universitaires");
        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //TODO : generer et envoyer un mail de rapport
        $this->initEntityService(AnneeUniversitaireService::class);
        $io = $this->initInputOut($input, $output);
        $io->title("Maj des années");
        try{
            $io->section("Etats");
            /** @var AnneeUniversitaireService $service */
            $service = $this->getEntityService();
            $annees = $service->findAll();
            $io->progressStart(sizeof($annees));

            /** @var AnneeUniversitaire $annee */
            foreach ($annees as $annee) {
                $etat = $annee->getEtatActif();
                $service->updateEtat($annee);
                $newEtat = $annee->getEtatActif();
                if(!isset($etat) || $newEtat->getId() != $etat->getId()){
                    $msg = sprintf("Année %s : %s", $annee->getLibelle(), $newEtat->getType()->getLibelle());
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