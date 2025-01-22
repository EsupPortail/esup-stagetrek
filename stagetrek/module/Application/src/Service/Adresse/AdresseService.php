<?php

namespace Application\Service\Adresse;

use Application\Entity\Db\Adresse;
use Application\Entity\Db\AdresseType;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\TerrainStage;
use Application\Entity\Interfaces\HasAdresseInterface;
use Application\Service\Misc\CommonEntityService;

class AdresseService extends CommonEntityService
{

    public function getEntityClass() : string
    {
        return Adresse::class;
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function findByType(AdresseType|string $type) : array
    {
        $code = ($type instanceof AdresseType) ? $type->getCode() : $type;
        $qb = $this->getObjectRepository()->createQueryBuilder($alias = 'a');
        $qb->join("$alias.adresseType", 't');
        $qb->andWhere(
            $qb->expr()->eq("t.code", ":code"),
        );
        $qb->setParameter('code', $code);
        return $qb->getQuery()->getResult();
    }

    /**
     * Todo : a revoir, fonction qui créer un objet adresse par défaut avec le bon type
     * FOnction probablement a supprimer
     * @param HasAdresseInterface $entity
     * @return HasAdresseInterface
     */
    public function checkEntityAdresse(HasAdresseInterface $entity): HasAdresseInterface
    {
        $adresse = $entity->getAdresse();
        if(!isset($adresse)){
            $adresse = new Adresse();
        }
        $typeCode = AdresseType::TYPE_INCONNU;
        switch(true) {
            case $entity instanceof Etudiant:
                $typeCode = AdresseType::TYPE_ETUDIANT;
            break;
            case $entity instanceof TerrainStage:
                $typeCode = AdresseType::TYPE_TERRAIN_STAGE;
            break;
        }
        if(!$adresse->isType($typeCode)){
            $type = $this->getObjectManager()->getRepository(AdresseType::class)->findOneBy(['code' => $typeCode]);
            $adresse->setAdresseType($type);
        }
        $entity->setAdresse($adresse);
        if($this->hasUnitOfWorksChange()){
            $this->getObjectManager()->flush($entity);
        }
        return $entity;
    }
}