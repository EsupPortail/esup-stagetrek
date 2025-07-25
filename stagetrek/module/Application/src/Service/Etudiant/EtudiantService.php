<?php

namespace Application\Service\Etudiant;

use Application\Entity\Db\Adresse;
use Application\Entity\Db\AdresseType;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\Stage;
use Application\Form\Etudiant\EtudiantRechercheForm as FormRecherche;
use Application\Provider\EtatType\ContrainteCursusEtudiantEtatTypeProvider;
use Application\Provider\EtatType\EtudiantEtatTypeProvider;
use Application\Provider\Roles\RolesProvider;
use Application\Service\Misc\CommonEntityService;
use Application\Service\Misc\Traits\CSVServiceAwareTrait;
use Application\Service\Misc\Traits\EntityEtatServiceAwareTrait;
use DateTime;
use Exception;
use RuntimeException;
use UnicaenEtat\Entity\Db\HasEtatsInterface;
use UnicaenUtilisateur\Entity\Db\RoleInterface;
use UnicaenUtilisateur\Service\Role\RoleServiceAwareTrait;
use UnicaenUtilisateur\Service\User\UserServiceAwareTrait;

/** TODO : splitter l'étudiant service pour avoir un service d'import en CSV, un service d'import depuis un référentiel ... */


class EtudiantService extends CommonEntityService
{
    protected int $importResult_nbEtudiantUpdated = 0;

    //Recherche d'un étudiant selon certain critére
    protected int $importResult_nbEtudiantCreated = 0;

    //Pour la création de l'étudiant
    use UserServiceAwareTrait;
    use RoleServiceAwareTrait;
    protected int $importResult_nbUserCreated = 0;

    use CSVServiceAwareTrait;

