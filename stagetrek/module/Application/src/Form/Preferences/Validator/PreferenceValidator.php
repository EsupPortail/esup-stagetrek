<?php

namespace Application\Form\Preferences\Validator;

use Application\Entity\Db\Preference;
use Application\Entity\Db\Stage;
use Application\Entity\Db\TerrainStage;
use Application\Form\Preferences\Fieldset\PreferenceFieldset;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Validator\AbstractValidator;

/**
 * Class PreferenceValidator
 * @package Application\Form\Validator
 */
class PreferenceValidator extends AbstractValidator implements ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    //Fonction de callback
    const ASSERT_ETUDIANT_IN_SESSION = "assertEtudiantInSession";
    const ASSERT_PREFERENCE_TERRAIN_NOT_EXIST = "assertPreferenceTerrainNotExist";
    const ASSERT_TERRAIN_SECONDAIRE_ALLOWED = "assertTerrainSecondaireAllowed";

    const CALLBACK_FUNCTION_NOT_DEFIND_ERROR = 'CALLBACK_FUNCTION_NOT_DEFIND_ERROR';
    const CALLBACK_FUNCTION_NOT_IMPLEMENTED_ERROR = 'CALLBACK_FUNCTION_NOT_IMPLEMENTED_ERROR';
    const PREFERENCE_TERRAIN_EXISTE_ERROR = 'PREFERENCE_TERRAIN_EXISTE_ERROR';

    /**
     * Validation failure message templates definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::CALLBACK_FUNCTION_NOT_DEFIND_ERROR => "La fonction de validation n'a pas été founrie.",
        self::CALLBACK_FUNCTION_NOT_IMPLEMENTED_ERROR => "La fonction de validation n'a pas été définie.",
        self::PREFERENCE_TERRAIN_EXISTE_ERROR => "Une préférence pour le terrain de stage %terrainLibelle% a déjà été définie",
    ];

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $messageVariables = [
        'displayName' => 'displayName',
        'terrainLibelle' => 'terrainLibelle',
    ];
    protected string $displayName="";
    protected string $sessionLibelle="";
    protected string $terrainLibelle="";

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
    public function isValid($value, array $context = []): bool
    {
        if (!key_exists('callback', $this->getOptions())) {
            $this->error(self::CALLBACK_FUNCTION_NOT_DEFIND_ERROR);
            return false;
        }
        $callback = $this->getOption('callback');
        if (!method_exists(self::class, $callback)) {
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
    public function assertPreferenceTerrainNotExist(mixed $value, array $context = []): bool
    {
        if(!isset($value)){return false;}
        $preferenceId = (isset($context[PreferenceFieldset::ID]) && $context[PreferenceFieldset::ID] != '') ?
            intval($context[PreferenceFieldset::ID]) : 0;
        $stageId = (isset($context[PreferenceFieldset::STAGE]) && $context[PreferenceFieldset::STAGE] != '') ?
            intval($context[PreferenceFieldset::STAGE]) : null;
        $terrainId = (isset($context[PreferenceFieldset::TERRAIN_STAGE]) && $context[PreferenceFieldset::TERRAIN_STAGE] != '') ?
            intval($context[PreferenceFieldset::TERRAIN_STAGE]) : 0;

        /** @var Stage $stage */
        $stage = ($stageId != 0) ? $this->getObjectManager()->getRepository(Stage::class)->find($stageId) : null;
        if (!$stage) {
            return true;
        } //Faux mais car pas de stage

        $preferences = $stage->getPreferences();

        /** @var Preference $pref */
        foreach ($preferences as $pref) {
            if ($pref->getTerrainStage()->getId() == $terrainId && $pref->getId() != $preferenceId) {
                $this->terrainLibelle = $pref->getTerrainStage()->getLibelle();
                $this->error(self::PREFERENCE_TERRAIN_EXISTE_ERROR);
                return false;
            }
        }
        return true;
    }

    /**
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function assertTerrainSecondaireAllowed(mixed $value, array $context = []): bool
    {
        if(!isset($value)){return false;}
        $stageId = (isset($context[PreferenceFieldset::STAGE]) && $context[PreferenceFieldset::STAGE] != '') ?
            intval($context[PreferenceFieldset::STAGE]) : null;
        $terrainId = (isset($context[PreferenceFieldset::TERRAIN_STAGE]) && $context[PreferenceFieldset::TERRAIN_STAGE] != '') ?
            intval($context[PreferenceFieldset::TERRAIN_STAGE]) : null;
        $terrainSecondaireId = (isset($context[PreferenceFieldset::TERRAIN_STAGE_SECONDAIRE]) && $context[PreferenceFieldset::TERRAIN_STAGE_SECONDAIRE] != '') ?
            intval($context[PreferenceFieldset::TERRAIN_STAGE_SECONDAIRE]) : null;

        /** @var TerrainStage $terrain */
        $terrain = ($terrainId != 0) ? $this->getObjectManager()->getRepository(TerrainStage::class)->find($terrainId) : null;
        if (!$terrain) {
            return true;
        } //Faux mais car pas de terrain de stage

        /** @var Stage $stage */
        $stage = ($stageId != 0) ? $this->getObjectManager()->getRepository(Stage::class)->find($stageId) : null;
        if (!$stage) {
            return true;
        } //Faux mais car pas de stage

        /** @var TerrainStage $terrainSecondaire */
        $terrainSecondaire = ($terrainSecondaireId != 0) ? $this->getObjectManager()->getRepository(TerrainStage::class)->find($terrainSecondaireId) : null;
        if (!isset($terrainSecondaire)) {
            return true;
        }

        return true;
    }
}