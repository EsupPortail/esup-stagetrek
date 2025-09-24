<?php


namespace Application\Service\Referentiel;

use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\Groupe;
use Application\Entity\Db\ReferentielPromo;
use Application\Entity\Db\Source;
use Application\Entity\Traits\AnneeUniversitaire\HasAnneeUniversitaireTrait;
use Application\Entity\Traits\Etudiant\HasEtudiantsTrait;
use Application\Entity\Traits\Groupe\HasGroupesTrait;
use Application\Entity\Traits\Referentiel\HasReferentielPromoTrait;
use Application\Exceptions\ImportException;
use Application\Service\Groupe\Traits\GroupeServiceAwareTrait;
use Application\Service\Referentiel\Interfaces\AbstractImportEtudiantsService;
use Application\Service\Referentiel\Interfaces\ImportEtudiantsServiceInterface;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use UnicaenApp\View\Helper\Messenger;
use UnicaenDbImport\Entity\Db\Service\ImportLog\ImportLogServiceAwareTrait;
use UnicaenDbImport\Service\Traits\ImportServiceAwareTrait;
use UnicaenDbImport\Service\Traits\SynchroServiceAwareTrait;
use Exception;

/**
 * @desc Import par un référentiel requierant DB-Import
 */
class ReferentielDbImportEtudiantService extends AbstractImportEtudiantsService implements ImportEtudiantsServiceInterface, ObjectManagerAwareInterface
{
    use ProvidesObjectManager;
    use ImportServiceAwareTrait;
    use SynchroServiceAwareTrait;
    use ImportLogServiceAwareTrait;

    const IMPORT_NAME ="ImportEtudiant";
    const IMPORT_TABLE_DESTINATION ="import_referentiel_etudiant";

    const SYNCHRO_NAME ="SynchroEtudiant";
    const SYNCHRO_TABLE_SOURCE ="v_synchro_etudiant";
    const SYNCHRO_TABLE_DESTINATION ="etudiant";

    public function getKey(): string
    {
        return Source::APOGEE;
    }
    private function getWhereClause(?ReferentielPromo $referentiel, ?AnneeUniversitaire $annee=null) : ?string
    {
        $where = null;
        if(isset($referentiel)){
            $codeVet = $referentiel->getCodePromo();
            $where .= sprintf("code_vet = '%s'", $codeVet);
        }
        if(isset($annee)) {
            if (isset($where)) {
                $where .= (isset($where)) ? " and " : null;
            }
            $codeAnnee = $annee->getCode();
            $where .= sprintf("code_annee = '%s'", $codeAnnee);
        }
        return $where;
    }

    use HasReferentielPromoTrait;
    use HasAnneeUniversitaireTrait;

    /**
     * @throws \Application\Exceptions\ImportException
     */
    protected function assertImportData(array $data) : bool
    {
        $referentiel = ($data['referentiel']) ?? null;
        $annee = ($data['annee']) ?? null;
        if (!isset($referentiel) || !$referentiel instanceof ReferentielPromo) {
            throw new ImportException("Le référentiel de données fournis n'est pas valide");
        }
        if (!isset($annee) || !$annee instanceof AnneeUniversitaire) {
            throw new ImportException("L'année fournise n'est pas valide");
        }
        $this->setReferentielPromo($referentiel);
        $this->setAnneeUniversitaire($annee);
        /** @var Groupe[] $groupes */
        $groupes = $referentiel->getGroupes()->toArray();
        $groupes = array_filter($groupes, function (Groupe $groupe) use ($annee) {
            return $groupe->getAnneeUniversitaire()->getCode() == $annee->getCode();
        });
        $this->setGroupes($groupes);
        return true;
    }

