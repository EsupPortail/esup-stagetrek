<?php

namespace Fichier\Form\Upload;

use Fichier\Service\Nature\NatureServiceAwareTrait;
use Laminas\Filter\File\RenameUpload;
use Laminas\Filter\ToInt;
use Laminas\Filter\ToNull;
use Laminas\Form\Element\Button;
use Laminas\Form\Element\File;
use Laminas\Form\Element\Select;
use Laminas\Form\Form;
use Laminas\InputFilter\FileInput;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\Callback;
use Laminas\Validator\File\Extension;
use Laminas\Validator\File\MimeType;
use Laminas\Validator\File\Size;
use Laminas\Validator\File\UploadFile;

class UploadForm extends Form
    implements
    InputFilterProviderInterface
{
    use NatureServiceAwareTrait;

    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);
        $this->setAttribute('method', 'post');
//       validateur par défaut
        $this->filesValidators = [];
        $this->filesValidators[] = [
            'name' => UploadFile::class,
            'options' => [
                'messages' => [
                    UploadFile::FILE_NOT_FOUND => "Le fichier n'as pas été trouvé",
                    UploadFile::NO_FILE => self::FILE_NOT_FOUND_ERROR,
                ],
                'break_chain_on_failure' => true,
            ],
        ];
    }

    public function init() : void
    {
        //upload
        $this->add([
            'type' => File::class,
            'name' => 'fichier',
            'options' => [
                'label' => 'Fichier à téléverser',
            ],
        ]);
        //nature
        $this->add([
            'type' => Select::class,
            'name' => 'nature',
            'options' => [
                'label' => "Nature du fichier",
                'value_options' => $this->getNatureService()->getNaturesAsOptions(),
            ],
            'attributes' => [
                'id'                => 'nature',
                'class'             => 'bootstrap-selectpicker show-tick',
                'data-live-search'  => 'true',
            ],
        ]);
        $validators = $this->getFileValidators();
        $this->setInputfilterSpecification('fichier',[
            'name' => 'fichier',
            'required' => true,
            'type' => FileInput::class,
            'validators' => $validators
        ]);

        $this->setInputfilterSpecification('nature',[
            'name' => 'nature',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
                ['name' => ToNull::class],
            ],
            'validators' => [
                [
                    'name' => Callback::class,
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => "La nature de fichier n'est pas valide",
                        ],
                        'callback' => function ($value, $context = []) {
                            if(!isset($value)){return false;}
                            $nature = $this->getNatureService()->getNature(intval($value));
                            return isset($nature);
                        }
                    ],
                ]
            ]
        ]);

        //button
        $this->add([
            'type' => Button::class,
            'name' => 'creer',
            'options' => [
                'label' => '<i class="fas fa-save"></i> Téléverser le fichier',
                'label_options' => [
                    'disable_html_escape' => true,
                ],
            ],
            'attributes' => [
                'type' => 'submit',
                'class' => 'btn btn-primary',
            ],
        ]);
    }

    /** @var array $inputFilterSpecification */
    protected array $inputFilterSpecification = [];
    /**
     * @return array
     */
    public function getInputFilterSpecification(): array
    {
        return $this->inputFilterSpecification;
    }

    public function setInputfilterSpecification(string $inputId, array $specification) : static
    {
        $this->inputFilterSpecification[$inputId] = $specification;
        return $this;
    }

    const FILE_NOT_FOUND_ERROR = "Vous devez fournir un fichier au format pdf.";
    const FILE_MAX_SIZE_ERROR = "Le poids maximum autorisé pour le fichier est %max%.";
    const FILE_EXTENTION_ERROR = "Le fichier fournis n'a pas une extention autorisé.";
    const FILE_TYPE_MINE_ERROR = "Le fichier fournis n'est pas d'un type attendu.";

    protected array $filesValidators = [];
    protected function getFileValidators() : array
    {
        return $this->filesValidators;
    }

    /** @var string|null  */
    protected ?string $maxSize=null;
    public function getMaxSize(): ?string
    {
        return $this->maxSize;
    }
    public function setMaxSize(?string $maxSize): void
    {
        if($maxSize==""){$maxSize=null;}
        $this->maxSize = $maxSize;
        if(!isset($maxSize)){
            unset($this->filesValidators['maxSize']);
        }
        else{
            $this->filesValidators['maxSize'] =[
                'name' => Size::class,
                'options' => [
                    'max' => $this->maxSize,
                    'message' => self::FILE_MAX_SIZE_ERROR,
                    'break_chain_on_failure' => true,
                ],
            ];
        }
    }

    protected array $allowedExtensions = [];
    public function getAllowedExtensions(): array
    {
        return $this->allowedExtensions;
    }
    public function setAllowedExtensions(array $allowedExtensions): void
    {
        $this->allowedExtensions = $allowedExtensions;
        if(empty($allowedExtensions)){
            unset($this->filesValidators['extensions']);
        }
        else{
            $this->filesValidators['extensions'] =[
                'name' => Extension::class,
                'options' => [
                    'extension' => $allowedExtensions,
                    'message' => self::FILE_EXTENTION_ERROR,
                    'break_chain_on_failure' => true,
                ],
            ];
        }
    }
    public function addAllowedExtention(string $ext): void
    {
        if(!isset($this->allowedExtensions)){$this->allowedExtensions = [];}
        $this->allowedExtensions[] = $ext;
        unset($this->filesValidators['extensions']);
        $this->filesValidators['extensions'] = [
            'name' => Extension::class,
            'options' => [
                'extension' => $this->allowedExtensions,
                'message' => self::FILE_EXTENTION_ERROR,
                'break_chain_on_failure' => true,
            ],
        ];
    }

    protected array $allowedTypeMime = [];
    public function getAllowedTypeMime(): array
    {
        return $this->allowedTypeMime;
    }
    public function setAllowedTypeMime(array $allowedTypeMime): void
    {
        $this->allowedTypeMime = $allowedTypeMime;
        if(!empty($typeMime)){
            unset($this->filesValidators['typeMime']);
        }
        else{
            $this->filesValidators['typeMime'] = [
                'name' => MimeType::class,
                'options' => [
                    'mimeType' => $allowedTypeMime,
                    'magicFile' => false,
                    'enableHeaderCheck' => true,
                    'message' => self::FILE_TYPE_MINE_ERROR,
                    'break_chain_on_failure' => true,
                ],
            ];
        }
    }

    public function addAllowedTypeMime(string $typeMime): void
    {
        if(!isset($this->allowedTypeMime)){$this->allowedTypeMime = [];}
        $this->allowedTypeMime[] = $typeMime;
        unset($this->filesValidators['typeMime']);
        $this->filesValidators['typeMime'] = [
            'name' => MimeType::class,
            'options' => [
                'mimeType' => $this->allowedTypeMime,
                'magicFile' => false,
                'enableHeaderCheck' => true,
                'message' => self::FILE_TYPE_MINE_ERROR,
                'break_chain_on_failure' => true,
            ],
        ];
    }

}