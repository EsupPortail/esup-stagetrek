<?php


namespace Application\Form\Etudiant;

use Application\Entity\Db\Groupe;
use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Abstrait\Interfaces\AbstractFormConstantesInterface;
use Application\Form\Annees\Element\AnneeUniversitaireSelectPicker;
use Application\Form\Groupe\Element\GroupeSelectPicker;
use Application\Form\Referentiel\Element\ReferentielPromoSelectPicker;
use Laminas\Filter\File\RenameUpload;
use Laminas\Form\Element\Button;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\File;
use Laminas\Form\Element\Hidden;
use Laminas\InputFilter\FileInput;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\Callback;
use Laminas\Validator\File\Extension;
use Laminas\Validator\File\MimeType;
use Laminas\Validator\File\Size;
use Laminas\Validator\File\UploadFile;

/**
 * Class ImportEtudiantForm
 * @package Application\Form\Etudiant
 */
class ImportEtudiantForm extends AbstractEntityForm implements AbstractFormConstantesInterface
{
    // Liste des input
    const INPUT_DATA_PROVIDED = "data-provided";
    const INPUT_IMPORT_REFERENTIEL = "referentiel";
    const INPUT_IMPORT_REFERENTIEL_ANNEE = "annee";
    const INPUT_IMPORT_FILE = "import_file";
    const INPUT_IMPORT_GROUPE = "import_groupe";
    const INPUT_ADD_IN_GROUPE = "add_in_groupe";
    // Pour savoir qu'elle onglet ouvrir dans l'affichage
    const INPUT_CURRENT_IMPORT = "current_import";


    // Liste des label
    const LABEL_IMPORT_REFERENTIEL = "Référentiel";
    const LABEL_IMPORT_FILE = "Fichier";
    const LABEL_IMPORT_GROUPE = "Importer depuis un groupe";
    const LABEL_ADD_IN_GROUPE = "Inscrire les étudiants dans un groupe";
    const EMPTY_OPTION_GROUPE = "Selectionner un groupe";
    const EMPTY_OPTION_REFERENTIEL = "Selectionner un référentiel";

    const VALIDATOR_MSG_NO_DATA_PROVIDED = "Vous devez selectionner une source de données.";
    const VALIDATOR_MSG_MULTIPLES_SOUCES_PROVIDED = "Vous devez selectionner une seul source de données.";
    const VALIDATOR_MSG_FILE_NOT_FOUND = "Vous devez fournir un fichier au format csv.";
    const VALIDATOR_MSG_FILE_NOT_CSV_EXTENTION = "L'extension de votre fichier n'est pas correcte.";
    const VALIDATOR_MSG_FILE_NOT_CSV_MIME_TYPE = "Le format de votre fichier n'est pas correct : %type%";
    const VALIDATOR_MSG_FILE_MAX_SIZE = "Le poids maximum autorisé pour le fichier est %max%.";

    const VALIDATOR_MSG_GROUPE_NOT_FOUND = "Le groupe demandé n'as pas été trouvé.";
    const VALIDATOR_MSG_IMPORT_GROUPE_NO_DESTINATION = "L'import depuis un groupe requiére de selectionner un autre groupe où inscrire les étudiants";
    const VALIDATOR_MSG_IMPORT_GROUPE_SAME_YEAR = "Le groupe de référence pour l'import ne doit pas être de la même année universitaire que le groupe d'inscription des étudiants";

    public function init(): void
    {
        $this->setAttribute('method', 'post')
            ->setAttribute('action', $this->getCurrentUrl())
            ->setAttribute('class', 'loadingForm');

        $this->addElements();
        $this->addInputFilter();
    }

