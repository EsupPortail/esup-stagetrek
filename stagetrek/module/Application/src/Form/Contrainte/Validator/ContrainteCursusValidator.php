<?php

namespace Application\Form\Contrainte\Validator;

use Application\Entity\Db\ContrainteCursusPortee;
use Application\Form\Contrainte\Fieldset\ContrainteCursusFieldset;
use Laminas\Validator\AbstractValidator;

/**
 * Class ContrainteCursusValidator
 * @package Application\Form\ContraintesCursus\Validator
 */
class ContrainteCursusValidator extends AbstractValidator
{

    //Fonction de callback
    const ASSERT_PORTEE = "assertPortee";
    const ASSERT_CATEGORIE_TERRAIN = "assertCategorieTerrain";
    const ASSERT_TERRAIN = "assertTerrain";
   const ASSERT_NB_STAGE = "assertNbStage";

    const CALLBACK_FUNCTION_NOT_DEFIND_ERROR = 'CALLBACK_FUNCTION_NOT_DEFIND_ERROR';
    const CALLBACK_FUNCTION_NOT_IMPLEMENTED_ERROR = 'CALLBACK_FUNCTION_NOT_IMPLEMENTED_ERROR';
    const CATEGORIE_TERRAIN_REQUIRED = 'CATEGORIE_TERRAIN_REQUIRED';
    const TERRAIN_REQUIRED = 'TERRAIN_REQUIRED';
    const CATEGORIE_TERRAIN_SHOULD_BE_EMPTY = 'CATEGORIE_TERRAIN_SHOULD_BE_EMPTY';
   const TERRAIN_SHOULD_BE_EMPTY  = 'TERRAIN_SHOULD_BE_EMPTY';
    const NB_STAGE_MIN_SUP_NB_STAGE_MAX  = 'NB_STAGE_MIN_SUP_NB_STAGE_MAX';
    const NO_CONTRAINTE_STAGE_ERROR  = 'NO_CONTRAINTE_STAGE_ERROR';

    /**
     * Validation failure message templates definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::CALLBACK_FUNCTION_NOT_DEFIND_ERROR => "La fonction de validation n'a pas été founrie.",
        self::CALLBACK_FUNCTION_NOT_IMPLEMENTED_ERROR => "La fonction de validation n'a pas été définie.",
        self::CATEGORIE_TERRAIN_REQUIRED => "Une catégorie de stage est requise.",
        self::TERRAIN_REQUIRED => "Un terrain de stage est requis.",
        self::CATEGORIE_TERRAIN_SHOULD_BE_EMPTY => "La contrainte ne porte pas sur les catégories de stage. Aucune catégorie de stage ne doit être sélectionnée.",
        self::TERRAIN_SHOULD_BE_EMPTY => "La contrainte ne porte pas sur les terrains de stages.  Aucun terrain de stage ne doit être sélectionné.",
        self::NB_STAGE_MIN_SUP_NB_STAGE_MAX => "Le nombre de stage(s) minimum doit être inférieur au nombre de stage(s) maximum.",
        self::NO_CONTRAINTE_STAGE_ERROR => "Un nombre de stage(s) minimum ou maximum doit être défini.",
    ];

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $messageVariables = [
        'displayName' => 'displayName',
    ];
    protected ?string $displayName = "";

    /**
     * Constructor
     *
     * @param array|callable $options
     */
    public function __construct($options = null)
    {
        if (is_callable($options)) {
            $options = ['callback' => $options];
        }

        parent::__construct($options);
    }

