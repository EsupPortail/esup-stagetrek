<?php

namespace Fichier\Service\Nature;

use Doctrine\ORM\NonUniqueResultException;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Fichier\Entity\Db\Nature;
use UnicaenApp\Exception\RuntimeException;

class NatureService
    implements ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    public function getNaturesAsOptions() : array
    {
        $qb = $this->getObjectManager()->getRepository(Nature::class)->createQueryBuilder('nature')
            ->orderBy('nature.id');

        $result = $qb->getQuery()->getResult();

        $options = [];
        $options[null] = "Sélectionner une nature de fichier ...";
        /** @var Nature $item */
        foreach ($result as $item) {
            $options[$item->getId()] = $item->getLibelle();
        }
        return $options;
    }

    /**
     * @param integer $id
     * @return Nature|null
     */
    public function getNature(int $id) : ?Nature
    {
        $qb = $this->getObjectManager()->getRepository(Nature::class)->createQueryBuilder('nature')
            ->andWhere('nature.id = :id')
            ->setParameter('id', $id)
        ;
        try {
            $result = $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            throw new RuntimeException("Plusieurs Nature partagent le même identifiant [".$id."]", $e);
        }
        return $result;
    }

    /**
     * @param string $code
     * @return Nature|null
     */
    public function getNatureByCode(string $code) : ?Nature
    {
        $qb = $this->getObjectManager()->getRepository(Nature::class)->createQueryBuilder('nature')
            ->andWhere('nature.code = :code')
            ->setParameter('code', $code)
        ;
        try {
            $result = $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            throw new RuntimeException("Plusieurs Nature partagent le même code [".$code."]", $e);
        }
        return $result;
    }

    /** FACADE ******************************************************************************************/

    /**
     * @param string $libelle
     * @param string|null $description
     * @return Nature
     */
    public function addNature(string $libelle, ?string $description) : Nature
    {
        $code = strtoupper($libelle);
        $nature = $this->getNatureByCode($code);

        if($nature === null) {
            $nature = new Nature();
            $nature->setCode($code);
            $nature->setLibelle($libelle);
            $nature->setDescription($description);
            $this->getObjectManager()->persist($nature);
            $this->getObjectManager()->flush($nature);
        }
        return $nature;
    }
}