    protected function importerEtudiants() : array
    {
        $referentiel = $this->getReferentielPromo();
        $annee = $this->getAnneeUniversitaire();
        $etudiants = [];
        try {
            //On force a vider la table d'import (cas ou il n'y a pas de résultat a l'import, la table conserver les données)
            $sql = sprintf("truncate table %s", self::IMPORT_TABLE_DESTINATION);
            $stmt = $this->getObjectManager()->getConnection()->prepare($sql);
            $stmt->executeQuery();

            $import = $this->importService->getImportByName(self::IMPORT_NAME);
            $where = $this->getWhereClause($referentiel, $annee);
            $import->getSource()->setWhere($where);

            $synchro = $this->synchroService->getSynchroByName(self::SYNCHRO_NAME);
            $this->importService->runImport($import);
            $importLog = $this->importLogService->findLastLogForImport($import);
            if(!$importLog->isSuccess()){
                throw new ImportException($importLog->getLogToHtml());
            }

            //On récupére la liste des numéros d'étudiants afin de pouvoir faire l'ajout dans les groupes aprés
            //Permet aussi de ne pas avoir a faire de synchro si rien n'as été importé
            $sql = sprintf("select num_etu from %s", self::IMPORT_TABLE_DESTINATION);
            $stmt = $this->getObjectManager()->getConnection()->prepare($sql);
            $etuImportee = $stmt->executeQuery()->fetchAllAssociative();
//            Pas d'import = pas besoins d'essayer de faire une synchro
            if(empty($etuImportee)){
                $this->addLog("Aucun étudiant importé");
                $this->setLogType((!$importLog->hasProblems()) ? Messenger::SUCCESS : Messenger::WARNING);
                return [];
            }

            $this->synchroService->runSynchro($synchro);
            $synchroLog = $this->importLogService->findLastLogForSynchro($synchro);
            if(!$synchroLog->isSuccess()){
                throw new ImportException($synchroLog->getLogToHtml());
            }
            $this->addLog($synchroLog->getLogToHtml());

            if($importLog->hasProblems()||$synchroLog->hasProblems()){
                $this->setLogType(Messenger::WARNING);
            }
            else{
                $this->setLogType(Messenger::SUCCESS);
            }

            foreach ($etuImportee as $data) {
                $numEtu = $data['num_etu'];
                $etudiant = $this->getEtudiantService()->findOneBy(['numEtu' => $numEtu]);
                $etudiants[$numEtu] = $etudiant;
            }
            //maj des état requit pour les nouveaux;
            $this->getEtudiantService()->updateEtats($etudiants);
        } catch (Exception $e) {
            $this->setLogType(Messenger::ERROR);
            $this->addLog($e->getMessage());
            throw new ImportException($e->getMessage(), previous: $e);
        }
        $this->setEtudiants($etudiants);

        return $etudiants;
    }

    protected function addEtudiantsInGroupes() : static
    {
        $annee = $this->getAnneeUniversitaire();
        $groupes = $this->getGroupes();
        $etudiants = $this->getEtudiants()->toArray();
        if(!isset($groupes) || $groupes->isEmpty()){
            $this->addLog(sprintf("Aucun groupe n'est associé au référentiel pour l'année %s", $annee->getLibelle()));
            return $this;
        }
        try {
            foreach ($groupes as $groupe) {
                $etudiantsCanBeAdd = $this->getGroupeService()->findEtudiantsCanBeAddInGroupe($groupe, $etudiants);
                if(!empty($etudiantsCanBeAdd)) {
                    $this->getGroupeService()->addEtudiants($groupe, $etudiantsCanBeAdd);
                }
                $msg = sprintf("%s étudiants inscrits dans le groupe %s - %s",
                    sizeof($etudiantsCanBeAdd), $groupe->getLibelle(), $groupe->getAnneeUniversitaire()->getLibelle()
                );
                $this->addLog($msg);
                $this->getEtudiantService()->updateEtats($etudiantsCanBeAdd);
            }
        } catch (Exception $e) {
            $this->setLogType(Messenger::ERROR);
            $this->addLog("Erreur lors de l'ajout des étudiants dans les groupes");
            $this->addLog($e->getMessage());
            throw new ImportException($e->getMessage(), previous: $e);
        }
        return $this;
    }
}
