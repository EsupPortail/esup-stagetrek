<?php


namespace Application\Form\Contacts\Fieldset;

use Application\Entity\Db\ContactTerrain;
use Application\Entity\Traits\Contact\HasContactTerrainTrait;
use Application\Entity\Traits\Contact\HasContactTrait;
use Application\Entity\Traits\Stage\HasTerrainStageTrait;
use Application\Form\Abstrait\Traits\FormElementTrait;
use Application\Form\Contacts\Element\ContactSelectPicker;
use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\TerrainStage\Element\TerrainStageSelectPicker;
use Laminas\Filter\ToInt;
use Laminas\Filter\ToNull;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Number;
use Laminas\Validator\Callback;

class ContactTerrainFieldset extends AbstractEntityFieldset
{
    use FormElementTrait;

    const CONTACT = "contact";
    const TERRAIN = "terrainStage";

    /** Messages d'erreur */
    use IdInputAwareTrait;


    public function init() : static
    {
        $this->initIdInput();
        $this->initContactInput();
        $this->initTerrainInput();
        $this->initPropertiesInput();
        return $this;
    }

    private function initContactInput() : void
    {
        $this->add([
            'type' => ContactSelectPicker::class,
            'name' => self::CONTACT,
            'options' => [
                'label' => "Contact",
                'empty_option' => "Sélectionner un contact",
            ],
            'attributes' => [
                'id' => self::CONTACT,
            ],
        ]);

        $this->setInputfilterSpecification(self::CONTACT, [
            "name" => self::CONTACT,
            'required' => true,
            'filters' => [
                ['name' => ToNull::class], // Permet de s'assurer que le contact existe
                ['name' => ToInt::class],
            ],
            'validators' => [
                [
                    'name' => Callback::class,
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => "Le contact est déjà associé au terrain de stage",
                        ],
                        'callback' => function ($value) {
                            if($this->modeEdition){return true;}
                            //Faux car le terrain n'existe pas géré après
                            if(!isset($this->terrainStage)){return true;}
                            /** @var ContactTerrain $ct */
                            foreach ($this->terrainStage->getContactsTerrains() as $ct){
                                if($ct->getContact()->getId() == $value){
                                    return false;
                                }
                            }
                            return true;
                        },
                        'break_chain_on_failure' => true,
                    ],
                ],
            ],
        ]);
    }

    private function initTerrainInput() : void
    {
        $this->add([
            'type' => TerrainStageSelectPicker::class,
            'name' => self::TERRAIN,
            'options' => [
                'label' => "Terrain de stage",
                'empty_option' => "Sélectionner un terrain de stage",
            ],
            'attributes' => [
                'id' => self::TERRAIN,
            ],
        ]);

        $this->setInputfilterSpecification(self::TERRAIN, [
            "name" => self::TERRAIN,
            'required' => true,
            'filters' => [
                ['name' => ToNull::class], // Permet de s'assurer que le terrain existe
                ['name' => ToInt::class],
            ],
            'validators' => [
                [
                    'name' => Callback::class,
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => "Le contact est déjà associé au terrain de stage",
                        ],
                        'callback' => function ($value) {
                            if($this->modeEdition){return true;}
                            //Faux car le contact n'existe pas
                            if(!isset($this->contact)){return true;}
                            /** @var ContactTerrain $ct */
                            foreach ($this->contact->getContactsTerrains() as $ct){
                                if($ct->getTerrainStage()->getId() == $value){
                                    return false;
                                }
                            }
                            return true;
                        },
                        'break_chain_on_failure' => true,
                    ],
                ],
            ],
        ]);
    }

    const IS_VISIBLE_ETUDIANT = "isVisibleParEtudiant";
    const IS_RESPONSABLE_STAGE = "isResponsableStage";
    const CAN_VALIDER_STAGE = "canValiderStage";
    const IS_SIGNATAIRE_CONVENTION = "isSignataireConvention";
    const PRIORITE_ORDRE_SIGNATURE = "prioriteOrdreSignature";
