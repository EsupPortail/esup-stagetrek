<?php


namespace Application\Service\Etudiant;


use API\Service\Traits\ReferentielEtudiantApiServiceAwareTrait;
use Application\Entity\Db\Adresse;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\ReferentielPromo;
use Application\Entity\Db\TerrainStage;
use Application\Exceptions\ImportException;
use Application\Provider\Roles\RolesProvider;
use Application\Service\Etudiant\Traits\EtudiantServiceAwareTrait;
use Application\Service\Misc\CSVService;
use Application\Service\Misc\Traits\CSVServiceAwareTrait;
use Application\Service\Referentiel\Traits\ReferentielServiceAwareTrait;
use Application\Validator\Import\EtudiantCsvImportValidator;
use DateTime;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Exception;
use UnicaenUtilisateur\Entity\Db\RoleInterface;
use UnicaenUtilisateur\Entity\Db\User;
use UnicaenUtilisateur\Service\Role\RoleServiceAwareTrait;
use UnicaenUtilisateur\Service\User\UserServiceAwareTrait;

/**
 * Class EtudiantService
 * @package Application\Service\Etudiant
 */
class EtudiantImportService
    implements
    ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    use UserServiceAwareTrait;
    use RoleServiceAwareTrait;
    //TODO : a revoir car
    /** @return RoleInterface */
    public function getEtudiantRole(): RoleInterface
    {
        return $this->getRoleService()->findByRoleId(RolesProvider::ROLE_ETUDIANT);
    }

    use ReferentielServiceAwareTrait;
    use EtudiantServiceAwareTrait;

    use ReferentielEtudiantApiServiceAwareTrait;

    /**
     * @param array $fileData
     * @return Etudiant[]
     * @throws \Application\Exceptions\ImportException
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function importEtudiantFromReferentiel(?ReferentielPromo $referentielPromo = null): array
    {

        $refSource = $this->getReferentielSourceCode();
        if(!isset($refSource) || $refSource==""){
            Throw new ImportException("L'import depuis un référentiel exterieur n'est pas correctement configuré");
        }
        if (!$referentielPromo) {
            throw new ImportException("La source de l'importation n'as pas été trouvée.");
        }
        $dateSource = $referentielPromo->getSource()->getCode();
        if($refSource != $dateSource){
            Throw new ImportException("L'import depuis le référentiel exterieur n'est pas configuré pour la source de code ".$dateSource);
        }
        $refDataConfig = $this->getReferentielDataConfig();

        if(empty($refDataConfig)
            || !isset($refDataConfig['num_etu'])
            || !isset($refDataConfig['nom'])
            || !isset($refDataConfig['prenom'])
            || !isset($refDataConfig['email'])
            || !isset($refDataConfig['date_naissance'])
        ){
            throw new ImportException("L'import depuis un référentiel exterieur n'est pas correctement configuré : noms des champs requis indéterminé");
        }
        $code = $referentielPromo->getCodePromo();
        if(!isset($code) || $code == ""){
            throw new ImportException("Le code de la promotion dans le référentiel n'est pas défini");
        }
        try{
            $apiData = $this->getReferentielEtudiantService()->findByCodePromo($code);
        }
        catch (Exception $e){
            $msg = sprintf("Erreur d'accés au référentiel : %s", $e->getMessage());
            throw new ImportException($msg);
        }
        if(!isset($apiData) || $apiData == "" || $apiData == '[]'){
            $msg = "Le référentiel n'as retourné aucun résultat.";
            $msg .= " Vérifiez la disponibilité du service et le code de promo utilisé.";
            throw new ImportException($msg);
        }
        $apiData = json_decode($apiData, true);
        /** @var Etudiant[] $etudiants */
        $etudiants = [];
        foreach ($apiData as $eutData) {
            try {
                $etudiant = $this->getEtudiantFromJSONData($eutData);
                if ($etudiant->getId() == null) {
                    $this->getObjectManager()->persist($etudiant);
                }
                $this->getObjectManager()->flush($etudiant);
                $etudiants[$etudiant->getNumEtu()] = $etudiant;
            }
            catch (Exception $e){
                throw new ImportException($e->getMessage());
            }
        }

        return $etudiants;
    }

    use CSVServiceAwareTrait;

    /**
     * @param array $fileData
     * @return Etudiant[]
     * @throws \Application\Exceptions\ImportException
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function importEtudiantFromCSV(array $fileData): array
    {
        /** @var Etudiant[] $etudiants */
        $etudiants = [];
        $this->getCsvService()->setHeaders(EtudiantCsvImportValidator::getImportHeader());
        $this->csvService->readCSVFile($fileData);
        $data = $this->getCsvService()->getData();
        foreach ($data as $rowData) {
            $etudiant = $this->getEtudiantFromCSVData($rowData);
            if ($etudiant->getId() == null) {
                $this->getObjectManager()->persist($etudiant);
            }
            $this->getObjectManager()->flush($etudiant);
            $etudiants[$etudiant->getNumEtu()] = $etudiant;
        }

        return $etudiants;
    }

    private function getEtudiantFromCSVData(mixed $rowData) : Etudiant
    {
        $numEtu = trim(($rowData[EtudiantCsvImportValidator::HEADER_NUM_ETUDIANT]) ?? "");
        $nom = trim(($rowData[EtudiantCsvImportValidator::HEADER_NOM]) ?? "");
        $prenom = trim(($rowData[EtudiantCsvImportValidator::HEADER_PRENOM]) ?? "");
        $email = trim(($rowData[EtudiantCsvImportValidator::HEADER_EMAIL]) ?? "");
        $dateNaissance =  CSVService::textToDate(($rowData[EtudiantCsvImportValidator::HEADER_DATE_NAISSANCE]) ?? null);
        $adresse = trim(($rowData[EtudiantCsvImportValidator::HEADER_ADRESSE]) ?? "");
        $complement = trim(($rowData[EtudiantCsvImportValidator::HEADER_ADRESSE_COMPLEMENT]) ?? "");
        $cp = trim(($rowData[EtudiantCsvImportValidator::HEADER_CP]) ?? "");
        $ville = trim(($rowData[EtudiantCsvImportValidator::HEADER_VILLE]) ?? "");
        $cedex = trim(($rowData[EtudiantCsvImportValidator::HEADER_CEDEX]) ?? "");

        $etudiant = $this->getEtudiantService()->findOneBy(['numEtu' => $numEtu]);
        if (!$etudiant) {
            $etudiant = new Etudiant();
            $etudiant->setNumEtu($numEtu);
        }
        $etudiant->setNom($nom);
        $etudiant->setPrenom($prenom);
        $etudiant->setEmail($email);
        $etudiant->setDateNaissance($dateNaissance);

        if($etudiant->getAdresse() === null){
            $adresse = new Adresse();
            $etudiant->setAdresse($adresse);
        }
        $etudiant->getAdresse()->setAdresse($adresse);
        $etudiant->getAdresse()->setComplement($complement);
        $etudiant->getAdresse()->setCodePostal($cp);
        $etudiant->getAdresse()->setVille($ville);
        $etudiant->getAdresse()->setCedex($cedex);
        return $etudiant;
    }


    protected ?string $referentielSourceCode = null;
    protected array $referentielDataConfig = [];

    public function getReferentielSourceCode(): ?string
    {
        return $this->referentielSourceCode;
    }

    public function setReferentielSourceCode(?string $referentielSourceCode): static
    {
        $this->referentielSourceCode = $referentielSourceCode;
        return $this;
    }


    public function setReferentielDataConfig(array $referentielDataConfig) : static
    {
        $this->referentielDataConfig = $referentielDataConfig;
        return $this;
    }
    public function getReferentielDataConfig() : array
    {
        return  $this->referentielDataConfig;
    }

    protected function getEtudiantFromJSONData(array $eutData) : Etudiant
    {
        $dataConfig = $this->getReferentielDataConfig();
        $keyNumEtu = $dataConfig['num_etu'];
        $keyNom = $dataConfig['nom'];
        $keyPrenom = $dataConfig['prenom'];
        $keyEmail = $dataConfig['email'];
        $keyDateNaissance = $dataConfig['date_naissance'];

        if(!isset($eutData[$keyNumEtu]) ||
            !isset($eutData[$keyNom]) || !
            !isset($eutData[$keyPrenom]) || !
            !isset($eutData[$keyEmail])
        ){
            throw new Exception("Les données fournisent par le référentiel ne contiennent pas les champs requis");
        }

        $numEtu = $eutData[$keyNumEtu];
        $nom = $eutData[$keyNom];
        $prenom = $eutData[$keyPrenom];
        $email = $eutData[$keyEmail];
        $dateNaissance = ($eutData[$keyDateNaissance]) ?? null;
        if(!isset($numEtu)){
            throw new ImportException("Le numéro de l'étudiant.e n'est pas défini");
        }
        if(!isset($nom)){
            throw new ImportException("Le nom de l'étudiant.e n'est pas défini");
        }
        if(!isset($prenom)){
            throw new ImportException("Le prénom de l'étudiant.e n'est pas défini");
        }
        if(!isset($email)){
            throw new ImportException("L'adresse mail de l'étudiant.e n'est pas défini");
        }

        $etudiant = $this->getEtudiantService()->findOneBy(['numEtu' => $numEtu]);
        if (!$etudiant) {
            $etudiant = new Etudiant();
            $etudiant->setNumEtu($numEtu);
        }
        $etudiant->setNom($nom);
        $etudiant->setPrenom($prenom);
        $etudiant->setEmail($email);
        if(isset($dateNaissance) && $dateNaissance instanceof DateTime) {
            $etudiant->setDateNaissance($dateNaissance);
        }
        if($etudiant->getAdresse() === null){
            $adresse = new Adresse();
            $etudiant->setAdresse($adresse);
        }
        return $etudiant;
    }


    //Fonction qui permet de regarder si certaines entité ont eu des changements depuis le dernier commit
    protected function hasUnitOfWorksChange(): bool
    {
        $unitOfwork = $this->getObjectManager()->getUnitOfWork();
        $unitOfwork->computeChangeSets();
        return
            !empty($unitOfwork->getScheduledEntityInsertions())
            || !empty($unitOfwork->getScheduledEntityUpdates())
            || !empty($unitOfwork->getScheduledCollectionUpdates())
            || !empty($unitOfwork->getScheduledEntityDeletions())
            || !empty($unitOfwork->getScheduledCollectionDeletions());
    }


}