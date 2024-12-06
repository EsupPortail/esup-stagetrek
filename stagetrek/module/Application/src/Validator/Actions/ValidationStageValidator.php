<?php

namespace Application\Validator\Actions;

use Application\Entity\Db\ContactStage;
use Application\Entity\Db\Stage;
use Application\Entity\Traits\Stage\HasStageTrait;
use Application\Provider\Roles\UserProvider;
use DateTime;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\Validator\AbstractValidator;
use UnicaenApp\View\Helper\Messenger;
use UnicaenUtilisateur\Entity\Db\User;

/**
 * Class ValidationStageValidator
 * @package Application\Form\Validator
 * Validateur pour déterminer si un stage peux être validé et sinon pourquoi (utile pour gerer les liens de validation ...)
 */
class ValidationStageValidator  extends AbstractValidator implements ObjectManagerAwareInterface
{
    use ProvidesObjectManager;

    /**
     *
     * @param mixed $value
     * @param array $context //Ensembles des variables pour la validation
     * @return bool
     */
    public function isValid(mixed $value, array $context = []) : bool
    {
        $this->setData($context);
        if(!$this->hasAppUser()){
            $this->setCodeErreurValidation(self::CODE_ERREUR_APP_USER_NOT_FOUND);
            return false;
        }
        if(!$value instanceof Stage){
            $this->setCodeErreurValidation(self::CODE_ERREUR_STAGE_NOT_FOUND);
            return false;
        }
        $stage = $value;

        if (!$this->hasToken()) {
            $this->setCodeErreurValidation(self::CODE_ERREUR_LIEN_INVALIDE);
            return false;
        }
        $token = $this->getToken();

        /** @var ContactStage $contact */
        $contact = null;
        /** @var ContactStage $contactStage */
        foreach ($stage->getContactsStages() as $contactStage) {
            $tokenContact = $contactStage->getTokenValidation();
            if (isset($tokenContact) && $tokenContact !== "" &&
                strcmp($tokenContact, $token) == 0
            ) {
                $contact = $contactStage;
                break;
            }
        }
        if (!$contact) {
            $this->setCodeErreurValidation(self::CODE_ERREUR_CONTACT_TOKEN_NOT_FOUND);
            return false;
        }

        $today = new DateTime();

        switch (true){
            case $stage->hasEtatFutur():
            case $stage->hasEtatPhasePreferences():
            case $stage->hasEtatPhaseAffectation():
            case $stage->hasEtatAVenir():
                $this->setCodeErreurValidation(self::CODE_ERREUR_STAGE_ETAT_VALIDATION_NOT_ALLOWED);
                return false;
            case $stage->hasEtatValide():
            case $stage->hasEtatNonValide():
                $this->setCodeErreurValidation(self::CODE_ERREUR_STAGE_TERMINEE);
                return false;
            case $stage->hasEtatEnErreur():
            case $stage->hasEtatEnAlerte():
                $this->setCodeErreurValidation(self::CODE_ERREUR_STAGE_IN_ERROR);
                return false;

            case $stage->hasEtatDesactive(): // car si le stage est désactivé de maniére temporaire on autorise quand même la validation
            case $stage->hasEtatEnDisponibilite():
            case $stage->hasEtatEnCours():
            case $stage->hasEtatPhaseValidation():
            case $stage->hasEtatPhaseEvaluation():
            case $stage->hasEtatValidationEnRetard():
            case $stage->hasEtatEvaluationEnRetard():
                break;

            default : // état indéterminée
                $this->setCodeErreurValidation(self::CODE_ERREUR_STAGE_IN_ERROR);
                return false;
        }

//        Est-ce que le token de validation est toujours bon
        if ($contact->getTokenExpirationDate() <= $today) {
            $this->setCodeErreurValidation(self::CODE_ERREUR_TOKEN_EXPIRE);
            return false;
        }

        $affection = $stage->getAffectationStage();
        if (!$affection || !$affection->hasEtatValidee()) {
            $this->setCodeErreurValidation(self::CODE_ERREUR_AFFECTATION_NON_VALIDEE);
            return false;
        }
        $validation = $stage->getValidationStage();
        if (!isset($validation)) {
            $this->setCodeErreurValidation(self::CODE_ERREUR_VALIDATION_NOT_FOUND);
            return false;
        }
        if ($today <= $stage->getDateDebutValidation()) {
            $this->setCodeErreurValidation(self::CODE_ERREUR_VALIDATION_NOT_OPEN);
            return false;
        }
        if ($validation->validationEffectue() && $stage->getDateFinValidation() <= $today) {
            $this->setCodeErreurValidation(self::CODE_ERREUR_STAGE_TERMINEE);
            return false;
        }
        //Vérification sur le contact
        if (!$contact->isActif()) {
            $this->setCodeErreurValidation(self::CODE_ERREUR_CONTACT_INACTIF);
            return false;
        }
        if (!$contact->canValiderStage()) {
            $this->setCodeErreurValidation(self::CODE_ERREUR_CONTACT_VALIDATION_NOT_ALLOWED);
            return false;
        }
        $this->setCodeErreurValidation(self::CODE_VALIDATION_VALIDE);
        return true;
    }