//    const SEND_MAIL_AUTO_LISTE_ETUDIANT_STAGE = "sendMailAutoListeEtudiantsStage";
    const SEND_MAIL_AUTO_VALIDATION_STAGE = "sendMailAutoValidationStage";
    const SEND_MAIL_AUTO_RAPPEL_VALIDATION_STAGE = "sendMailAutoRappelValidationStage";
    private function initPropertiesInput() : void
    {
        $this->add([
            'name' => self::IS_VISIBLE_ETUDIANT,
            'type' => Checkbox::class,
            'options' => [
                'label' => "Visible par les étudiants ?",
                'label_options' => [
                    'disable_html_escape' => true,
                    'checked_value' => "1",
                    'unchecked_value' => "0",
                ],
                'use_hidden_element' => true,
            ],
            'attributes' => [
                'id' => self::IS_VISIBLE_ETUDIANT,
                'value' => 1,
                'class' => 'form-check-input',
            ]
        ]);

        $this->add([
            'name' => self::IS_RESPONSABLE_STAGE,
            'type' => Checkbox::class,
            'options' => [
                'label' => "Responsable des stages ?",
                'label_options' => [
                    'disable_html_escape' => true,
                    'checked_value' => "1",
                    'unchecked_value' => "0",
                ],
                'use_hidden_element' => true,
            ],
            'attributes' => [
                'id' => self::IS_RESPONSABLE_STAGE,
                'value' => 1,
                'class' => 'form-check-input',
            ]
        ]);

        $this->add([
            'name' => self::IS_SIGNATAIRE_CONVENTION,
            'type' => Checkbox::class,
            'options' => [
                'label' =>  "Signataire des conventions de stage ?",
                'label_options' => [
                    'disable_html_escape' => true,
                    'checked_value' => "1",
                    'unchecked_value' => "0",
                ],
                'use_hidden_element' => true,
            ],
            'attributes' => [
                'id' => self::IS_SIGNATAIRE_CONVENTION,
                'value' => 1,
                'class' => 'form-check-input',
            ]
        ]);


        $this->add([
            "name" => self::PRIORITE_ORDRE_SIGNATURE,
            "type" => Number::class,
            "options" => [
                "label" => "Priorité dans l'ordre d'affichage des conventions",
            ],
            "attributes" => [
                "id" => self::PRIORITE_ORDRE_SIGNATURE,
                "min" => 0,
            ],
        ]);

        $this->add([
            'name' => self::CAN_VALIDER_STAGE,
            'type' => Checkbox::class,
            'options' => [
                'label' =>  "Peut valider les stages ?",
                'label_options' => [
                    'disable_html_escape' => true,
                    'checked_value' => "1",
                    'unchecked_value' => "0",
                ],
                'use_hidden_element' => true,
            ],
            'attributes' => [
                'id' => self::CAN_VALIDER_STAGE,
                'value' => 1,
                'class' => 'form-check-input',
            ]
        ]);

        $this->add([
            'name' => self::SEND_MAIL_AUTO_VALIDATION_STAGE,
            'type' => Checkbox::class,
            'options' => [
                'label' => "Envoie automatique du lien de validation des stages ?",
                'label_options' => [
                    'disable_html_escape' => true,
                    'checked_value' => "1",
                    'unchecked_value' => "0",
                ],
                'use_hidden_element' => true,
            ],
            'attributes' => [
                'id' => self::SEND_MAIL_AUTO_VALIDATION_STAGE,
                'value' => 1,
                'class' => 'form-check-input',
            ]
        ]);

        $this->add([
            'name' => self::SEND_MAIL_AUTO_RAPPEL_VALIDATION_STAGE,
            'type' => Checkbox::class,
            'options' => [
                'label' => "Envoie automatique du mail de rappels pour les stages dont la validation n'as pas été effectués ?",
                'label_options' => [
                    'disable_html_escape' => true,
                    'checked_value' => "1",
                    'unchecked_value' => "0",
                ],
                'use_hidden_element' => true,
            ],
            'attributes' => [
                'id' => self::SEND_MAIL_AUTO_VALIDATION_STAGE,
                'value' => 1,
                'class' => 'form-check-input',
            ]
        ]);

        $this->setInputfilterSpecification(self::IS_VISIBLE_ETUDIANT, [
            "name" => self::IS_VISIBLE_ETUDIANT,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $this->setInputfilterSpecification(self::CAN_VALIDER_STAGE, [
            "name" => self::CAN_VALIDER_STAGE,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $this->setInputfilterSpecification(self::IS_RESPONSABLE_STAGE, [
            "name" => self::IS_RESPONSABLE_STAGE,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $this->setInputfilterSpecification(self::IS_SIGNATAIRE_CONVENTION,[
            "name" => self::IS_SIGNATAIRE_CONVENTION,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);
        $this->setInputfilterSpecification(self::PRIORITE_ORDRE_SIGNATURE, [
            "name" => self::PRIORITE_ORDRE_SIGNATURE,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);
        $this->setInputfilterSpecification(self::SEND_MAIL_AUTO_VALIDATION_STAGE, [
            "name" => self::SEND_MAIL_AUTO_VALIDATION_STAGE,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $this->setInputfilterSpecification(self::SEND_MAIL_AUTO_RAPPEL_VALIDATION_STAGE, [
            "name" => self::SEND_MAIL_AUTO_VALIDATION_STAGE,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);
    }





    protected bool $modeEdition = false;
    public function setModeEdition(?bool $mode = true) : static
    {
        $this->modeEdition = $mode;
        return $this;
    }
    public function isModeEdition() : bool
    {
        return $this->modeEdition;
    }

    use HasContactTrait;
    use HasContactTerrainTrait;
    use HasTerrainStageTrait;

    public function setContactTerrain(ContactTerrain $contactTerrain) : static
    {
        $this->contactTerrain = $contactTerrain;
        $this->contact = $contactTerrain->getContact();
        $this->terrainStage = $contactTerrain->getTerrainStage();
        /** @var TerrainStageSelectPicker $selectTerrain */
        $selectTerrain = $this->get(self::TERRAIN);
        /** @var ContactSelectPicker $selectContact */
        $selectContact = $this->get(self::CONTACT);
        if(isset($this->terrainStage)) {
            $selectTerrain->setTerrainsStages([$this->terrainStage]);
            $selectTerrain->setEmptyOption(null);
        }
        if(isset($this->contact)) {
            $selectContact->setContacts([$this->contact]);
            $selectContact->setEmptyOption(null);
        }

        if(isset($this->contact) && !isset($this->terrainStage)) {
            /** @var ContactTerrain $ct */
            foreach ($this->contact->getContactsTerrains() as $ct) {
                $terrain = $ct->getTerrainStage();
                $selectTerrain->removeTerrainStage($terrain);
            }
        }
        if(isset($this->terrainStage) && !isset($this->contact)) {
            /** @var ContactTerrain $ct */
            foreach ($this->terrainStage->getContactsTerrains() as $ct) {
                $contact = $ct->getContact();
                $selectContact->removeContact($contact);
            }
        }
        return $this;
    }
}