<?php


namespace Application\Form\Referentiel;


use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\Source;
use Application\Form\Referentiel\Interfaces\AbstractImportEtudiantsForm;
use Application\Validator\Import\Traits\ImportValidatorTrait;
use Laminas\Filter\File\RenameUpload;
use Laminas\Form\Element\File;
use Laminas\InputFilter\FileInput;
use Laminas\Validator\Callback;
use Laminas\Validator\File\Extension;
use Laminas\Validator\File\MimeType;
use Laminas\Validator\File\Size;
use Laminas\Validator\File\UploadFile;

/**
 * Class CSVImportEtudiantsForm
 * @package Application\Form\Etudiant
 */
class CSVImportEtudiantsForm extends AbstractImportEtudiantsForm
{
    public static function getKey(): string
    {
        return Source::CSV;
    }

    public function getFormLabel(): string
    {
        return "Depuis un CSV";
    }

    public function init(): static
    {
        parent::init();
        $this->initFileInput();
        return $this;
    }

    const INPUT_IMPORT_FILE = "import_file";
    const VALIDATOR_MSG_FILE_NOT_FOUND = "Vous devez fournir un fichier au format csv.";
    const VALIDATOR_MSG_FILE_NOT_CSV_EXTENTION = "L'extension de votre fichier n'est pas correcte.";
    const VALIDATOR_MSG_FILE_NOT_CSV_MIME_TYPE = "Le format de votre fichier n'est pas correct : %type%";
    const VALIDATOR_MSG_FILE_MAX_SIZE = "Le poids maximum autorisé pour le fichier est %max%.";
    const VALIDATOR_CSV_NON_VALIDE = "Le fichier fourni contient des données non valides";
    use ImportValidatorTrait;

    private function initFileInput() : static
    {
        $this->add([
            'type' => File::class,
            'name' => self::INPUT_IMPORT_FILE,
            'options' => [
                'label' => "Fichier",
            ],
            'attributes' => [
                'id' => self::INPUT_IMPORT_FILE,
                "style" => "height:auto", //Pour forcer le css
            ],
        ]);

        $this->setInputfilterSpecification(self::INPUT_IMPORT_FILE, [
            'name' => self::INPUT_IMPORT_FILE,
            'required' => true,
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
                [
                    'name' => Callback::class,
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => self::VALIDATOR_CSV_NON_VALIDE
                        ],
                        'callback' => function ($value, $context = []) {
                            return $this->getImportValidator()->isValid($value);
                        }
                    ],
                ],
            ],
        ]);
        return $this;
    }

}