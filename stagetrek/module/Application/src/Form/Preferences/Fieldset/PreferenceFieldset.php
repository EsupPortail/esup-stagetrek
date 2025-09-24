<?php


namespace Application\Form\Preferences\Fieldset;

use Application\Entity\Db\ContrainteCursusEtudiant;
use Application\Entity\Db\Parametre;
use Application\Entity\Db\Preference;
use Application\Entity\Db\TerrainStage;
use Application\Entity\Traits\Preference\HasPreferenceTrait;
use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\Preferences\Validator\PreferenceValidator;
use Application\Form\TerrainStage\Element\TerrainStagePrincipalSelectPicker;
use Application\Form\TerrainStage\Element\TerrainStageSecondaireSelectPicker;
use Application\Form\TerrainStage\Element\TerrainStageSelectPicker;
use Application\Service\Parametre\Traits\ParametreServiceAwareTrait;
use Application\Service\Preference\Traits\PreferenceServiceAwareTrait;
use DateTime;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Number;

/**
 * Class PreferenceFieldset
 * @package Application\Form\Fieldset
 */
class PreferenceFieldset extends AbstractEntityFieldset
{
    use ParametreServiceAwareTrait;
    use HasPreferenceTrait;
    use PreferenceServiceAwareTrait;

    use IdInputAwareTrait;

    /** Id/Name des inputs */
    const ID = "id";
    const STAGE = "stage";
    const TERRAIN_STAGE = "terrainStage";
    const TERRAIN_STAGE_SECONDAIRE = "terrainStageSecondaire";
    const RANG = "rang";


    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function init() : static
    {
        $this->initIdInput();
        $this->initStageInput();
        $this->initTerrainPrincipalInput();
        $this->initTerrainSecondaireInput();
        $this->initRangInput();
        return $this;
    }

