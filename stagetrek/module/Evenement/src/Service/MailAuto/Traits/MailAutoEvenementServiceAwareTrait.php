<?php
namespace Evenement\Service\MailAuto\Traits;

use Evenement\Service\MailAuto\MailAutoAffectationEvenementService;
use Evenement\Service\MailAuto\MailAutoListeEtudiantsEncadresEvenementService;
use Evenement\Service\MailAuto\MailAutoStageDebutChoixEvenementService;
use Evenement\Service\MailAuto\MailAutoStageDebutValidation;
use Evenement\Service\MailAuto\MailAutoStageRappelChoixEvenementService;
use Evenement\Service\MailAuto\MailAutoStageRappelValidationEvenementService;
use Evenement\Service\MailAuto\MailAutoStageValidationEffectueEvenementService;

trait MailAutoEvenementServiceAwareTrait
{
    /** @var MailAutoStageDebutChoixEvenementService|null  $mailAutoStageDebutChoixEvenementService*/
    protected ?MailAutoStageDebutChoixEvenementService $mailAutoStageDebutChoixEvenementService = null;
    /** @var MailAutoStageRappelChoixEvenementService|null  $mailAutoStageRappelChoixEvenementService*/
    protected ?MailAutoStageRappelChoixEvenementService $mailAutoStageRappelChoixEvenementService = null;
    /** @var MailAutoAffectationEvenementService|null  $mailAutoAffectationEvenementService*/
    protected ?MailAutoAffectationEvenementService $mailAutoAffectationEvenementService = null;
    /** @var MailAutoStageDebutValidation|null  $mailAutoStageDebutValidationService*/
    protected ?MailAutoStageDebutValidation $mailAutoStageDebutValidationService = null;
    /** @var MailAutoStageRappelValidationEvenementService|null  $mailAutoStageRappelValidationService*/
    protected ?MailAutoStageRappelValidationEvenementService $mailAutoStageRappelValidationService = null;
    /** @var MailAutoStageValidationEffectueEvenementService|null  $mailAutoStageValidationEffectueEvenementService*/
    protected ?MailAutoStageValidationEffectueEvenementService $mailAutoStageValidationEffectueEvenementService = null;

    /** @var MailAutoListeEtudiantsEncadresEvenementService|null $mailAutoListeEtudiantsEncadresEvenementService*/
    protected ?MailAutoListeEtudiantsEncadresEvenementService $mailAutoListeEtudiantsEncadresEvenementService = null;

    /**
     * @return MailAutoStageDebutValidation
     */
    public function getMailAutoStageDebutValidationService(): ?MailAutoStageDebutValidation
    {
        /** MailAutoStageDebutValidation */
        return $this->mailAutoStageDebutValidationService;
    }

    public function setMailAutoStageDebutValidationService(MailAutoStageDebutValidation $mailAutoStageDebutValidationService): static
    {
        $this->mailAutoStageDebutValidationService = $mailAutoStageDebutValidationService;
        return $this;
    }

    /**
     * @return MailAutoStageRappelValidationEvenementService
     */
    public function getMailAutoStageRappelValidationService(): ?MailAutoStageRappelValidationEvenementService
    {
        return $this->mailAutoStageRappelValidationService;
    }

    public function setMailAutoStageRappelValidationService(MailAutoStageRappelValidationEvenementService $mailAutoStageRappelValidationService): static
    {
        $this->mailAutoStageRappelValidationService = $mailAutoStageRappelValidationService;
        return $this;
    }

    /**
     * @return MailAutoStageDebutChoixEvenementService
     */
    public function getMailAutoStageDebutChoixEvenementService(): ?MailAutoStageDebutChoixEvenementService
    {
        return $this->mailAutoStageDebutChoixEvenementService;
    }

    /**
     * @return MailAutoAffectationEvenementService
     */
    public function getMailAutoAffectationEvenementService(): ?MailAutoAffectationEvenementService
    {
        return $this->mailAutoAffectationEvenementService;
    }

    public function setMailAutoAffectationEvenementService(MailAutoAffectationEvenementService $mailAutoAffectationEvenementService): static
    {
        $this->mailAutoAffectationEvenementService = $mailAutoAffectationEvenementService;
        return $this;
    }

    public function setMailAutoStageDebutChoixEvenementService(MailAutoStageDebutChoixEvenementService $mailAutoStageDebutChoixEvenementService): static
    {
        $this->mailAutoStageDebutChoixEvenementService = $mailAutoStageDebutChoixEvenementService;
        return $this;
    }

    /**
     * @return MailAutoStageRappelChoixEvenementService
     */
    public function getMailAutoStageRappelChoixEvenementService(): ?MailAutoStageRappelChoixEvenementService
    {
        return $this->mailAutoStageRappelChoixEvenementService;
    }

    public function setMailAutoStageRappelChoixEvenementService(MailAutoStageRappelChoixEvenementService $mailAutoStageRappelChoixEvenementService): static
    {
        $this->mailAutoStageRappelChoixEvenementService = $mailAutoStageRappelChoixEvenementService;
        return $this;
    }

    /**
     * @return MailAutoStageValidationEffectueEvenementService
     */
    public function getMailAutoStageValidationEffectueEvenementService(): ?MailAutoStageValidationEffectueEvenementService
    {
        return $this->mailAutoStageValidationEffectueEvenementService;
    }

    public function setMailAutoStageValidationEffectueEvenementService(MailAutoStageValidationEffectueEvenementService $mailAutoStageValidationEffectueEvenementService): static
    {
        $this->mailAutoStageValidationEffectueEvenementService = $mailAutoStageValidationEffectueEvenementService;
        return $this;
    }

    /**
     * @return MailAutoListeEtudiantsEncadresEvenementService
     */
    public function getMailAutoListeEtudiantsEncadresEvenementService(): ?MailAutoListeEtudiantsEncadresEvenementService
    {
        return $this->mailAutoListeEtudiantsEncadresEvenementService;
    }

    public function setMailAutoListeEtudiantsEncadresEvenementService(MailAutoListeEtudiantsEncadresEvenementService $mailAutoListeEtudiantsEncadresEvenementService): static
    {
        $this->mailAutoListeEtudiantsEncadresEvenementService = $mailAutoListeEtudiantsEncadresEvenementService;
        return $this;
    }

}