    /**
     * Validation
     *
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function isValid($value, $context = []){
        if(!key_exists('callback', $this->getOptions())){
            $this->error(self::CALLBACK_FUNCTION_NOT_DEFIND_ERROR);
            return false;
        }
        $callback = $this->getOption('callback');
        if(!method_exists(self::class, $callback)){
            $this->error(self::CALLBACK_FUNCTION_NOT_IMPLEMENTED_ERROR);
            return false;
        }
        return $this->$callback($value, $context);
    }

    /**
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function assertPortee($value, $context =[]){
        $portee = (isset($context[ContrainteCursusFieldset::PORTEE])) ?
            intval($context[ContrainteCursusFieldset::PORTEE]) : null;
        $noError=true;
        switch ($portee){
            case ContrainteCursusPortee::ID_PORTEE_GENERAL:
                $noError &= $this->categorieIsEmpty($context);
                $noError &= $this->terrainIsEmpty($context);
            break;
            case ContrainteCursusPortee::ID_PORTEE_CATEGORIE:
                $noError &= $this->categorieIsNotEmpty($context);
                $noError &= $this->terrainIsEmpty($context);
            break;
            case ContrainteCursusPortee::ID_PORTEE_TERRAIN:
                $noError &= $this->categorieIsEmpty($context);
                $noError &= $this->terrainIsNotEmpty($context);
            break;
        }
        return $noError;
    }

    /**
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function assertCategorieTerrain($value, $context =[]){
        $portee = (isset($context[ContrainteCursusFieldset::PORTEE])) ?
            intval($context[ContrainteCursusFieldset::PORTEE]) : null;
        if($portee == ContrainteCursusPortee::ID_PORTEE_CATEGORIE){
            return $this->categorieIsNotEmpty($context);
        }
        else{
            return $this->categorieIsEmpty($context);
        }
    }

    /**
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function assertTerrain($value, $context =[]){
        $portee = (isset($context[ContrainteCursusFieldset::PORTEE])) ?
            intval($context[ContrainteCursusFieldset::PORTEE]) : null;
        if($portee == ContrainteCursusPortee::ID_PORTEE_TERRAIN){
            return $this->terrainIsNotEmpty($context);
        }
        else{
            return $this->terrainIsEmpty($context);
        }
    }

    public function assertNbStage($value, $context =[]){
        $nbStageMin = $context[ContrainteCursusFieldset::NB_STAGE_MIN];
        $nbStageMax = $context[ContrainteCursusFieldset::NB_STAGE_MAX];
        if($nbStageMin==0 && $nbStageMax==0){
            $this->error(self::NO_CONTRAINTE_STAGE_ERROR);
            return false;
        }
        if($nbStageMin==0||$nbStageMax==0){
            return true;
        }
        if($nbStageMin > $nbStageMax){
            $this->error(self::NB_STAGE_MIN_SUP_NB_STAGE_MAX);
            return false;
        }
        return true;
    }


    //Sous-fonctions d'assertion
    protected function categorieIsEmpty($context){
        $value = (isset($context[ContrainteCursusFieldset::CATEGORIE_STAGE])) ?
            intval($context[ContrainteCursusFieldset::CATEGORIE_STAGE]) : null;
        if($value!=0){
            $this->error(self::CATEGORIE_TERRAIN_SHOULD_BE_EMPTY);
            return false;
        }
        return true;
    }
    protected function categorieIsNotEmpty($context){
        $value = (isset($context[ContrainteCursusFieldset::CATEGORIE_STAGE])) ?
            intval($context[ContrainteCursusFieldset::CATEGORIE_STAGE]) : 0;
        if($value==0){
            $this->error(self::CATEGORIE_TERRAIN_REQUIRED);
            return false;
        }
        return true;
    }
    protected function terrainIsEmpty($context){
        $value = (isset($context[ContrainteCursusFieldset::TERRAIN_STAGE])) ?
            intval($context[ContrainteCursusFieldset::TERRAIN_STAGE]) : 0;
        if($value!=0){
            $this->error(self::TERRAIN_SHOULD_BE_EMPTY);
            return false;
        }
        return true;
    }
    protected function terrainIsNotEmpty($context){
        $value = (isset($context[ContrainteCursusFieldset::TERRAIN_STAGE])) ?
            intval($context[ContrainteCursusFieldset::TERRAIN_STAGE]) : null;
        if($value==0){
            $this->error(self::TERRAIN_REQUIRED);
            return false;
        }
        return true;
    }

}