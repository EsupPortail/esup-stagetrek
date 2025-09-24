<?php


namespace Application\Form\Affectation\Fieldset;

use Application\Entity\Db\AffectationStage;
use Application\Entity\Db\Preference;
use Application\Entity\Db\TerrainStage;
use Application\Entity\Traits\Stage\HasAffectationStageTrait;
use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\TerrainStage\Element\TerrainStagePrincipalSelectPicker;
use Application\Form\TerrainStage\Element\TerrainStageSecondaireSelectPicker;
use Application\Form\TerrainStage\Element\TerrainStageSelectPicker;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToFloat;
use Laminas\Filter\ToInt;
use Laminas\Filter\ToNull;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Number;
use Laminas\Form\Element\Textarea;
use Laminas\Validator\Callback;

/**
 * Class AffectationStageFieldset
 * @package Application\Form\AffectationStage\Fieldset;
 */
class AffectationStageFieldset  extends AbstractEntityFieldset
{
    use IdInputAwareTrait;

    const STAGE = "stage";
    const TERRAIN_STAGE = "terrainStage";
    const TERRAIN_STAGE_SECONDAIRE = "terrainStageSecondaire";
    const COUT_TERRAIN = "coutTerrain";
    const BONUS_MALUS = "bonusMalus";
    const STAGE_NON_EFFECTUE = "stageNonEffectue";
    const PRE_VALIDER = "prevalidee";
    const VALIDER = "validee";
    const INFOS = "informationsComplementaires";
    const SEND_MAIL = "sendMail";
    public function init(): static
    {
        $this->initIdInput();
        $this->initStageInput();
        $this->initTerrainStageInputs();
        $this->initCoutsAffectationInputs();
        $this->initEtatsInputs();
        $this->initPropertiesInputs();
        return $this;
    }

