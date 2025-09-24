<?php

namespace Console\Service\Update;

use Application\Entity\Db\Parametre;
use Application\Provider\Fichier\NatureFichierProvider;
use Application\Service\ConventionStage\ConventionStageService;
use Application\Service\Parametre\ParametreService;
use Console\Service\Update\Interfaces\AbstractUpdateEntityCommand;
use DateInterval;
use DateTime;
use Exception;
use UnicaenFichier\Entity\Db\Fichier;
use UnicaenFichier\Service\Fichier\FichierService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateConventionStageCommand extends AbstractUpdateEntityCommand
{
    protected static $defaultName = 'update:conventions-stages';

    /**
     * @throws \Exception
     */
    protected function configure() : static
    {
        $this->setDescription("Mise à jours automatiques des conventions de stage");
        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //TODO : generer et envoyer un mail de rapport
        $this->initEntityService(ConventionStageService::class);
        $this->initFileService(FichierService::class);
        $io = $this->initInputOut($input, $output);
        try{
            $io->title("Maj des conventions des stages");
            $this->deleteOldConventions();
            $this->clearOldFiles(); //Suppession automatiques des fichiers qui sont archivée
            $this->genrerateConventions();
            $io->success("Maj terminée");

        }catch (Exception $e){
            $io->error($e->getMessage());
            return self::FAILURE;
        }
        return self::SUCCESS;
    }

    private function deleteOldConventions() : void
    {
//        TODO : a voir commment récupérer les étudiants archivée pour déterminer ensuites qu'elles fichier supprimer
        $io = $this->getInputOutPut();
        $io->section("Nettoyage des anciennes conventions de stages");
        $io->info("Suppression automatique des conventions de stages pas encore implémentée");
    }
    /**
     * @throws Exception
     */
    protected function clearOldFiles(): void
    {
        $io = $this->getInputOutPut();
        $io->section("Nettoyage des fichiers");

        $date = new DateTime();
        $duree = $this->getParametreService()->getParametreValue(Parametre::DUREE_CONSERVCATION);
        $date->sub(new DateInterval('P'.$duree.'D'));
        /** @var FichierService $service */
        $fileService = $this->getFileService();
        $qb = $this->getObjectManager()->getRepository(Fichier::class)->createQueryBuilder($alias = 'f');
        $qb = $qb->join("$alias.nature", 'n')
            ->andWhere('f.histoDestruction IS not NULL')
           ->andWhere('f.histoDestruction < :date')
           ->andWhere('n.code = :codeNature');
        $qb->setParameter('date', $date);
        $qb->setParameter('codeNature', NatureFichierProvider::CONVENTION);

        $fichiers = $qb->getQuery()->getResult();
        if(sizeof($fichiers) == 0) {
            $io->info("Pas de fichier à supprimer");
            $this->renderLog();
            return;
        }

        $cpt = 0;
        $io->progressStart(sizeof($fichiers));
        foreach ($fichiers as $fichier){
            $cpt++;
            $fileService->delete($fichier);
            $io->progressAdvance();
        }
        $io->progressFinish();
        $io->info(sprintf("%s / %s anciennes conventions ont été supprimées", $cpt, sizeof($fichiers)));

    }

    /**
     * @throws Exception
     */
    protected function genrerateConventions(): void
    {
        $io = $this->getInputOutPut();
        $io->section("Génération des conventions de stages");
        $io->info("Génération automatique des conventions de stages pas encore implémentée");
    }

    protected function getParametreService() : ParametreService
    {
        return $this->getServiceManager()->get(ParametreService::class);
    }
    protected ?FichierService $fileService = null;
    public function getFileService(): ?FichierService
    {
        return $this->fileService;
    }
    public function setFileService(?FichierService $fileService): void
    {
        $this->fileService = $fileService;
    }
    private function initFileService() : FichierService
    {
        if(!$this->fileService){
            try {
                $this->fileService = $this->getServiceManager()->get(FichierService::class);
            } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
                throw new Exception($e->getMessage());
            }
        }
        return $this->fileService;
    }


}