    /** @return string */
    public function getEntityClass(): string
    {
        return Etudiant::class;
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\DBAL\Exception
     */
    public function search(array $criteria): array
    {
        $qb = $this->getObjectRepository()->createQueryBuilder($alias = 'e');
        if (isset($criteria[FormRecherche::INPUT_ANNEE]) || isset($criteria[FormRecherche::INPUT_GROUPE])) {
            $qb->leftJoin("$alias.groupes", 'g');
            $qb->leftJoin("g.anneeUniversitaire", 'a');
        }
        if (!empty($criteria[FormRecherche::INPUT_NOM])) {
            $qb->andWhere($qb->expr()->like($qb->expr()->upper("$alias.nom"), $qb->expr()->upper(':nom')));
            $qb->setParameter('nom', "{$criteria[FormRecherche::INPUT_NOM]}%");
        }
        if (!empty($criteria[FormRecherche::INPUT_PRENOM])) {
            $qb->andWhere($qb->expr()->like($qb->expr()->upper("$alias.prenom"), $qb->expr()->upper(':prenom')));
            $qb->setParameter('prenom', "{$criteria[FormRecherche::INPUT_PRENOM]}%");
        }
        if (!empty($criteria[FormRecherche::INPUT_NUM_ETU])) {
            $qb->andWhere($qb->expr()->like($qb->expr()->upper("$alias.numEtu"), $qb->expr()->upper(':numEtu')));
            $qb->setParameter('numEtu', "{$criteria[FormRecherche::INPUT_NUM_ETU]}%");
        }
        if (isset($criteria[FormRecherche::INPUT_ANNEE])) {
            $where = $qb->expr()->in("a.id", ":annee");
            if (in_array(-1, $criteria[FormRecherche::INPUT_ANNEE])) {
                $where .= ' or ' . $qb->expr()->isNull("a.id");
            }
            $qb->andWhere($where);
            $qb->setParameter('annee', $criteria[FormRecherche::INPUT_ANNEE]);
        }
        if (isset($criteria[FormRecherche::INPUT_GROUPE])) {
            $where = $qb->expr()->in("g.id", ":groupe");
            if (in_array(-1, $criteria[FormRecherche::INPUT_GROUPE])) {
                $where .= ' or ' . $qb->expr()->isNull("g.id");
            }
            $qb->andWhere($where);
            $qb->setParameter('groupe', $criteria[FormRecherche::INPUT_GROUPE]);
        }
        if (!empty($criteria[FormRecherche::INPUT_ETAT])) {
            $qb = Etudiant::decorateWithEtats($qb, $alias, $criteria['etat']);
        }
        return $qb->getQuery()->getResult();
    }

    /** @return RoleInterface */
    public function getEtudiantRole(): RoleInterface
    {
        return $this->getRoleService()->findByRoleId(RolesProvider::ETUDIANT);
    }

    /**
     * Ajoute une entité
     *
     * @param mixed $entity
     * @param string|null $serviceEntityClass classe de l'entité
     * @return mixed
     */
    public function add(mixed $entity, string $serviceEntityClass = null): Etudiant
    {
        /** @var Etudiant $etudiant */
        $etudiant = $entity;
        if (!$etudiant->hasAdresse()) {
            $adresse = new Adresse();
            $etudiant->setAdresse($adresse);
        }
        $adresse = $etudiant->getAdresse();
        if (!$adresse->isType(AdresseType::TYPE_ETUDIANT)) {
            $adresseType = $this->getObjectManager()->getRepository(AdresseType::class)->findOneBy(['code' => AdresseType::TYPE_ETUDIANT]);
            $adresse->setAdresseType($adresseType);
        }
        $this->getObjectManager()->persist($etudiant);
        $this->getObjectManager()->flush();
        $this->updateEtat($etudiant);
        return $etudiant;
    }

    /**
     * @param array $entities
     * @param string|null $serviceEntityClass
     * @return mixed
     */
    public function addMultiple(array $entities = [], string $serviceEntityClass = null): mixed
    {
        throw new RuntimeException("La création des étudiants doit passer par la fonction createEtudiant");
    }

    /**
     * @param mixed $entity
     * @param string|null $serviceEntityClass classe de l'entité
     * @return \Application\Service\Etudiant\EtudiantService
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(mixed $entity, string $serviceEntityClass = null): static
    {
        if (!isset($entity)) {
            throw new RuntimeException("Aucun étudiant à supprimer n'as été transmis.");
        }
        $groupes = $entity->getGroupes();
        $sessions = $entity->getSessionsStages();
        $stages = $entity->getStages();
        if (!$groupes->isEmpty() || !$sessions->isEmpty() || !$stages->isEmpty()) {
            throw new RuntimeException("Impossible de supprimer un étudiant ayant un stage ou inscrit dans un groupe");
        }
        $this->getObjectManager()->remove($entity);
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush();
        }
        return $this;
    }

    use EntityEtatServiceAwareTrait;
    protected function computeEtat(HasEtatsInterface $entity): string
    {
        if (!$entity instanceof Etudiant) {
            throw new Exception("L'entité fournise n'est pas un étudiant");
        }
        $etudiant = $entity;
        $today = new DateTime();

        $stages = $etudiant->getStages()->toArray();

        if($etudiant->hasCursusTermine() && $etudiant->hasCursusValide()){
            return EtudiantEtatTypeProvider::CURSUS_VAlIDE;
        }
        if($etudiant->hasCursusTermine() && !$etudiant->hasCursusValide()){
            return EtudiantEtatTypeProvider::CURSUS_INVALIDE;
        }

        // L'étudiant est en dispo
        if($etudiant->isEnDispo()){
            return EtudiantEtatTypeProvider::EN_DISPO;
        }

        //L'étudiant a 0 stages = en construction
        if(empty($stages)) {
            $this->setEtatInfo("L'étudiant n'a actuellement aucun stage.");
            return EtudiantEtatTypeProvider::CURSUS_EN_CONSTRUCTION;
        }

        /** @var \Application\Entity\Db\ContrainteCursusEtudiant $c */
        $hasAlerte = false;
        foreach ($etudiant->getContraintesCursusEtudiants() as $c){
//            Possiblement requis car les stages peuvent eux même avoir changé d'état entre temps
            $this->getObjectManager()->refresh($c);
            if($c->isEtatActif(ContrainteCursusEtudiantEtatTypeProvider::EN_ERREUR)){
                $this->setEtatInfo("Au moins une contrainte du cursus est en erreur");
                return EtudiantEtatTypeProvider::EN_ERREUR;
            }
            if($c->isEtatActif(ContrainteCursusEtudiantEtatTypeProvider::INSAT)){
                $msg = sprintf("La contrainte de cursus %s n'est pas satisfiable", $c->getLibelle());
                $this->addEtatInfo($msg);
                $hasAlerte = true;
            }
            if($c->hasEtatInsat()){
                $msg = sprintf("La contrainte de cursus %s est instatifiable", $c->getLibelle());
                $this->addEtatInfo($msg);
                $hasAlerte = true;
            }
        }
        if($hasAlerte){
            return EtudiantEtatTypeProvider::EN_AlERTE;
        }

        //L'étudiant a des stages en erreurs => erreur
        //Un stage non validé après la date : cas d'alerte

        /** @var Stage $stage */
        foreach ($stages as $stage) {
//            Possiblement requis car les stages peuvent eux même avoir changé d'état entre temps
            $this->getObjectManager()->refresh($stage);
            if($stage->hasEtatEnErreur()){
                $msg = sprintf("Le stage %sn°%s est en erreur.", ($stage->isStageSecondaire()) ? "secondaire " : "", $stage->getNumero());
                $this->setEtatInfo($msg);
                return EtudiantEtatTypeProvider::EN_ERREUR;}
            if($stage->hasEtatEnAlerte()
            ){
                $msg = sprintf("Le stage %sn°%s est en alerte.", ($stage->isStageSecondaire()) ? "secondaire " : "", $stage->getNumero());
                $this->addEtatInfo($msg);
                $hasAlerte = true;
            }
            if($stage->hasEtatValidationEnRetard()){
                $msg= sprintf("Le stage %sn°%s n'a pas été évalué par le responsable.", ($stage->isStageSecondaire()) ? "secondaire " : "", $stage->getNumero());
                $this->addEtatInfo($msg);
                $hasAlerte = true;
            }
            if($stage->hasEtatEvaluationEnRetard()){
                $msg= sprintf("Le stage %sn°%s n'a pas été évalué par l'étudiant.", ($stage->isStageSecondaire()) ? "secondaire " : "", $stage->getNumero());
                $this->addEtatInfo($msg);
                $hasAlerte = true;
            }
            $validation = $stage->getValidationStage();
            if( $stage->getDateFinValidation() <= $today &&
                $stage->hasEtatValide() && $validation->getWarning()){
                $msg= sprintf("Le stage %sn°%s a été validé avec une alerte.", ($stage->isStageSecondaire()) ? "secondaire " : "", $stage->getNumero());
                $this->addEtatInfo($msg);
                $hasAlerte = true;
            }
            elseif( $stage->getDateFinValidation() <= $today &&
                $stage->hasEtatNonValide() && $validation->getWarning()){
                $msg= sprintf("Le stage %sn°%s a été validé avec une alerte.", ($stage->isStageSecondaire()) ? "secondaire " : "", $stage->getNumero());
                $this->addEtatInfo($msg);
                $hasAlerte = true;
            }
        }
        if($hasAlerte){
            return EtudiantEtatTypeProvider::EN_AlERTE;
        }
        return EtudiantEtatTypeProvider::CURSUS_EN_COURS;

    }

}