    protected function addElements(): static
    {
        $this->add([
            'type' => Hidden::class,
            'name' => self::INPUT_DATA_PROVIDED,
            'attributes' => [
                'id' => self::INPUT_DATA_PROVIDED,
                'value' => 0,
            ],
        ]);
        $this->add([
            'type' => ReferentielPromoSelectPicker::class,
            'name' => self::INPUT_IMPORT_REFERENTIEL,
            'options' => [
                'label' => self::LABEL_IMPORT_REFERENTIEL,
                'empty_option' => self::EMPTY_OPTION_REFERENTIEL,
            ],
            'attributes' => [
                'id' => self::INPUT_IMPORT_REFERENTIEL,
            ],
        ]);

        $this->add([
            'type' => AnneeUniversitaireSelectPicker::class,
            'name' => self::INPUT_IMPORT_REFERENTIEL_ANNEE,
            'options' => [
                'label' => "Année d'inscription",
                'empty_option' => "Selectionnez une année universitaire",
            ],
            'attributes' => [
                'id' => self::INPUT_IMPORT_REFERENTIEL_ANNEE,
            ],
        ]);


        $this->add([
            'type' => File::class,
            'name' => self::INPUT_IMPORT_FILE,
            'options' => [
                'label' => self::LABEL_IMPORT_FILE,
            ],
            'attributes' => [
                'id' => self::INPUT_IMPORT_FILE,
                "style" => "height:auto", //Pour forcer le css
            ],
        ]);


        $this->add([
            'type' => GroupeSelectPicker::class,
            'name' => self::INPUT_IMPORT_GROUPE,
            'options' => [
                'label' => self::LABEL_IMPORT_GROUPE,
                'empty_option' => self::EMPTY_OPTION_GROUPE,
            ],
            'attributes' => [
                'id' => self::INPUT_IMPORT_GROUPE,
            ],
        ]);

        $this->add([
            'type' => GroupeSelectPicker::class,
            'name' => self::INPUT_ADD_IN_GROUPE,
            'options' => [
                'label' => self::LABEL_ADD_IN_GROUPE,
                'empty_option' => self::EMPTY_OPTION_GROUPE,
            ],
            'attributes' => [
                'id' => self::INPUT_ADD_IN_GROUPE,
            ],
        ]);

        $this->add([
            'type' => Hidden::class,
            'name' => self::INPUT_CURRENT_IMPORT,
            'attributes' => [
                'value' => self::INPUT_IMPORT_REFERENTIEL,
                'id' => self::INPUT_CURRENT_IMPORT,
            ],
        ]);

        $this->add([
            'type' => Button::class,
            'name' => self::INPUT_SUBMIT,
            'options' => [
                'label' => self::LABEL_SUBMIT_IMPORT,
                'label_options' => [
                    'disable_html_escape' => true,
                ],
            ],
            'attributes' => [
                'id' => self::INPUT_SUBMIT,
                'type' => 'submit',
                'class' => 'btn btn-primary',
            ],
        ]);

        $this->add(new Csrf(self::CSRF));
        return $this;
    }

