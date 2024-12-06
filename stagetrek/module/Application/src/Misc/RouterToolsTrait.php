<?php


namespace Application\Misc;

use Application\Entity\Db\AffectationStage;
use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\CategorieStage;
use Application\Entity\Db\Contact;
use Application\Entity\Db\ContactStage;
use Application\Entity\Db\ContactTerrain;
use Application\Entity\Db\ContrainteCursus;
use Application\Entity\Db\ContrainteCursusEtudiant;
use Application\Entity\Db\ContrainteCursusPortee;
use Application\Entity\Db\ConventionStage;
use Application\Entity\Db\Disponibilite;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\Faq;
use Application\Entity\Db\FaqCategorieQuestion;
use Application\Entity\Db\Groupe;
use Application\Entity\Db\MessageInfo;
use Application\Entity\Db\ModeleConventionStage;
use Application\Entity\Db\NiveauEtude;
use Application\Entity\Db\Parametre;
use Application\Entity\Db\ParametreCategorie;
use Application\Entity\Db\ParametreCoutAffectation;
use Application\Entity\Db\ParametreTerrainCoutAffectationFixe;
use Application\Entity\Db\ParametreType;
use Application\Entity\Db\Preference;
use Application\Entity\Db\ProcedureAffectation;
use Application\Entity\Db\ReferentielPromo;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Source;
use Application\Entity\Db\Stage;
use Application\Entity\Db\TerrainStage;
use Application\Entity\Db\ValidationStage;
use Application\Provider\Roles\RolesProvider;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Mvc\Controller\Plugin\Params;
use Laminas\Permissions\Acl\Role\RoleInterface;
use UnicaenMail\Entity\Db\Mail;
use UnicaenRenderer\Entity\Db\Macro;

/**
 * @method Params|mixed params(string $param = null, mixed $default = null)
 */
trait RouterToolsTrait
{
    /** Récupération des entités depuis la route */
    use ProvidesObjectManager;

    /**
     * @param string $name nom du paramètre
     * @param mixed|null $default valeur par défaut
     * @return ?string
     */
    protected function getParam(string $name, mixed $default=null) : ?string
    {
        return $this->params()->fromRoute($name, $default);
    }

    protected function getEntityFromRoute(string $entityClass, string $param, mixed $default=null) : mixed
    {
        $id = $this->getParam($param);
        if (!isset($id)) {return $default;}
        return $this->getObjectManager()->getRepository($entityClass)->find($id);
    }

    protected function getEntitiesFromPost(string $entityClass, string $key = 'ids') : array
    {
        $data = $this->params()->fromPost();
        if(!isset($data[$key])){ return [];}
        $entities=[];
        foreach ($data[$key] as $id){
            $entity = $this->getObjectManager()->getRepository($entityClass)->find($id);
            if(isset($entity)){
                $entities[$entity->getId()] = $entity;
            }
        }
        return $entities;
    }

    /** **************************************************** */

    /** Fonction pour acceder aux entités */
    public function getAffectationStageFromRoute(?string $param='affectationStage', mixed $default=null): ?AffectationStage
    {
        return $this->getEntityFromRoute(AffectationStage::class, $param, $default);
    }
    public function getProcedureAffectationFromRoute(?string $param='procedureAffectation', mixed $default=null): ?ProcedureAffectation
    {
        return $this->getEntityFromRoute(ProcedureAffectation::class, $param, $default);
    }

    public function getAnneeUniversitaireFromRoute(?string $param='anneeUniversitaire', mixed $default=null): ?AnneeUniversitaire
    {
        return $this->getEntityFromRoute(AnneeUniversitaire::class, $param, $default);
    }

    public function getContactFromRoute(?string $param='contact', mixed $default=null): ?Contact
    {
        return $this->getEntityFromRoute(Contact::class, $param, $default);
    }

    public function getContactStageFromRoute(?string $param='contactStage', mixed $default=null): ?ContactStage
    {
        return $this->getEntityFromRoute(ContactStage::class, $param, $default);
    }


    public function getContactTerrainFromRoute(?string $param='contactTerrain', mixed $default=null): ?ContactTerrain
    {
        return $this->getEntityFromRoute(ContactTerrain::class, $param, $default);
    }

    public function getContrainteCursusEtudiantFromRoute(?string $param='contrainteCursusEtudiant', mixed $default=null): ?ContrainteCursusEtudiant
    {
        return $this->getEntityFromRoute(ContrainteCursusEtudiant::class, $param, $default);
    }

    public function getContrainteCursusFromRoute(?string $param='contrainteCursus', mixed $default=null): ?ContrainteCursus
    {
        return $this->getEntityFromRoute(ContrainteCursus::class, $param, $default);
    }

    public function getContrainteCursusPorteeFromRoute(?string $param='contrainteCursusPortee', mixed $default=null): ?ContrainteCursusPortee
    {
        return $this->getEntityFromRoute(ContrainteCursusPortee::class, $param, $default);
    }

    public function getConventionStageFromRoute(?string $param='conventionStage', mixed $default=null): ?ConventionStage
    {
        return $this->getEntityFromRoute(ConventionStage::class, $param, $default);
    }

    public function getModeleConventionStageFromRoute(?string $param='modeleConventionStage', mixed $default=null): ?ModeleConventionStage
    {
        return $this->getEntityFromRoute(ModeleConventionStage::class, $param, $default);
    }

