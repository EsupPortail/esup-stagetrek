<?php

namespace Application\Service\TerrainStage;


use Application\Entity\Db\CategorieStage;
use Application\Service\Misc\CommonEntityService;
use Application\Service\Misc\CSVService;
use Application\Service\Misc\Traits\CSVServiceAwareTrait;
use Application\Validator\Import\CategorieStageCsvImportValidator;
use Exception;

/** ToDo : Adapter pour être utilisé via l'entityManager */
class CategorieStageService extends CommonEntityService
{
    /** @return string */
    public function getEntityClass(): string
    {
        return CategorieStage::class;
    }

    public function findAll(): array
    {
        $result = $this->getObjectRepository()->findBy([], ['ordre' => 'ASC']);
        return $this->getList($result);
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
        if(!$entity->getTerrainsStages()->isEmpty()){
            throw new Exception("La catégorie de stage est utilisé par au moins un terrain");
        }
        $this->getObjectManager()->remove($entity);
        $this->getObjectManager()->flush();
        return $this;
    }

    /**
     * @param array $fileData
     * @return CategorieStage[]
     * @desc Hypothèse : la vérification des données par l'importValidateur a déjà été faite
     */
    use CSVServiceAwareTrait;

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Application\Exceptions\ImportException
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function importFromCSV($fileData): array
    {

        $this->getCsvService()->setHeaders(CategorieStageCsvImportValidator::getImportHeader());
        $this->csvService->readCSVFile($fileData);
        $data = $this->getCsvService()->getData();
        $categories = [];
        foreach ($data as $rowData) {
            $categorie = $this->getCategorieFromData($rowData);
            $this->getObjectManager()->persist($categorie);
            $categories[] = $categorie;
        }
        $this->getObjectManager()->flush();
        return $categories;
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    private function getCategorieFromData(mixed $rowData) : CategorieStage
    {
        $code = trim($this->getCsvService()->readDataAt(CategorieStageCsvImportValidator::HEADER_CODE_CATEGORIE, $rowData, ""));
        $acronyme = trim($this->getCsvService()->readDataAt(CategorieStageCsvImportValidator::HEADER_ACRONYME, $rowData, ""));
        $libelle = trim($this->getCsvService()->readDataAt(CategorieStageCsvImportValidator::HEADER_LIBELLE, $rowData, ""));
        $ordre = CSVService::textToInt($this->getCsvService()->readDataAt(CategorieStageCsvImportValidator::HEADER_ORDRE, $rowData, 0));
        $principale = CSVService::yesNoValueToBoolean($this->getCsvService()->readDataAt(CategorieStageCsvImportValidator::HEADER_PRINCIPAL, $rowData, false));

        /** @var CategorieStage $categorie */
        $categorie = $this->getObjectManager()->getRepository(CategorieStage::class)->findOneBy(['code' => $code]);
        if (!$categorie) {
            $categorie = new CategorieStage();
            $categorie->setCode($code);
        }
        $categorie->setAcronyme($acronyme);
        $categorie->setLibelle($libelle);
        $categorie->setOrdre($ordre);
        $categorie->setCategoriePrincipale($principale);
        return $categorie;
    }

}