    protected function addInputFilter() : static
    {
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        $inputFilter->add([
            'name' => self::INPUT_DATA_PROVIDED,
            'required' => true,
            'validators' => [[
                'name' => Callback::class,
                'options' => [
                    'messages' => [
                        Callback::INVALID_VALUE => self::VALIDATOR_MSG_NO_DATA_PROVIDED,
                    ],
                    'callback' => function ($value, $context = []) {
                        $referentiel = (isset($context[self::INPUT_IMPORT_REFERENTIEL]) && strlen($context[self::INPUT_IMPORT_REFERENTIEL]) > 0);
                        $csv = (isset($context[self::INPUT_IMPORT_FILE])
                            && isset($context[self::INPUT_IMPORT_FILE]['name'])
                            && strlen($context[self::INPUT_IMPORT_FILE]['name']) > 0);
                        $fromGroupe = (isset($context[self::INPUT_IMPORT_GROUPE]) && strlen($context[self::INPUT_IMPORT_GROUPE]) > 0);
                        return $referentiel || $csv || $fromGroupe;
                    },
                    'break_chain_on_failure' => true,
                ]],
            ],
        ]);

        $inputFilter->add([
            'name' => self::INPUT_IMPORT_REFERENTIEL,
            'required' => false,
            'validators' => [[
                'name' => Callback::class,
                'options' => [
                    'messages' => [
                        Callback::INVALID_VALUE => self::VALIDATOR_MSG_MULTIPLES_SOUCES_PROVIDED,
                    ],
                    'callback' => function ($value, $context = []) {
                        $referentiel = (isset($context[self::INPUT_IMPORT_REFERENTIEL]) && strlen($context[self::INPUT_IMPORT_REFERENTIEL]) > 0);
                        $csv = (isset($context[self::INPUT_IMPORT_FILE])
                            && isset($context[self::INPUT_IMPORT_FILE]['name'])
                            && strlen($context[self::INPUT_IMPORT_FILE]['name']) > 0);
                        return !$referentiel || !$csv;
                    },
                    'break_chain_on_failure' => true,
                ],
            ],
            [
            'name' => Callback::class,
                'options' => [
                    'messages' => [
                        Callback::INVALID_VALUE => "Vous devez préciser l'année d'inscription des étudiants",
                    ],
                    'callback' => function ($value, $context = []) {
                        $annee = (isset($context[self::INPUT_IMPORT_REFERENTIEL_ANNEE]) && strlen($context[self::INPUT_IMPORT_REFERENTIEL_ANNEE]) > 0);
                        return $annee;
                    },
                    'break_chain_on_failure' => true,
                ],
            ]
            ],
        ]);

        $inputFilter->add([
            'name' => self::INPUT_IMPORT_REFERENTIEL_ANNEE,
            'required' => false,
            'validators' => [[
                'name' => Callback::class,
                'options' => [
                    'messages' => [
                        Callback::INVALID_VALUE => "Vous devez selectionner le référentiel d'import",
                    ],
                    'callback' => function ($value, $context = []) {
                        $referentiel = (isset($context[self::INPUT_IMPORT_REFERENTIEL]) && strlen($context[self::INPUT_IMPORT_REFERENTIEL]) > 0);
                        return $referentiel;
                    },
                    'break_chain_on_failure' => true,
                ],
            ]],
        ]);

        $inputFilter->add([
            'name' => self::INPUT_IMPORT_GROUPE,
            'required' => false,
            'validators' => [
                [
                    'name' => Callback::class,
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => self::VALIDATOR_MSG_IMPORT_GROUPE_NO_DESTINATION,
                        ],
                        'callback' => function ($value, $context = []) {
                            if (!isset($context[self::INPUT_ADD_IN_GROUPE])) {
                                return false;
                            }
                            return (intval(($context[self::INPUT_ADD_IN_GROUPE]) ?? 0) != 0);
                        },
                        'break_chain_on_failure' => true,
                    ],
                ],
                [
                    'name' => Callback::class,
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => self::VALIDATOR_MSG_GROUPE_NOT_FOUND,
                        ],
                        'callback' => function ($value, $context = []) {
                            $groupeId = intval(($value) ?? 0);
                            $groupe = $this->getObjectManager()->getRepository(Groupe::class)->find($groupeId);
                            return isset($groupe);
                        },
                        'break_chain_on_failure' => true,
                    ],
                ],
                [
                    'name' => Callback::class,
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => self::VALIDATOR_MSG_IMPORT_GROUPE_SAME_YEAR,
                        ],
                        'callback' => function ($value, $context = []) {
                            if (!isset($context[self::INPUT_ADD_IN_GROUPE])) {
                                return true; //Faux mais pour d'autres raison
                            }
                            $groupeIdA = intval(($value) ?? 0);
                            $groupeIdB = intval(($context[self::INPUT_ADD_IN_GROUPE]) ?? 0);
                            /** @var Groupe $groupeA */
                            $groupeA = $this->getObjectManager()->getRepository(Groupe::class)->find($groupeIdA);
                            /** @var Groupe $groupeB */
                            $groupeB = $this->getObjectManager()->getRepository(Groupe::class)->find($groupeIdB);
                            if (!isset($groupeA) || !isset($groupeB)) {
                                return true; // false mais gérer ailleurs
                            }
                            return ($groupeA->getAnneeUniversitaire()->getId() != $groupeB->getAnneeUniversitaire()->getId());
                        },
                        'break_chain_on_failure' => true,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            "name" => self::INPUT_ADD_IN_GROUPE,
            'required' => false,
            'validator' => [
                [
                    'name' => Callback::class,
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => self::VALIDATOR_MSG_GROUPE_NOT_FOUND,
                        ],
                        'callback' => function ($value) {
                            $groupeId = intval(($value) ?? 0);
                            $groupe = $this->getObjectManager()->getRepository(Groupe::class)->find($groupeId);
                            return isset($groupe);
                        },
                        'break_chain_on_failure' => true,
                    ],
                ],
            ]
        ]);
        $inputFilter->add([
            'name' => self::INPUT_IMPORT_FILE,
            'required' => false,
            'type' => FileInput::class,
            'filters' => [
                [
                    'name' => RenameUpload::class,
                    'options' => [
                        'overwrite' => true,
                        'randomize' => true,
                    ],
                ],
            ],
            'validators' => [
                [
                    'name' => Callback::class,
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => self::VALIDATOR_MSG_MULTIPLES_SOUCES_PROVIDED,
                        ],
                        'callback' => function ($value, $context = []) {
                            $referentiel = (isset($context[self::INPUT_IMPORT_REFERENTIEL]) && strlen($context[self::INPUT_IMPORT_REFERENTIEL]) > 0);
                            $csv = (isset($context[self::INPUT_IMPORT_FILE])
                                && isset($context[self::INPUT_IMPORT_FILE]['name'])
                                && strlen($context[self::INPUT_IMPORT_FILE]['name']) > 0);
                            return !$referentiel || !$csv;
                        },
                        'break_chain_on_failure' => true,
                    ],
                ],
                [
                    'name' => UploadFile::class,
                    'options' => [
                        'messages' => [
                            UploadFile::FILE_NOT_FOUND => self::VALIDATOR_MSG_FILE_NOT_FOUND,
                            UploadFile::NO_FILE => self::VALIDATOR_MSG_FILE_NOT_FOUND,
                        ],
                        'break_chain_on_failure' => true,
                    ],
                ],
                [
                    'name' => Extension::class,
                    'options' => [
                        'extension' => ['csv'],
                        'message' => self::VALIDATOR_MSG_FILE_NOT_CSV_EXTENTION,
                        'break_chain_on_failure' => true,
                    ],
                ],
                [
                    'name' => MimeType::class,
                    'options' => [
                        'mimeType' => [
                            'text/plain', 'text/x-csv', 'text/csv',
                            'application/csv', 'application/vnd.ms-excel', 'application/x-csv',
                            'text/comma-separated-values', 'text/x-comma-separated-values', 'text/tab-separated-values',
                        ],
                        'magicFile' => false,
                        'enableHeaderCheck' => true,
                        'message' => self::VALIDATOR_MSG_FILE_NOT_CSV_MIME_TYPE,
                        'break_chain_on_failure' => true,
                    ],
                ],
                [
                    'name' => Size::class,
                    'options' => [
                        'max' => '2MB',
                        'message' => self::VALIDATOR_MSG_FILE_MAX_SIZE,
                        'break_chain_on_failure' => true,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            "name" => self::INPUT_CURRENT_IMPORT,
            'required' => false,
        ]);

//        /** @var Element $groupeSelector */
//        foreach ($this->inputGroupeList as $groupeSelector) {
//            $inputFilter->add([
//                'name' => $groupeSelector->getName(),
//                'required' => false,
//                'validators' => [[
//                    'name' => Callback::class,
//                    'options' => [
//                        'messages' => [
//                            Callback::INVALID_VALUE => self::VALIDATOR_MSG_ONLY_ONE_GROUPE_PER_YEAR,
//                        ],
//                        'callback' => function ($value, $context = []) {
//                            return sizeof($value) <= 1;
//                        },
//                        'break_chain_on_failure' => true,
//                    ],
//                ]],
//            ]);
//        }
        return $this;
    }

    // Fonction qui de fixer et forcer l'import dans un groupe précis
    public function fixeGroupe(Groupe $groupe) : static
    {
        /** @var GroupeSelectPicker $input */
        $input = $this->get(self::INPUT_ADD_IN_GROUPE);
        $input->setGroupes([$groupe]);
        $input->setEmptyOption(null);
        $input->setAttribute('data-live-search', false);
        return $this;
    }

    //Permet de rester sur le bon onglet en cas d'échec d'import

    /**
     * @return string|null
     */
    public function getCurrentImportValue(): ?string
    {
        return $this->get(self::INPUT_CURRENT_IMPORT)->getValue();
    }
}