    public function getDisponibiliteFromRoute(?string $param='disponibilite', mixed $default=null): ?Disponibilite
    {
        return $this->getEntityFromRoute(Disponibilite::class, $param, $default);
    }

    public function getEtudiantFromRoute(?string $param='etudiant', mixed $default=null): ?Etudiant
    {
        return $this->getEntityFromRoute(Etudiant::class, $param, $default);
    }

    protected function getEtudiantsFromPost(?string $params='etudiants') : array
    {
        return $this->getEntitiesFromPost(Etudiant::class, $params);
    }

    protected function getEtudiantFromUser(): ?Etudiant
    {
        $utilisateur = $this->getUser();
        return $this->getObjectManager()->getRepository(Etudiant::class)->findOneBy(["user" => $utilisateur]);
    }

    public function getGroupeFromRoute(?string $param='groupe', mixed $default=null): ?Groupe
    {
        return $this->getEntityFromRoute(Groupe::class, $param, $default);
    }

    public function getMacroFromRoute(?string $param='macro', mixed $default=null): ?Macro
    {
        return $this->getEntityFromRoute(Macro::class, $param, $default);
    }

    public function getMailFromRoute(?string $param='mail', mixed $default=null): ?Mail
    {
        return $this->getEntityFromRoute(Mail::class, $param, $default);
    }

    public function getNiveauEtudeFromRoute(?string $param='niveauEtude', mixed $default=null): ?NiveauEtude
    {
        return $this->getEntityFromRoute(NiveauEtude::class, $param, $default);
    }

    public function getParametreFromRoute(?string $param='parametre', mixed $default=null): ?Parametre
    {
        return $this->getEntityFromRoute(Parametre::class, $param, $default);
    }

    public function getParametreCategorieFromRoute(?string $param='parametreCategorie', mixed $default=null): ?ParametreCategorie
    {
        return $this->getEntityFromRoute(ParametreCategorie::class, $param, $default);
    }

    public function getParametreTypeFromRoute(?string $param='parametreType', mixed $default=null): ?ParametreType
    {
        return $this->getEntityFromRoute(ParametreType::class, $param, $default);
    }

    public function getParametreCoutAffectationFromRoute(?string $param='parametreCoutAffectation', mixed $default=null): ?ParametreCoutAffectation
    {
        return $this->getEntityFromRoute(ParametreCoutAffectation::class, $param, $default);
    }

    public function getParametreTerrainCoutAffectationFixeFromRoute(?string $param='parametreTerrainCoutAffectationFixe', mixed $default=null): ?ParametreTerrainCoutAffectationFixe
    {
        return $this->getEntityFromRoute(ParametreTerrainCoutAffectationFixe::class, $param, $default);
    }

    public function getPreferenceFromRoute(?string $param='preference', mixed $default=null): ?Preference
    {
        return $this->getEntityFromRoute(Preference::class, $param, $default);
    }

    public function getReferentielPromoFromRoute(?string $param='referentielPromo', mixed $default=null): ?ReferentielPromo
    {
        return $this->getEntityFromRoute(ReferentielPromo::class, $param, $default);
    }

    public function getSessionStageFromRoute(?string $param='sessionStage', mixed $default=null): ?SessionStage
    {
        return $this->getEntityFromRoute(SessionStage::class, $param, $default);
    }

    public function getSourceFromRoute(?string $param='source', mixed $default=null): ?Source
    {
        return $this->getEntityFromRoute(Source::class, $param, $default);
    }

    public function getStageFromRoute(?string $param='stage', mixed $default=null): ?Stage
    {
        return $this->getEntityFromRoute(Stage::class, $param, $default);
    }

    protected function getStagesFromPost(?string $params='stages') : array
    {
        return $this->getEntitiesFromPost(Stage::class, $params);
    }

    public function getValidationStageFromRoute(?string $param='validationStage', mixed $default=null): ?ValidationStage
    {
        return $this->getEntityFromRoute(ValidationStage::class, $param, $default);
    }

    public function getTerrainStageFromRoute(?string $param='terrainStage', mixed $default=null): ?TerrainStage
    {
        return $this->getEntityFromRoute(TerrainStage::class, $param, $default);
    }

    public function getCategorieStageFromRoute(?string $param='categorieStage', mixed $default=null): ?CategorieStage
    {
        return $this->getEntityFromRoute(CategorieStage::class, $param, $default);
    }

    public function getFaqCategorieQuestionFromRoute(?string $param='faqCategorieQuestion', mixed $default=null): ?FaqCategorieQuestion
    {
        return $this->getEntityFromRoute(FaqCategorieQuestion::class, $param, $default);
    }

    public function getFaqQuestionFromRoute(?string $param='faq', mixed $default=null): ?Faq
    {
        return $this->getEntityFromRoute(Faq::class, $param, $default);
    }

    public function getMessageInfoFromRoute(?string $param='messageInfo', mixed $default=null): ?MessageInfo
    {
        return $this->getEntityFromRoute(MessageInfo::class, $param, $default);
    }

    /**
     * @return RoleInterface
     */
    public function getCurrentRole() : ?RoleInterface
    {
        $user = $this->getUser();
        return ($user) ? $user->getLastRole() : null;
    }


    /**
     * @return RoleInterface
     */
    public function currentRoleIsStudent() : bool
    {
        $role = $this->getCurrentRole();
        return (isset($role) && $role->getRoleId() == RolesProvider::ROLE_ETUDIANT);
    }

}