    private function initStageInput() : void
    {
        $this->add([
            "name" => self::STAGE,
            'type' => Hidden::class,
            'attributes' => [
                "id" => self::STAGE,
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

    private function initTerrainStageInputs() : void
    {
        $this->add([
            'type' => TerrainStagePrincipalSelectPicker::class,
            'name' => self::TERRAIN_STAGE,
            'options' => [
                'label' => "Terrain de stage",
                'empty_option' => "Pas de terrain de stage",
            ],
            'attributes' => [
                'id' => self::TERRAIN_STAGE,
            ],
        ]);

        $this->add([
            'type' => TerrainStageSecondaireSelectPicker::class,
            'name' => self::TERRAIN_STAGE_SECONDAIRE,
            'options' => [
                'label' =>  "Terrain de stage secondaire",
                'empty_option' => "Pas de terrain de stage secondaire",
            ],
            'attributes' => [
                'id' => self::TERRAIN_STAGE_SECONDAIRE,
            ],
        ]);
        $this->setInputfilterSpecification(self::TERRAIN_STAGE,  [
            'name' => self::TERRAIN_STAGE,
            'required' => false,
            'filters' => [
                ['name' => ToNull::class],
                ['name' => ToInt::class],
            ],
        ]);
        $this->setInputfilterSpecification(self::TERRAIN_STAGE_SECONDAIRE,  [
            'name' => self::TERRAIN_STAGE_SECONDAIRE,
            'required' => false,
            'filters' => [
                ['name' => ToNull::class],
                ['name' => ToInt::class],
            ]
        ]);
    }

    private function initCoutsAffectationInputs() : void
    {
        $this->add([
            'name' => self::COUT_TERRAIN,
            'type' => Number::class,
            'options' => [
                'label' => "Cout du terrain",
            ],
            'attributes' => [
                'id' => self::COUT_TERRAIN,
                'step' => 'any',
            ],
        ]);
        $this->add([
            'name' => self::BONUS_MALUS,
            'type' => Number::class,
            'options' => [
                'label' => "Bonus / Malus",
            ],
            'attributes' => [
                'id' => self::BONUS_MALUS,
                'step' => 'any',
            ],
        ]);

        $this->setInputfilterSpecification(self::COUT_TERRAIN, [
            "name" => self::COUT_TERRAIN,
            'required' => true,
            'filters' => [
                ['name' => ToFloat::class],
            ]
        ]);

        $this->setInputfilterSpecification(self::BONUS_MALUS, [
            "name" => self::BONUS_MALUS,
            'required' => true,
            'filters' => [
                ['name' => ToFloat::class],
            ],
        ]);
    }

    private function initEtatsInputs() : void
    {
        $this->add([
            'name' => self::PRE_VALIDER,
            'type' => Checkbox::class,
            'options' => [
                'label' => "Affectation pré-validée",
                'use_hidden_element' => true,
                'checked_value' => "1",
                'unchecked_value' => "0",
            ],
            'attributes' => [
                'id' => self::PRE_VALIDER,
                'value' => 0,
                'class' => 'form-check-input'
            ],
        ]);

        $this->add([
            'name' => self::VALIDER,
            'type' => Checkbox::class,
            'options' => [
                'label' => "Affectation validée",
                'use_hidden_element' => true,
                'checked_value' => "1",
                'unchecked_value' => "0",
            ],
            'attributes' => [
                'id' => self::VALIDER,
                'value' => 0,
                'class' => 'form-check-input'
            ],
        ]);

        $this->setInputfilterSpecification(self::PRE_VALIDER, [
            'name' => self::PRE_VALIDER,
            'required' => false,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $this->setInputfilterSpecification(self::VALIDER, [
            'name' => self::VALIDER,
            'required' => false,
            'filters' => [
                ['name' => ToInt::class],
            ],
            'validators' => [[
                'name' => Callback::class,
                'options' => [
                    'messages' => [
                        Callback::INVALID_VALUE => "L'affectation doit être pré-validée avant d'être validée",
                    ],
                    'callback' => function ($value, $context = []) {
                        $validee = boolval($value);
                        $prealidee = (isset($context[self::PRE_VALIDER])) ?
                            boolval($context[self::PRE_VALIDER]) : false;
                        return (!$validee || ($validee && $prealidee));
                    }
                ],
            ],[
                'name' => Callback::class,
                'options' => [
                    'messages' => [
                        Callback::INVALID_VALUE => "Une affectation sans terrain de stage ne peux-être validée sans l'option Stage non effectué",
                    ],
                    'callback' => function ($value, $context = []) {
                        $validee = boolval($value);
                        if(!$validee){return true;}
                        $stageNonEffectuee = (isset($context[self::STAGE_NON_EFFECTUE])) ?
                            boolval($context[self::STAGE_NON_EFFECTUE]) : false;
                        if($stageNonEffectuee){return true;}
                        $terrainId = (isset($context[self::TERRAIN_STAGE])) ?
                            intval($context[self::TERRAIN_STAGE]) : 0;
                        return ($terrainId !=0);
                    }
                ],
            ]],
        ]);
    }
    private function initPropertiesInputs() : void
    {
        $this->add([
            'name' => self::STAGE_NON_EFFECTUE,
            'type' => Checkbox::class,
            'options' => [
                'label' => "Stage non effectué",
                'use_hidden_element' => true,
                'checked_value' => "1",
                'unchecked_value' => "0",
            ],
            'attributes' => [
                'id' => self::STAGE_NON_EFFECTUE,
                'value' => 0,
                'class' => 'form-check-input'
            ],
        ]);


        $this->add([
            'name' => self::SEND_MAIL,
            'type' => Checkbox::class,
            'options' => [
                'label' => "Prévenir l'étudiant d'une modification sur son affectation ?",
                'use_hidden_element' => true,
                'checked_value' => "1",
                'unchecked_value' => "0",
            ],
            'attributes' => [
                'id' => self::SEND_MAIL,
                'value' => 0,
                'class' => 'form-check-input'
            ],
        ]);

        $this->add([
            'name' => self::INFOS,
            'type' => Textarea::class,
            'options' => [
                'label' => "Informations complémentaire",
            ],
            'attributes' => [
                'id' => self::INFOS,
                'class' => 'no-resize',
                "placeholder" => "Saisir des informations complémentaires",
                "rows" => 5,
                "max-rows" => 10,
            ],
        ]);

        $this->setInputfilterSpecification(self::STAGE_NON_EFFECTUE, [
            'name' => self::STAGE_NON_EFFECTUE,
            'required' => false,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $this->setInputfilterSpecification(self::SEND_MAIL, [
            'name' => self::SEND_MAIL,
            'required' => false,
            'filters' => [
                ['name' => ToInt::class],
            ],
            'validators' => [[
                'name' => Callback::class,
                'options' => [
                    'messages' => [
                        Callback::INVALID_VALUE => "L'affectation doit être validée pour selectionner l'option d'envoie du mail.",
                    ],
                    'callback' => function ($value, $context = []) {
                        $sendMail = boolval($value);
                        $validee = (isset($context[self::VALIDER])) ?
                            boolval($context[self::VALIDER]) : false;
                        return (!$sendMail || ($sendMail && $validee));
                    }
                ],
            ]],
        ]);

        $this->setInputfilterSpecification(self::INFOS, [
            "name" => self::INFOS,
            'required' => false,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
                ['name' => ToNull::class],
            ],
        ]);
    }

    use HasAffectationStageTrait;

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */
    public function setAffectationStage(?AffectationStage $affectationStage): static
    {
        $this->affectationStage = $affectationStage;
        /** @var TerrainStage[] $terrains */
        $terrains = $this->getObjectManager()->getRepository(TerrainStage::class)->findAll();
        foreach ($terrains as $terrain){
            $this->updateSelectTerrain($terrain);
        }
        return $this;
    }

    protected function updateSelectTerrain(TerrainStage $terrain) : static
    {
        $affectation = $this->affectationStage;
        if (!$affectation) return $this;
        $stage = $affectation->getStage();
        $session = $stage->getSessionStage();
        $preferences = $stage->getPreferences();
        $niveauEtude = $stage->getNiveauEtude();
        $terrainLinker = $session->getTerrainLinkFor($terrain);

        $isTerrainPrincipal = ($terrain->isTerrainPrincipal());

        /** @var TerrainStageSelectPicker $input */
        $input = $this->get(self::TERRAIN_STAGE);
        if(!$isTerrainPrincipal){
            $input = $this->get(self::TERRAIN_STAGE_SECONDAIRE);
        }

        $terrainAffectee = ($affectation->hasEtatValidee()) ? $stage->getTerrainStage() : null;

        $badgeAffectation =
            ($terrainAffectee && $terrain->getId() == $terrainAffectee->getId()) ?
                $this->getBadgeAffectation() : ""
        ;

        $nbPlaces = $session->getNbPlacesOuvertes($terrain);
        $nbPlacesPreAffectees = $terrainLinker->getNbPlacesPreAffectees();
        $badgePlaces = $this->getBadgePlaces($nbPlacesPreAffectees, $nbPlaces);
        //Rang des préférences :
//        Cas d'un terrains de stage principal

        $badgePreference = "";
        if($isTerrainPrincipal) {
            /** @var Preference $pref */
            foreach ($preferences as $pref) {
                if ($pref->getTerrainStage() && $pref->getTerrainStage()->getId() == $terrain->getId()) {
                    $badgePreference = $this->getBadgePreferenceRang($pref->getRang());
                    break;
                }
            }
        }
        else{
            /** @var Preference $pref */
            foreach ($preferences as $pref) {
                if ($pref->getTerrainStageSecondaire() && $pref->getTerrainStageSecondaire()->getId() == $terrain->getId()) {
                    $badgePreference .= $this->getBadgePreferenceRang($pref->getRang());
                }
            }
        }
        $badgeDesactive= (!$terrain->isActif()
            || $terrain->isContraintForNiveauEtude($niveauEtude)
        ) ? $this->getBadgeDesactive() : "";

        $dataContent = sprintf("
                <span class='flex'>
               <span class='w-50 normal-white-space'>%s</span>
               <span class='w-10  text-center'>%s</span>
               <span class='w-10  text-center'>%s</span>
               <span class='w-10  text-center'>%s</span>
               <span class='w-20  text-center'>%s</span>
               </span>               
            ",
            $terrain->getLibelle(),
            $badgePlaces, $badgePreference, $badgeAffectation, $badgeDesactive
        );

        $input->setTerrainStageAttribute($terrain, 'title', $terrain->getLibelle());
        $input->setTerrainStageAttribute($terrain, "data-content", $dataContent);
        return $this;
    }

    public function getBadgeAffectation() : string
    {
        return "<span class='mx-1 badge badge-success'>A</span>";
    }

    public function getBadgeDesactive($libelle="Désactivé") : string
    {
        return sprintf("<span class='mx-1 badge badge-muted'>%s</span>", $libelle);
    }

    public function getBadgePreferenceRang($r) : string
    {
        return sprintf("<span class='mx-1 badge badge-primary'>%s</span>", $r);
    }

    public function getBadgePlaces($nbPreAffectations, $nbPlacesOuvertes) : string
    {
        $badgeClass = "";
        switch (true){
            case ($nbPreAffectations == $nbPlacesOuvertes && $nbPlacesOuvertes==0) :
                $badgeClass = "badge-muted";
            break;
            case ($nbPreAffectations < $nbPlacesOuvertes ) :
                $badgeClass = "badge-light-info";
                break;
            case ($nbPreAffectations == $nbPlacesOuvertes ) :
                $badgeClass = "badge-success";
                break;
            case ($nbPreAffectations > $nbPlacesOuvertes ) :
                $badgeClass = "badge-warning";
                break;
        }
        return sprintf("<span class='mx-1 badge %s'>%s / %s</span>",
            $badgeClass, $nbPreAffectations, $nbPlacesOuvertes
        );
    }
}