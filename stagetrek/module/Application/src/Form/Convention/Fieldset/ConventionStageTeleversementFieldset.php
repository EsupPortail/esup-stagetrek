<?php

namespace Application\Form\Convention\Fieldset;

use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Laminas\Form\Element\File;
use Laminas\InputFilter\FileInput;
use Laminas\Validator\File\Extension;
use Laminas\Validator\File\MimeType;
use Laminas\Validator\File\Size;
use Laminas\Validator\File\UploadFile;

class ConventionStageTeleversementFieldset extends AbstractEntityFieldset
{
    const INPUT_FILE = 'file';
    public function init(): void
    {
        $this->add([
            'type' => File::class,
            'name' => self::INPUT_FILE,
            'options' => [
                'label' => "Selectionner le fichier",
            ],
            'attributes' => [
                'id' => self::INPUT_FILE,
            ],
        ]);

        $validators = [];
        $config = $this->fileConfig;
        $maxSize = (isset($config['maxSize']) && $config['maxSize'] != "") ? $config['maxSize'] : "2MB";
        $extentions = ['pdf']; //indépendament de la config, les conventions sont obligatoriement des pdf
        $mimeType = ['application/pdf']; //indépendament de la config, les conventions sont obligatoriement des pdf


        $this->setInputfilterSpecification(self::INPUT_FILE,[
            'name' => self::INPUT_FILE,
            'required' => true,
            'type' => FileInput::class,
            'filters' => [],
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
                        'extension' => $extentions,
                        'message' => self::VALIDATOR_MSG_FILE_NOT_PDF_EXTENTION,
                        'break_chain_on_failure' => true,
                    ],
                ],
                [
                    'name' => MimeType::class,
                    'options' => [
                        'mimeType' =>$mimeType,
                        'magicFile' => false,
                        'enableHeaderCheck' => true,
                        'message' => self::VALIDATOR_MSG_FILE_NOT_PDF_MIME_TYPE,
                        'break_chain_on_failure' => true,
                    ],
                ],
                [
                    'name' => Size::class,
                    'options' => [
                        'max' => $maxSize,
                        'message' => self::VALIDATOR_MSG_FILE_MAX_SIZE,
                        'break_chain_on_failure' => true,
                    ],
                ],
            ],
        ]);
    }

    const VALIDATOR_MSG_FILE_NOT_FOUND = "Vous devez fournir un fichier au format pdf.";
    const VALIDATOR_MSG_FILE_NOT_PDF_EXTENTION = "Seul les fichiers pdf sont autorisé.";
    const VALIDATOR_MSG_FILE_NOT_PDF_MIME_TYPE = "Le format de votre fichier n'est pas correct : %type%";
    const VALIDATOR_MSG_FILE_MAX_SIZE = "Le poids maximum autorisé pour le fichier est %max%.";

    /**
     * @var array $fileConfig
     * @desc permet de récupérer les variables de config de UnicaenFichier qui sont normalement définie pour un formulaire et nom pour un fieldset
     */
    protected array $fileConfig = [];

    public function getFileConfig(): array
    {
        return $this->fileConfig;
    }

    public function setFileConfig(array $fileConfig): void
    {
        $this->fileConfig = $fileConfig;
    }


}
