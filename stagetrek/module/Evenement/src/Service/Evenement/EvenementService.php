<?php

namespace Evenement\Service\Evenement;

use DateInterval;
use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use UnicaenApp\Exception\RuntimeException;
use UnicaenEvenement\Entity\Db\Evenement;

class EvenementService extends \UnicaenEvenement\Service\Evenement\EvenementService
{
    /**
     * Affichage des événements
     *
     * @param int $maxResults
     * @param array $filter
     * @return array|array[]
     */
    public function display(int $maxResults = 10000, array $filter = [])
    {
        $sql = <<<EOS
with event as (
	SELECT e.id as id
		 , e.nom as nom
		 , e.description as description
		 , e.date_planification as planification_date
		 , TO_CHAR( e.date_creation, 'DD/MM/YYYY' ) || ' à ' || TO_CHAR( e.date_creation, 'HH24:MI:SS' ) as date_creation
		 , TO_CHAR( e.date_planification, 'DD/MM/YYYY' ) || ' à ' || TO_CHAR( e.date_planification, 'HH24:MI:SS' ) as date_planification
		 , TO_CHAR( e.date_traitement, 'DD/MM/YYYY' ) || ' à ' || TO_CHAR( e.date_traitement, 'HH24:MI:SS' ) as date_traitement
		 , t.code as type_code
		 , t.libelle as type_libelle
		 , etat.code as etat_code
		 , etat.libelle as etat_libelle
		 , e.parent_id as parent_id
		 , case when e.parent_id is not null then 2
		                                     else 1
		   end as niveau
		 , case when e.parent_id is not null then  e.parent_id
		                                     else e.id
		   end as racine_id
	FROM unicaen_evenement_instance e
		     join unicaen_evenement_type t on t.id = e.type_id
		     join unicaen_evenement_etat etat on etat.id = e.etat_id
		FETCH FIRST $maxResults ROWS ONLY
)
SELECT * from event
ORDER BY racine_id DESC, niveau, planification_date desc, id
EOS;
        $result = $this->getObjectManager()->getConnection()->executeQuery($sql);
//        var_dump($result->);
//        die();
        return $result->fetchAllAssociative();
    }

    /**
     * @param array $filtres
     * @return Evenement[]
     */
    public function getEvenementsWithFiltre(array $filtres = []) : array
    {
        $qb = $this->createQueryBuilder();

        if (isset($filtres['etat']) AND $filtres['etat'] !== '') {
            $qb = $qb->andWhere('etat.code = :etat')->setParameter('etat', $filtres['etat']);
        }
        if (isset($filtres['date']) AND $filtres['date'] !== '') {
            try {
                $date = (new DateTime())->sub(new DateInterval('P' . $filtres['date']));
            } catch (Exception $e) {
                throw new RuntimeException("Problème de calcul de la date buttoir avec [".$filtres['date']."]",0,$e);
            }
            $qb = $qb->andWhere('evenement.datePlanification >= :date')->setParameter('date',$date);
        }
        if (isset($filtres['type']) AND $filtres['type'] !== '') {
            $qb = $qb->andWhere('type.id = :type')->setParameter('type', $filtres['type']);
        }
        $qb->orderBy('evenement.datePlanification', 'desc');
        $qb->setFirstResult(0);
        $qb->setMaxResults(2000);
        $paginator = new Paginator($qb, fetchJoinCollection: true);
        $c = count($paginator);
        $result=[];
        foreach ($paginator as $r) {
            $result[] = $r;
        }
        return $result;
    }
}