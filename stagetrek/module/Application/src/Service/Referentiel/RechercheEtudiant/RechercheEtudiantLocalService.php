<?php


namespace Application\Service\Referentiel\RechercheEtudiant;

use Application\Entity\Db\Etudiant;
use Application\Service\Referentiel\RechercheEtudiant\Entity\LocalEtudiantResult;
use Application\Service\Referentiel\RechercheEtudiant\Interfaces\RechercheEtudiantServiceInterface;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;

class RechercheEtudiantLocalService implements RechercheEtudiantServiceInterface, ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    /**
     * @param string $name
     * @param int $limit
     * @return RechercheEtudiantServiceInterface[]
     */
    public function findEtudiantsByName(string $name, int $limit=-1) : array
    {
        $qb = $this->getObjectManager()->getRepository(Etudiant::class)->createQueryBuilder('e');
        $qb
            ->where('LOWER(CONCAT(e.nom,\' \', e.prenom)) LIKE :name')
            ->setParameter("name", trim(strtolower($name) . '%'))
            ->orderBy("e.nom, e.prenom");
        if($limit > -1){
            $qb->setMaxResults($limit);
        }
        $etudiants = $qb->getQuery()->getResult();
        $results = [];
        foreach ($etudiants as $etudiant) {
            $results[] = new LocalEtudiantResult($etudiant);
        }

        return $results;
    }

    /**
     * @param string|int $numero
     * @param int $limit
     * @return RechercheEtudiantServiceInterface[]
     */

    public function findEtudiantsByNumero(int|string $numero, int $limit=-1) : array
    {
        $numero = trim(strval($numero));
        $qb = $this->getObjectManager()->getRepository(Etudiant::class)->createQueryBuilder('e');
        $qb->andWhere(
            $qb->expr()->like("CONCAT(e.numEtu,'')", ":numero")
        )
        ->setParameter("numero", $numero . '%')
        ->orderBy("e.nom, e.prenom");
        if($limit > -1){
            $qb->setMaxResults($limit);
        }
        $etudiants = $qb->getQuery()->getResult();
        $results = [];
        foreach ($etudiants as $etudiant) {
            $results[] = new LocalEtudiantResult($etudiant);
        }
        return $results;
    }

    /**
     * @param string $codePromo
     * @param int $limit
     * @return RechercheEtudiantServiceInterface[]
     */
    public function findEtudiantsByPromo(string $codePromo, int $limit=-1) : array
    {
        return [];
//        $id = (int)($codePromo);//Force le codePromo en temps que int (si impossible, se sera 0, se qui signifie que la promo n'existe pas
//        /** @var Groupe $groupe */
//        $groupe = $this->getObjectManager()->getRepository(Groupe::class)->find($id);
//        if (!$groupe) return [];
//        /** @var AnnuaireEtudiantResultInterface[] $res */
//        $res = [];
//        foreach ($groupe->getEtudiants() as $etu) {
//            $res[] = new LocalEtudiantResult($etu);
//        }
//        return $res;
    }
}