    /*******************************
     ** Sous-fonctions d'assertion **
     *******************************/

    //Pour gerer les causes d'echec de la validation par stage spécifiquement avec de multiples cas
    const CODE_VALIDATION_VALIDE = "Validation-autorisee";
    const CODE_ERREUR_CONFIGURATION = "E-501";
    const CODE_ERREUR_INCONNU = "E-404";
    const CODE_ERREUR_APP_USER_NOT_FOUND = "U-404";
    const CODE_ERREUR_STAGE_NOT_FOUND = "S-404";
    const CODE_ERREUR_LIEN_INVALIDE = "E-403";
    const CODE_ERREUR_STAGE_ETAT_VALIDATION_NOT_ALLOWED = "E-S01";
    const CODE_ERREUR_STAGE_TERMINEE = "E-S02";
    const CODE_ERREUR_STAGE_IN_ERROR = "E-S403";
    const CODE_ERREUR_AFFECTATION_NON_VALIDEE = "E-A01";
    const CODE_ERREUR_VALIDATION_NOT_OPEN = "E-V01";
    const CODE_ERREUR_VALIDATION_TERMINEE = "E-V02";
    const CODE_ERREUR_VALIDATION_NOT_FOUND = "E-V404";
    const CODE_ERREUR_TOKEN_EXPIRE = "E-C01";
    const CODE_ERREUR_CONTACT_INACTIF = "E-C02";
    const CODE_ERREUR_CONTACT_VALIDATION_NOT_ALLOWED = "E-C403";
    const CODE_ERREUR_CONTACT_TOKEN_NOT_FOUND = "E-C404";

    protected ?string $codeErreurValidation = null;

    public function setCodeErreurValidation(?string $code) : static
    {
        $this->codeErreurValidation = $code;
        return $this;
    }

    public function getCodeErreurValidation() : ?string
    {
        return $this->codeErreurValidation;
    }

    public function getValidationNotAllowedMessage(array $data = []) : string
    {
        $codeErreur = ($this->getCodeErreurValidation()) ?? self::CODE_ERREUR_INCONNU;
        switch ($codeErreur) {
            case self::CODE_VALIDATION_VALIDE:
                $msg = "";
                break;
            case self::CODE_ERREUR_VALIDATION_NOT_OPEN:
                $msg = "<b>La phase de validation du stage n'est pas encore commencé.</b>";
                /** @var Stage $stage */
                $stage = ($data['stage']) ?? null;
                if ($stage) {
                    $msg .= "<div><ul>";
                    $msg .= sprintf("<li>Début de la phase de validation le %s</li>", $stage->getDateDebutValidation()->format('d/m/Y'));
                    $msg .= sprintf("<li>Fin de la phase de validation le %s</li>", $stage->getDateFinValidation()->format('d/m/Y'));
                    $msg .= "</ul></div>";
                }
                break;
            case self::CODE_ERREUR_STAGE_TERMINEE:
            case self::CODE_ERREUR_VALIDATION_TERMINEE:
                $msg = "La validation du stage a déjà été effectuée.";
                /** @var Stage $stage */
                $stage = ($data['stage']) ?? null;
                $validation = ($stage) ? $stage->getValidationStage() : null;
                if ($validation) {
                    $validePar = $validation->getValidateBy();
                    $dateValidation = $validation->getDateValidation();
                    $valide = $validation->isValide();
                    $invalide = $validation->isInvalide();
                    $msg .= "<div><ul>";
                    if ($validePar && $validePar != "") {
                        $msg .= sprintf("<li>Validation effectuée par %s</li>", $validePar);
                    }
                    if ($dateValidation && $dateValidation != "") {
                        $msg .= sprintf("<li>Le %s à %s</li>",
                            $dateValidation->format('d/m/Y'),
                            $dateValidation->format('H\hi')
                        );
                    }
                    $msg .= sprintf("<li>Etat : %s</li>",
                        ($valide) ? "Validé" : (($invalide) ? "Invalidé" : "Indéterminé")
                    );
                    $msg .= "</ul></div>";

                }
                break;
            case self::CODE_ERREUR_STAGE_NOT_FOUND:
            case self::CODE_ERREUR_LIEN_INVALIDE:
                $msg = "<b>Le lien utilisé n'est pas valide.</b>";
                break;
            case self::CODE_ERREUR_CONTACT_TOKEN_NOT_FOUND:
                $msg = "<b>Le lien utilisé n'est pas valide.</b>";
                $msg .= "<div>Il est possible que vous ayez reçu un nouveau lien de validation ou que celui-ci soit arrivé à expiration.</div>";
                $msg .= sprintf("<div><i>Vous pouvez demander un nouveau lien de validation en contactant %s.</i></div>",$this->getAppUser()->getEmail());
                break;
            case self::CODE_ERREUR_TOKEN_EXPIRE:
                $msg = "<b>Votre lien de validation est arrivé à expiration.</b>";
                /** @var Stage $stage */
                $stage = ($data['stage']) ?? null;
                $validation = ($stage) ? $stage->getValidationStage() : null;
                if ($validation && !$validation->validationEffectue()) {
                    $msg .=  sprintf("<div><i>Vous pouvez demander un nouveau lien de validation en contactant par mail %s.</i></div>", $this->getAppUser()->getEmail());
                }
                break;
            case self::CODE_ERREUR_CONTACT_VALIDATION_NOT_ALLOWED:
            case self::CODE_ERREUR_CONTACT_INACTIF:
                $msg = "<b>Vous n'êtes pas autorisé à procéder à la validation du stage demandé.</b>";
                break;
            case self::CODE_ERREUR_STAGE_ETAT_VALIDATION_NOT_ALLOWED:
            case self::CODE_ERREUR_AFFECTATION_NON_VALIDEE:
            case self::CODE_ERREUR_VALIDATION_NOT_FOUND:
            case self::CODE_ERREUR_STAGE_IN_ERROR:
                $msg = "<b>Validation indisponible</b> : ".$codeErreur;
                $msg .= "<div>La validation du stage demandé est temporairement indisponible. Merci de réessayer ultérieurement.</div>";
                break;
            case self::CODE_ERREUR_CONFIGURATION:
            case self::CODE_ERREUR_APP_USER_NOT_FOUND:
            case self::CODE_ERREUR_INCONNU:
            default :
                $msg = "<b>Une erreur est survenue</b>";
                $msg .= "<div>Suite à une erreur technique, la validation du stage est temporairement indsponible.</div>";
                break;
        }
        return $msg;
    }

