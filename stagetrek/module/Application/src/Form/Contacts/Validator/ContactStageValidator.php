<?php

namespace Application\Form\Contacts\Validator;

use Application\Entity\Db\Contact;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use Application\Form\Contacts\Fieldset\ContactStageFieldset;
use Application\Misc\Util;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Exception;
use Laminas\Validator\AbstractValidator;

class ContactStageValidator extends AbstractValidator implements ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    //Fonction de callback
    const ASSERT_ETUDIANT = "assertEtudiant";
    const ASSERT_SESSION = "assertSession";
    const ASSERT_CONTACT = "assertContact";

    const CALLBACK_FUNCTION_NOT_DEFIND_ERROR = 'CALLBACK_FUNCTION_NOT_DEFIND_ERROR';
    const CALLBACK_FUNCTION_NOT_IMPLEMENTED_ERROR = 'CALLBACK_FUNCTION_NOT_IMPLEMENTED_ERROR';
    const CONTACT_NOT_FOUND_ERROR = 'CONTACT_NOT_FOUND_ERROR';
    const ETUDIANT_NOT_FOUND_ERROR = 'ETUDIANT_NOT_FOUND_ERROR';
    const SESSION_NOT_FOUND_ERROR = 'SESSION_NOT_FOUND_ERROR';
    const ETUDIANT_NO_STAGE_FOR_SESSION_ERROR = 'ETUDIANT_NO_STAGE_FOR_SESSION_ERROR';
    const ETUDIANT_NO_AFFECTATION_FOR_STAGE_ERROR = 'ETUDIANT_NO_AFFECTATION_FOR_STAGE_ERROR';
    const CONTACT_STAGE_ALREADY_EXIST = 'CONTACT_STAGE_ALREADY_EXIST';
    const EXCEPTION = 'EXCEPTION';


    /**
     * Validation failure message templates definitions
     * @var array
     */
    protected $messageTemplates = [
        self::CALLBACK_FUNCTION_NOT_DEFIND_ERROR => "La fonction de validation n'a pas été founrie.",
        self::CALLBACK_FUNCTION_NOT_IMPLEMENTED_ERROR => "La fonction de validation n'a pas été définie.",
        self::CONTACT_NOT_FOUND_ERROR => "Le contact demandé n'as pas été trouvée",
        self::ETUDIANT_NOT_FOUND_ERROR => "L'étudiant".Util::POINT_MEDIANT."e demandé".Util::POINT_MEDIANT."e n'as pas été trouvé".Util::POINT_MEDIANT."e",
        self::SESSION_NOT_FOUND_ERROR => "La session de stage demandée n'as pas été trouvée",
        self::ETUDIANT_NO_STAGE_FOR_SESSION_ERROR => "L'étudiant".Util::POINT_MEDIANT."e  n'est pas inscrit".Util::POINT_MEDIANT."e  dans la session de stage demandée",
        self::ETUDIANT_NO_AFFECTATION_FOR_STAGE_ERROR => "L'étudiant".Util::POINT_MEDIANT."e  n'a pas d'affectation valide pour le stage demandé",
        self::CONTACT_STAGE_ALREADY_EXIST => "Le contact est déjà associé au stage demandé",
        self::EXCEPTION => "Une erreur est survenue : %exceptionMessage%",
    ];

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $messageVariables = [
        'exceptionMessage' => 'exceptionMessage',
    ];
    protected string $exceptionMessage = "";
    /**
     * Constructor
     *
     * @param array|callable $options
     */
    public function __construct($options = null)
    {
        if (is_callable($options)) {
            $options = ['callback' => $options];
            $options = ['modeEdition' => $options];
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
        if(!key_exists('callback', $this->getOptions())){
            $this->error(self::CALLBACK_FUNCTION_NOT_DEFIND_ERROR);
            return false;
        }
        $callback = $this->getOption('callback');
        if(!method_exists(self::class, $callback)){
            $this->error(self::CALLBACK_FUNCTION_NOT_IMPLEMENTED_ERROR);
            return false;
        }
        $this->readEntities($context);
        return $this->$callback($value, $context);
    }


    /**
     * @var Contact|null $contact;
     * @var Etudiant|null $etudiant;
     * @var SessionStage|null $sessionStage;
     * @var Stage|null $stage;
     */
    protected ?Contact $contact=null;
    protected ?Etudiant $etudiant=null;
    protected ?SessionStage $sessionStage=null;
    protected ?Stage $stage=null;

    //Lies les entités dispos depuis le context afin de les avoirs pour les différentes assertiosn (si elle existe)
    protected function readEntities($context): static
    {
        try {
            $contactId = (isset($context[ContactStageFieldset::CONTACT])) ? intval($context[ContactStageFieldset::CONTACT]) : 0;
            $this->contact = ($contactId != 0) ? $this->getObjectManager()->getRepository(Contact::class)->find($contactId) : null;
            $etudiantId = (isset($context[ContactStageFieldset::ETUDIANT_ID])) ? intval($context[ContactStageFieldset::ETUDIANT_ID]) : 0;
            $this->etudiant = ($etudiantId != 0) ? $this->getObjectManager()->getRepository(Etudiant::class)->find($etudiantId) : null;
            $sessionid = (isset($context[ContactStageFieldset::SESSION])) ? intval($context[ContactStageFieldset::SESSION]) : 0;
            $this->sessionStage = ($sessionid != 0) ? $this->getObjectManager()->getRepository(SessionStage::class)->find($sessionid) : null;
            if (isset($this->sessionStage) && isset($this->etudiant)) {
                $this->stage = $this->etudiant->getStageFor($this->sessionStage);
            }
        }
        catch (Exception $e){
            $this->exceptionMessage = $e->getCode();
        }
        return $this;

    }

    /**
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function assertContact(mixed $value, $context =[]): bool
    {
        $editMode = $this->getOption('modeEdition');
        if (!$this->contact) {
            $this->error(self::CONTACT_NOT_FOUND_ERROR);
            return false;
        }
        if(isset($this->stage) && !$editMode){
            //ON regarde si le contact est déjà associé au stage en question
            $cs = $this->contact->getContactForStage($this->stage);
            if(isset($cs)){
                $this->error(self::CONTACT_STAGE_ALREADY_EXIST);
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
    public function assertSession(mixed $value, array $context =[]): bool
    {
        if (!$this->sessionStage) {
            $this->error(self::SESSION_NOT_FOUND_ERROR);
            return false;
        }
        return true;
    }

    /**
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function assertEtudiant(mixed $value, array $context =[]): bool
    {
        if (!$this->etudiant) {
            $this->error(self::ETUDIANT_NOT_FOUND_ERROR);
            return false;
        }
        if(!isset($this->stage)){
            $this->error(self::ETUDIANT_NO_STAGE_FOR_SESSION_ERROR);
            return false;
        }
        $affectation = $this->stage->getAffectationStage();
        if(!isset($affectation) || !$affectation->hasEtatValidee()){
            $this->error(self::ETUDIANT_NO_AFFECTATION_FOR_STAGE_ERROR);
            return false;
        }
        return true;
    }
}