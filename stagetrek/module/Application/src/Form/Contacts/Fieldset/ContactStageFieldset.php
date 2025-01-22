<?php


namespace Application\Form\Contacts\Fieldset;

use Application\Entity\Db\Contact;
use Application\Entity\Db\ContactStage;
use Application\Entity\Db\ContactTerrain;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use Application\Entity\Traits\Contact\HasContactTrait;
use Application\Entity\Traits\Etudiant\HasEtudiantTrait;
use Application\Entity\Traits\Stage\HasSessionStageTrait;
use Application\Entity\Traits\Stage\HasStageTrait;
use Application\Form\Contacts\Element\ContactSelectPicker;
use Application\Form\Contacts\Validator\ContactStageValidator;
use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\Stages\Element\SessionStageSelectPicker;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Filter\ToNull;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Number;
use Laminas\Form\Element\Text;

class ContactStageFieldset extends AbstractEntityFieldset
{


    use IdInputAwareTrait;

    public function init(): void
    {
        $this->initIdInput();
        $this->initContactInput();
        $this->initEtudiantInput();
        $this->initStageInput();
        $this->initSessionInput();
        $this->initPropertiesInput();
    }

    const CONTACT = "contact";
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
                ['name' => ToNull::class],
                ['name' => ToInt::class],
            ],
            'validators' => [
                [
                    'name' => ContactStageValidator::class,
                    'options' => [
                        'callback' => ContactStageValidator::ASSERT_CONTACT,
                        'modeEdition' => $this->isModeEdition(),
                        'break_chain_on_failure' => false,
                    ],
                ],
            ],
        ]);
    }

    const ETUDIANT = "etudiant";
    const ETUDIANT_ID = "etudiantId";
    private function initEtudiantInput() : void
    {
        $this->add([
            'name' => self::ETUDIANT_ID,
            'type' => Hidden::class,
            'attributes' => [
                'id' => self::ETUDIANT_ID,
            ],
        ]);

        $this->add([
            'name' => self::ETUDIANT,
            'type' => Text::class,
            'options' => [
                'label' => "Etudiant",
            ],
            'attributes' => [
                'id' => self::ETUDIANT,
                'placeholder' => "Rechercher un étudiant",
                'class' => 'individu-finder',
            ],
        ]);

        $this->setInputfilterSpecification(self::ETUDIANT, [
            "name" => self::ETUDIANT,
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => ContactStageValidator::class,
                    'options' => [
                        'callback' => ContactStageValidator::ASSERT_ETUDIANT,
                        'modeEdition' => $this->isModeEdition(),
                        'break_chain_on_failure' => false,
                    ],
                ],
            ],
        ]);
    }

    const STAGE_ID = "stageId";
    private function initStageInput() : void
    {
        $this->add([
            'name' => self::STAGE_ID,
            'type' => Hidden::class,
            'attributes' => [
                'id' => self::STAGE_ID,
            ],
        ]);
    }

    const SESSION = "sessionStage";
    private function initSessionInput() : void
    {
        $this->add([
            'type' => SessionStageSelectPicker::class,
            'name' => self::SESSION,
            'options' => [
                'label' => "Session de stage",
                'empty_option' => "Sélectionner une session de stage",
            ],
            'attributes' => [
                'id' => self::SESSION,
            ],
        ]);

        $this->setInputfilterSpecification(self::SESSION, [
            "name" => self::SESSION,
            'required' => true,
            'filters' => [
                ['name' => ToNull::class],
                ['name' => ToInt::class],
            ],
            'validators' => [
                [
                    'name' => ContactStageValidator::class,
                    'options' => [
                        'callback' => ContactStageValidator::ASSERT_SESSION,
                        'modeEdition' => $this->isModeEdition(),
                        'break_chain_on_failure' => false,
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
                'label' => "Visible par l'étudiants ?",
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
                'label' => "Responsable du stage ?",
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
                'label' => "Signataire des conventions de stage ?",
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
                'label' => "Peut valider le stage ?",
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
                'label' => "Envoie automatique du lien de validation du stage ?",
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
                'label' => "Envoie automatique du mail de rappel si la validation n'as pas été effectué ?",
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

        $toIntFilter = ['name' => ToInt::class];

        $this->setInputfilterSpecification(self::IS_VISIBLE_ETUDIANT, [
            "name" => self::IS_VISIBLE_ETUDIANT,
            'required' => true,
            'filters' => [$toIntFilter],
        ]);

        $this->setInputfilterSpecification(self::IS_RESPONSABLE_STAGE, [
            "name" => self::IS_RESPONSABLE_STAGE,
            'required' => true,
            'filters' => [$toIntFilter],
        ]);

        $this->setInputfilterSpecification(self::CAN_VALIDER_STAGE, [
            "name" => self::CAN_VALIDER_STAGE,
            'required' => true,
            'filters' => [$toIntFilter],
        ]);

        $this->setInputfilterSpecification(self::IS_SIGNATAIRE_CONVENTION, [
            "name" => self::IS_SIGNATAIRE_CONVENTION,
            'required' => true,
            'filters' => [$toIntFilter],
        ]);
        $this->setInputfilterSpecification(self::PRIORITE_ORDRE_SIGNATURE, [
            "name" => self::PRIORITE_ORDRE_SIGNATURE,
            'required' => true,
            'filters' => [$toIntFilter],
        ]) ;

        $this->setInputfilterSpecification(self::SEND_MAIL_AUTO_VALIDATION_STAGE, [
            "name" => self::SEND_MAIL_AUTO_VALIDATION_STAGE,
            'required' => true,
            'filters' => [$toIntFilter],
        ]);

        $this->setInputfilterSpecification(self::SEND_MAIL_AUTO_RAPPEL_VALIDATION_STAGE, [
            "name" => self::SEND_MAIL_AUTO_VALIDATION_STAGE,
            'required' => true,
            'filters' => [$toIntFilter],
        ]);
    }


    protected bool $modeEdition = false;
    public function setModeEdition(?bool $mode = true) : static
    {
        $this->modeEdition = $mode;
//        On met a jours les inputs filter en les réinitialisant
        $this->initContactInput();
        $this->initEtudiantInput();
        $this->initSessionInput();
        return $this;
    }
    public function isModeEdition() : bool
    {
        return $this->modeEdition;
    }

    use HasStageTrait;
    use HasSessionStageTrait;
    use HasContactTrait;
    use HasEtudiantTrait;
    //Faire de même pour les session de stage et les étudiants.
    public function setContact(Contact $contact) : static
    {
        $this->contact = $contact;
        /** @var ContactSelectPicker $selectContact */
        $selectContact = $this->get(self::CONTACT);
        $selectContact->setContacts([$contact]);
        $selectContact->setEmptyOption(null);
        return $this;
    }
    //Faire de même pour les session de stage et les étudiants.
    public function setStage(Stage $stage) : static
    {
        $this->stage = $stage;
        $etudiant = $stage->getEtudiant();
        $session = $stage->getSessionStage();
        $inputId = $this->get(self::STAGE_ID);
        $inputId->setValue($stage->getId());
        $this->setEtudiant($etudiant);
        $this->setSession($session);

        /** @var ContactSelectPicker $selectContact */
        $selectContact = $this->get(self::CONTACT);
        //Ajout d'un badge au contact associé au terrain
        $affectation = $stage->getAffectationStage();
        $terrain = (isset($affectation)) ? $stage->getTerrainStage() : null;
        if(isset($terrain)){
            $contacts = $terrain->getContactsTerrains();
            /** @var ContactTerrain $ct */
            foreach ($contacts as $ct){
                $badge =  "<span class='badge badge-light-success'>T</span>";
                $dataContent = sprintf("
                <span class='flex'>
               <span class='w-90 normal-white-space'>%s (%s)</span>
               <span class='w-10  text-center'>%s</span>
               </span>", $ct->getDisplayName(), $ct->getEmail(), $badge
                );
                $selectContact->setContactAttribute($ct->getContact(),  'title', $ct->getCode()." - ".$ct->getLibelle());
                $selectContact->setContactAttribute($ct->getContact(),  'data-content', $dataContent);
            }
        }
        //On désactive les contactsStages déjà associé
        /** @var ContactStage $cs */
        foreach ($stage->getContactsStages() as $cs){
            $selectContact->setContactAttribute($cs->getContact(), 'disabled', 'disabled');
        }
        return $this;
    }

    public function setEtudiant(Etudiant $etudiant) : static
    {
        $this->etudiant = $etudiant;
        $inputId = $this->get(self::ETUDIANT_ID);
        $inputId->setValue($etudiant->getId());
        $inputEtudiant = $this->get(self::ETUDIANT);
        $inputEtudiant->setAttribute('readonly', 'readonly');
        $inputEtudiant->setValue($etudiant->getDisplayName());
        return $this;
    }

    public function setSession(SessionStage $sessionStage) : static
    {
        $this->sessionStage = $sessionStage;
        /** @var SessionStageSelectPicker $selectSession */
        $selectSession = $this->get(self::SESSION);
        $selectSession->setSessions([$sessionStage]);
        $selectSession->setEmptyOption(null);
        return $this;
    }
}