    private function initStageInput() : void
    {
        $this->add([
            'name' => self::STAGE,
            'type' => Hidden::class,
            'attributes' => [
                'id' => self::STAGE,
            ],
        ]);
        $this->setInputfilterSpecification(self::STAGE, [
            "name" => self::STAGE,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);
    }

    private function initTerrainPrincipalInput() : void
    {
        $this->add([
            'type' => TerrainStagePrincipalSelectPicker::class,
            'name' => self::TERRAIN_STAGE,
            'options' => [
                'label' => "Terrain de stage",
                'empty_option' => "Selectionnez un terrain de stage",
            ],
            'attributes' => [
                'id' => self::TERRAIN_STAGE,
            ],
        ]);
        $this->setInputfilterSpecification(self::TERRAIN_STAGE, [
            "name" => self::TERRAIN_STAGE,
            'required' => true,
            'validators' => [
                [
                    'name' => PreferenceValidator::class,
                    'options' => [
                        'callback' => PreferenceValidator::ASSERT_PREFERENCE_TERRAIN_NOT_EXIST,
                        'break_chain_on_failure' => false,
                    ],
                ],
            ],
        ]);
    }

    private function initTerrainSecondaireInput() : void
    {
        $this->add([
            'type' => TerrainStageSecondaireSelectPicker::class,
            'name' => self::TERRAIN_STAGE_SECONDAIRE,
            'options' => [
                'label' => "Terrain de stage secondaire",
                'empty_option' => "Non",
            ],
            'attributes' => [
                'id' => self::TERRAIN_STAGE_SECONDAIRE,
            ],
        ]);

        $this->setInputfilterSpecification(self::TERRAIN_STAGE_SECONDAIRE, [
            "name" => self::TERRAIN_STAGE_SECONDAIRE,
            'required' => false,
            'validators' => [
                [
                    'name' => PreferenceValidator::class,
                    'options' => [
                        'callback' => PreferenceValidator::ASSERT_TERRAIN_SECONDAIRE_ALLOWED,
                        'break_chain_on_failure' => false,
                    ],
                ],
            ],
        ]);
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    private function initRangInput() : void
    {
        $rangMax = $this->getParametreService()->getParametreValue(Parametre::NB_PREFERENCES);
        $this->add([
            'name' => self::RANG,
            'type' => Number::class,
            'options' => [
                'label' => "Rang",
            ],
            'attributes' => [
                'min' => 1,
                'max' => $rangMax,
                'step' => 1,
                'id' => self::RANG
            ],
        ]);
        $this->setInputfilterSpecification(self::RANG, [
            "name" => self::RANG,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);
    }


    /** @var bool $modeAdmin */
    protected bool $modeAdmin = false;
    public function setModeAdmin(bool $modeAdmin=true): void
    {
        $this->modeAdmin = $modeAdmin;
    }

    public function getModeAdmin(): bool
    {
        return $this->modeAdmin;
    }


    /**
     * @param Preference $preference
     */
    public function setPreference(Preference $preference): void
    {
        $this->preference = $preference;
        $stage = $preference->getStage();

        //Si la date de début du stage est dépassé et que l'on est en mode admin, on ignore la borne max afin de permettre de corriger des cas ou le paramètres était différents
        if($this->modeAdmin && $stage->getDateDebutStage() < new DateTime()){
            /** @var Number $input */
            $inputRang = $this->get(self::RANG);
            $inputRang->setAttribute('max', null);
        }
        /** @var TerrainStage[] $terrains */
        $terrains = $this->getObjectManager()->getRepository(TerrainStage::class)->findAll();
        foreach ($terrains as $terrain){
            $this->updateSelectTerrain($terrain);
        }

    }

    protected function updateSelectTerrain(TerrainStage $terrain): static
    {
        $preference = $this->preference;
        if (!$preference) return $this; //Au cas ou
        $stage = $preference->getStage();
        $session = $stage->getSessionStage();
        $affectation = $stage->getAffectationStage();
        $niveauEtude = $stage->getNiveauEtude();

        $terrainPrincipal = $terrain->isTerrainPrincipal();

        /** @var TerrainStageSelectPicker $input */
        $input = $this->get(self::TERRAIN_STAGE);
        if(!$terrainPrincipal){
            $input = $this->get(self::TERRAIN_STAGE_SECONDAIRE);
        }
        $contraintesCursusEtudiants = $preference->getEtudiant()->getContraintesCursusEtudiants()->toArray();
        $contraintesCursusEtudiants = array_filter($contraintesCursusEtudiants,
        function (ContrainteCursusEtudiant $contraite) use ($terrain)
        {
            if(!$contraite->isActive() || $contraite->isInContradiction() || $contraite->hasEtatErreur()){return false;}
            if(!$contraite->getMax() || $contraite->getMax()==0){return false;}
            if(!$contraite->hasPorteeTerrain() && !$contraite->hasPorteeCategorie()){return false;}
            if($contraite->hasPorteeTerrain() && $contraite->getTerrainStage()->getId() != $terrain->getId()){
                return false;
            }
            if($contraite->hasPorteeCategorie() && !$contraite->getCategorieStage()->getTerrainsStages()->contains($terrain)){
                return false;
            }
            return ($contraite->getNbStagesValidant()
                + $contraite->getNbEquivalences() >= $contraite->getMax()
            );
        });
        //On retire les terrains n'autorisant pas les préférences ou inactif
        $nbPlace = $session->getNbPlacesOuvertes($terrain);
        if(!$this->modeAdmin && (
                !$terrain->isActif()
                || $nbPlace <=0
                || !$terrain->getPreferencesAutorisees()
                || $terrain->isContraintForNiveauEtude($niveauEtude)
                || !empty($contraintesCursusEtudiants)
            )
        ){
            $input->removeTerrainStage($terrain);
            $catOptions = $input->getCategorieStageOptions($terrain->getCategorieStage());
            if(empty($catOptions['options'])){
                $input->removeCategorieDeStage($terrain->getCategorieStage());
            }
            return $this;
        }

        $terrainAffectee = ($affectation && $affectation->hasEtatValidee()) ? $stage->getTerrainStage(): null;

        $badgeAffectation =
            ($terrainAffectee && $terrain->getId() == $terrainAffectee->getId()) ?
            $this->getBadgeAffectation() : ""
        ;
        //Rang des préférences :
        $badgeRang="";
        //Preference actuel sur le terrain Principale
        if($terrainPrincipal) {
            if ($preference->getTerrainStage() && $preference->getTerrainStage()->getId() == $terrain->getId()) {
                $badgeRang = $this->getBadgePreferenceActuelle($preference->getRang());
            }
            else { //Autres préférences
                /** @var Preference $pref */
                foreach ($stage->getPreferences() as $pref) {
                    if ($pref->getId() == $preference->getId()) {
                        continue;
                    }
                    if ($pref->getTerrainStage() && $pref->getTerrainStage()->getId() == $terrain->getId()) {
                        $badgeRang = $this->getBadgePreferenceRang($pref->getRang());
                        $input->setTerrainStageAttribute($terrain, 'disabled', 'disabled');
                        break;
                    }
                }
            }
        }
        else{
            if($preference->getTerrainStageSecondaire() && $preference->getTerrainStageSecondaire()->getId() == $terrain->getId()) {
                $badgeRang = $this->getBadgePreferenceActuelle($preference->getRang());
            }
            /** @var Preference $pref */
            foreach ($stage->getPreferences() as $pref) {
                if ($pref->getId() == $preference->getId()) {
                    continue;
                }
                if ($pref->getTerrainStageSecondaire() && $pref->getTerrainStageSecondaire()->getId() == $terrain->getId()) {
                    $badgeRang .= $this->getBadgePreferenceRang($pref->getRang()); //on notes tout les rang de préférences
                }
            }
        }

        $badgeDesactive= (!$terrain->isActif()
            || $nbPlace <=0
            || !$terrain->getPreferencesAutorisees()
            || !empty($contraintesCursusEtudiants)
            || $terrain->isContraintForNiveauEtude($niveauEtude)
        ) ? $this->getBadgeDesactive() : "";

        $dataContent = sprintf("
                <span class='flex'>
               <span class='w-60 normal-white-space'>%s</span>
               <span class='w-10  text-center'>%s</span>
               <span class='w-10  text-center'>%s</span>
               <span class='w-20  text-center'>%s</span>
               </span>
            ",
            $terrain->getLibelle(),
            $badgeRang, $badgeAffectation, $badgeDesactive
        );
        $input->setTerrainStageAttribute($terrain, 'title', $terrain->getLibelle());
        $input->setTerrainStageAttribute($terrain, "data-content", $dataContent);
        $input->setTerrainStageAttribute($terrain, 'data-terrain-id', $terrain->getId());
//
        return $this;
    }

    public function getBadgePreferenceActuelle($r): string
    {
        return sprintf("<span class='mx-1 badge badge-success'>%s</span>", $r);
    }

    public function getBadgeAffectation(): string
    {
        return "<span class='mx-1 badge badge-success'>A</span>";
    }

    public function getBadgeDesactive($libelle="Désactivé"): string
    {
        return sprintf("<span class='mx-1 badge badge-muted'>%s</span>", $libelle);
    }

    public function getBadgePreferenceRang(int|string $r) : string
    {
        return sprintf("<span class='mx-1 badge badge-light-info'>%s</span>", $r);
    }
}