<?php

namespace Application\Form\Misc;

use Application\Form\Abstrait\Traits\FormElementTrait;
use Laminas\Filter\File\RenameUpload;
use Laminas\Form\Element\Button;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\File;
use Laminas\Form\Form;
use Laminas\InputFilter\FileInput;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\File\Extension;
use Laminas\Validator\File\MimeType;
use Laminas\Validator\File\Size;
use Laminas\Validator\File\UploadFile;

/**
 * Class ImportForm
 * @package Application\Form\Default
 * @author Thibaut Vallée <thibaut.vallee at unicaen.fr>
 */
class ImportForm extends Form
{
    const INVALIDE_ERROR_MESSAGE = "Le formulaire n'est pas valide :";
    use  FormElementTrait;

    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'loadingForm');
    }

    public function init() : static
    {
        parent::init();
        $this->setAttribute("action", $this->getCurrentUrl());
        $this->setAttribute('method', 'post');
        $this->initSubmitInput();
        $this->initFileInput();
        return $this;
    }


    const INPUT_SUBMIT = 'submit';
    const CSRF ="csrf";
    protected function initSubmitInput() : static
    {
        $this->add([
            'type' => Button::class,
            'name' => self::INPUT_SUBMIT,
            'options' => [
                'label' => '<span class="icon icon-import"></span> Importer',
                'label_options' => [
                    'disable_html_escape' => true,
                ],
            ],
            'attributes' => [
                'id' =>  self::INPUT_SUBMIT,
                'type' => 'submit',
                'class' => 'btn btn-primary',
            ],
        ]);

        $this->add(new Csrf(self::CSRF));
        return $this;
    }

    const INPUT_IMPORT_FILE = 'import_file';
    const VALIDATOR_MSG_FILE_NOT_FOUND = "Vous devez fournir un fichier au format csv.";
    const VALIDATOR_MSG_FILE_NOT_CSV_EXTENTION = "L'extension de votre fichier n'est pas correcte.";
    const VALIDATOR_MSG_FILE_NOT_CSV_MIME_TYPE = "Le format de votre fichier n'est pas correct : %type%";
    const VALIDATOR_MSG_FILE_MAX_SIZE = "Le poids maximum autorisé pour le fichier est %max%.";
    protected function initFileInput() : static
    {
        $this->add([
            'type' => File::class,
            'name' => self::INPUT_IMPORT_FILE,
            'options' => [
                'label' => 'Importer depuis un CSV',
            ],
            'attributes' => [
                'id' => self::INPUT_IMPORT_FILE,
                "style" => "height:auto", //Pour forcer le css
            ],
        ]);

        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

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
        ], self::INPUT_IMPORT_FILE);
        return $this;
    }

}