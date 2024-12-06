<?php

namespace Application\Service\TerrainStage;


use Application\Entity\Db\Adresse;
use Application\Entity\Db\CategorieStage;
use Application\Entity\Db\TerrainStage;
use Application\Service\Misc\CommonEntityService;
use Application\Service\Misc\CSVService;
use Application\Service\Misc\Traits\CSVServiceAwareTrait;
use Application\Service\Stage\Traits\SessionStageServiceAwareTrait;
use Application\Validator\Import\TerrainStageCsvImportValidator;

class TerrainStageService extends CommonEntityService
{

    use SessionStageServiceAwareTrait;
    /** @return string */
    public function getEntityClass(): string
    {
        return TerrainStage::class;
    }

    /**
     * @param Boolean $onlyActif
     * @return TerrainStage[]
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function findAll(bool $onlyActif = false): array
    {
        $criteria=[];
        if($onlyActif){
            $criteria = ["actif" => true];
        }
        return $this->findAllBy($criteria, ['libelle' => 'ASC']);
    }

    /**
     * @param TerrainStage $entity
     * @param string|null $serviceEntityClass
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function add(mixed $entity, string $serviceEntityClass = null): mixed
    {
        $this->getObjectManager()->persist($entity);
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush($entity);
            $this->getSessionStageService()->computePlacesForSessions();
        }
        return $entity;
    }


    /**
     * @param mixed $entity
     * @param string|null $serviceEntityClass classe de l'entité
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function update(mixed $entity, string $serviceEntityClass = null): mixed
    {
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush($entity);
            $this->getSessionStageService()->computePlacesForSessions();
        }
        return $entity;
    }

    /**
     * Supprime une entité
     *
     * @param mixed $entity
     * @param string|null $serviceEntityClass classe de l'entité
     * @return $this
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function delete(mixed $entity, string $serviceEntityClass = null) : static
    {
        $this->getObjectManager()->remove($entity);

        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush();
            $this->getSessionStageService()->computePlacesForSessions();
        }
        return $this;
    }


    use CSVServiceAwareTrait;

    /**
     * @desc Hypothèse : la vérification des données par l'importValidateur a déjà été faite
     * @param array $fileData
     * @return TerrainStage[]
     * @throws \Application\Exceptions\ImportException
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function importFromCSV(array $fileData): array
    {
        $this->getCsvService()->setHeaders(TerrainStageCsvImportValidator::getImportHeader());
        $this->csvService->readCSVFile($fileData);
        $data = $this->getCsvService()->getData();
        $terrains = [];
        foreach ($data as $rowData) {
            $terrain = $this->getTerrainFromData($rowData);
            if($terrain->getId() == null){
                $this->getObjectManager()->persist($terrain);
            }
            $this->getObjectManager()->flush($terrain);
            $terrains[] = $terrain;
        }
        $this->getSessionStageService()->computePlacesForSessions();
        return $terrains;
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    private function getTerrainFromData(mixed $rowData) : TerrainStage
    {
        $codeCategorie = trim(($rowData[TerrainStageCsvImportValidator::HEADER_CODE_CATEGORIE]) ?? "");
        $codeTerrain = trim(($rowData[TerrainStageCsvImportValidator::HEADER_CODE_TERRAIN]) ?? "");
        $libelle = trim(($rowData[TerrainStageCsvImportValidator::HEADER_LIBELLE])  ?? "");
        $service = trim(($rowData[TerrainStageCsvImportValidator::HEADER_SERVICE])  ?? "");
        $min = CSVService::textToInt(($rowData[TerrainStageCsvImportValidator::HEADER_CAPA_MIN]) ?? 0, 0);
        $ideal = CSVService::textToInt(($rowData[TerrainStageCsvImportValidator::HEADER_CAPA_IDEAL]) ?? 0, 0);
        $max = CSVService::textToInt(($rowData[TerrainStageCsvImportValidator::HEADER_CAPA_MAX]) ?? 0, 0);
        $horsSubdivision =  CSVService::yesNoValueToBoolean(($rowData[TerrainStageCsvImportValidator::HEADER_HORS_SUBDIVISION])  ?? "", false);
        $preferences =  CSVService::yesNoValueToBoolean(($rowData[TerrainStageCsvImportValidator::HEADER_PREFERENCES])  ?? "", true);
        $lien = trim(($rowData[TerrainStageCsvImportValidator::HEADER_LIEN])  ?? "");
        $adresse = trim(($rowData[TerrainStageCsvImportValidator::HEADER_ADRESSE])  ?? "");
        $complement = trim(($rowData[TerrainStageCsvImportValidator::HEADER_ADRESSE_COMPLEMENT])  ?? "");
        $cp = trim(($rowData[TerrainStageCsvImportValidator::HEADER_CP])  ?? "");
        $ville = trim(($rowData[TerrainStageCsvImportValidator::HEADER_VILLE])  ?? "");
        $cedex = trim(($rowData[TerrainStageCsvImportValidator::HEADER_CEDEX])  ?? "");

        /** @var CategorieStage $categorie */
        $categorie = $this->getObjectManager()->getRepository(CategorieStage::class)->findOneBy(['code' => $codeCategorie]);
        $terrain = $this->getObjectRepository()->findOneBy(['code' => $codeTerrain]);
        if (!$terrain) {
            $terrain = new TerrainStage();
            $terrain->setCode($codeTerrain);
            $terrain->setCategorieStage($categorie);
        }

        $terrain->setLibelle($libelle);
        $terrain->setService($service);
        $terrain->setMinPlace($min);
        $terrain->setIdealPlace($ideal);
        $terrain->setMaxPlace($max);
        $terrain->setPreferencesAutorisees($preferences);
        $terrain->setHorsSubdivision($horsSubdivision);
        $terrain->setLien($lien);
        if($terrain->getAdresse() === null){
            $adresse = new Adresse();
            $this->getObjectManager()->persist($adresse);
            $terrain->setAdresse($adresse);
        }
        $terrain->getAdresse()->setAdresse($adresse);
        $terrain->getAdresse()->setComplement($complement);
        $terrain->getAdresse()->setCodePostal($cp);
        $terrain->getAdresse()->setVille($ville);
        $terrain->getAdresse()->setCedex($cedex);
        return $terrain;
    }
}