    public function getValidationNotAllowedMessagePriority(): string
    {
        $codeErreur = ($this->getCodeErreurValidation()) ?? self::CODE_ERREUR_INCONNU;
        return match ($codeErreur) {
            self::CODE_VALIDATION_VALIDE => "",
            self::CODE_ERREUR_VALIDATION_TERMINEE, self::CODE_ERREUR_STAGE_TERMINEE
                => Messenger::SUCCESS,
            self::CODE_ERREUR_VALIDATION_NOT_OPEN => Messenger::INFO,
            self::CODE_ERREUR_STAGE_ETAT_VALIDATION_NOT_ALLOWED, self::CODE_ERREUR_STAGE_IN_ERROR, self::CODE_ERREUR_AFFECTATION_NON_VALIDEE, self::CODE_ERREUR_VALIDATION_NOT_FOUND, self::CODE_ERREUR_CONTACT_TOKEN_NOT_FOUND, self::CODE_ERREUR_TOKEN_EXPIRE, self::CODE_ERREUR_CONTACT_INACTIF, self::CODE_ERREUR_CONTACT_VALIDATION_NOT_ALLOWED
                => Messenger::WARNING,
            default => Messenger::ERROR,
        };
    }

    public function getSendMailErreurValidation() : bool
    {
        $codeErreur = ($this->getCodeErreurValidation()) ?? self::CODE_ERREUR_INCONNU;
        return match ($codeErreur) {
            self::CODE_ERREUR_VALIDATION_TERMINEE, self::CODE_ERREUR_STAGE_TERMINEE,
            self::CODE_ERREUR_STAGE_ETAT_VALIDATION_NOT_ALLOWED,
            self::CODE_VALIDATION_VALIDE, self::CODE_ERREUR_LIEN_INVALIDE,
            self::CODE_ERREUR_CONTACT_TOKEN_NOT_FOUND, self::CODE_ERREUR_VALIDATION_NOT_OPEN,
            self::CODE_ERREUR_TOKEN_EXPIRE, self::CODE_ERREUR_CONTACT_INACTIF => false,
            default => true,
        };
    }

    use HasStageTrait;

    public function setData(array $data = []) : static
    {
        $this->setStage($data['stage'] ?? null);
        $this->setToken($data['token'] ?? null);
        $appUser = $this->getObjectManager()->getRepository(User::class)->findOneBy(['username' => UserProvider::APP_USER]);
        $this->setAppUser($appUser);

        return $this;
    }

    /**
     * @var ?string $token
     */
    protected ?string $token = null;

    /**
     * @return string|null
     */
    public function getToken() : ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     * @return ValidationStageValidator
     */
    public function setToken(?string $token): static
    {
        $this->token = $token;
        return $this;
    }

    protected function hasToken() : bool
    {
        return ($this->token !== null);
    }

    protected ?User $appUser = null;
    public function getAppUser(): ?User
    {
        return $this->appUser;
    }

    public function setAppUser(?User $appUser): static
    {
        $this->appUser = $appUser;
        return $this;
    }

    public function hasAppUser(): bool
    {
        return $this->appUser !== null;
    }

}
