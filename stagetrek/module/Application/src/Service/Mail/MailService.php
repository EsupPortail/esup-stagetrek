<?php

namespace Application\Service\Mail;

use Application\Entity\Db\AffectationStage;
use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\CategorieStage;
use Application\Entity\Db\ContactStage;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use Application\Entity\Db\TerrainStage;
use Application\Provider\Mailing\CodesMailsProvider;
use Application\Service\Parametre\Traits\ParametreServiceAwareTrait;
use Application\Service\Renderer\MacroService;
use DateTime;
use Exception;
use UnicaenMail\Entity\Db\Mail;
use UnicaenRenderer\Service\Macro\MacroServiceAwareTrait;
use UnicaenRenderer\Service\Template\TemplateServiceAwareTrait;

/** Surcouche du module MailService pour gerer les cas spécifique de l'application */
class MailService extends \UnicaenMail\Service\Mail\MailService
{
    //Liens vers les templates
    use TemplateServiceAwareTrait;
    use MacroServiceAwareTrait;

    /*************************************************
     * Envoye des mails type
     ************************************************/
    /**
     * @param string $codeMail
     * @param array $data
     * @return Mail
     * @throws \Exception
     */
    public function sendMailType(string $codeMail, array $data = []): Mail
    {
        $templateService = $this->getTemplateService();
        /** @var \Application\Service\Renderer\MacroService $macroService */
        $macroService = $this->getMacroService();
        //Securité : on vérifie que l'on peut bien envoyer le mail type demandé (action a effectuer logiquement en amont mais mise également ici par sécurité pour ne pas faire d'erreur)
        $this->canSendMailType($codeMail, $data);

        $template = $templateService->getTemplateByCode($codeMail);
        $macroData = $this->getMailTypeMacroVariables($codeMail, $data);
        $to = $this->getMailTypeDestinataires($codeMail, $data);
        $sujet = $template->getSujet();
        $corps = $template->getCorps();
        $sujet = $macroService->replaceMacros($sujet, $macroData);
        $corps = $macroService->replaceMacros($corps, $macroData);

        if ($macroService->textContainsMacro($sujet) || $macroService->textContainsMacro($corps)) {
            throw new Exception(sprintf("Impossible d'envoyer le mail type %s, toutes les macros n'ont pas été remplacées.", $codeMail));
        }
        $mail = $this->sendMail($to, $sujet, $corps);
        $motsClef = $this->getMailTypeKeyWords($codeMail, $data);
        $mail->setMotsClefs($motsClef);
        $this->update($mail);
        return $mail;
    }

    /**
     * Gestions de l'envoie des mails types
     */
    use ParametreServiceAwareTrait;

    /**
     * @param string $code
     * @param array $data
     * @return array
     */
    public function getMailTypeDestinataires(string $code, array $data = []): array
    {
        $destinataires = [];
        switch ($code) {
            case CodesMailsProvider::STAGE_DEBUT_CHOIX:
            case CodesMailsProvider::STAGE_DEBUT_CHOIX_RAPPEL:
            case CodesMailsProvider::AFFECTATION_STAGE_VALIDEE:
            case CodesMailsProvider::VALIDATION_STAGE_EFFECTUEE:
                /** @var Stage $stage */
                $stage = ($data['stage']) ?? null;
                $etudiant = ($stage && $stage->getEtudiant()) ? $stage->getEtudiant() : null;
                if (!$etudiant) break;
                $destinataires[] = $etudiant->getEmail();
                break;
            case CodesMailsProvider::VALIDATION_STAGE:
            case CodesMailsProvider::VAlIDATION_STAGE_RAPPEL:
                /** @var ContactStage $contactStage */
                $contactStage = ($data['contactStage']) ?? null;
                if (!$contactStage) break;
                $destinataires[] = $contactStage->getEmail();
                break;
        }
        return $destinataires;
    }

    public function getMailTypeKeyWords(string $codeMail, array $data = []): array
    {
        $motsClef[] = sprintf("MailType=%s", $codeMail);
        /** @var Stage $stage */
        $stage = ($data['stage']) ?? null;
        /** @var ContactStage $contactStage */
        $contactStage = ($data['contactStage']) ?? null;
        $session = ($data['sessionStage']) ?? null;
        if(!isset($session) && isset($stage)){
            $session = $stage->getSessionStage();
        }
        switch ($codeMail) {//A priori pas de différences pour les mails types mais celà pourrais changer
            case CodesMailsProvider::STAGE_DEBUT_CHOIX:
            case CodesMailsProvider::STAGE_DEBUT_CHOIX_RAPPEL:
            case CodesMailsProvider::AFFECTATION_STAGE_VALIDEE:
            case CodesMailsProvider::VALIDATION_STAGE:
            case CodesMailsProvider::VAlIDATION_STAGE_RAPPEL:
            case CodesMailsProvider::VALIDATION_STAGE_EFFECTUEE:
                $motsClef[] = sprintf("StageId=%s", (isset($stage)) ? $stage->getId() : '-1');
                $motsClef[] = sprintf("SessionStageId=%s", (isset($session)) ? $session->getId() : '-1');
                $motsClef[] = sprintf("ContactStageId=%s", (isset($contactStage)) ? $contactStage->getId() : '-1');
                break;
        }
        return $motsClef;
    }

    public function getMailTypeMacroVariables(string $code, array $data = []): array
    {
        /** @var MacroService $macroService */
        $macroService = $this->getMacroService();
        $variables = [];
        //On met le maximum de donnée possible
        /** @var Stage $stage */
        $stage = ($data['stage']) ?? null;
        /** @var Etudiant $etudiant */
        $etudiant = ($data['etudiant']) ?? null;
        /** @var SessionStage $session */
        $session = ($data['session']) ?? null;
        /** @var AffectationStage $affectation */
        $affectation = ($data['affectationStage']) ?? null;
        /** @var AnneeUniversitaire $annee */
        $annee = ($data['anneeUniversitaire']) ?? null;
        /** @var ContactStage $contactStage */
        $contactStage = ($data['contactStage']) ?? null;

        if(isset($stage) && !isset($etudiant)){
            $etudiant = $stage->getEtudiant();
        }
        if(isset($stage) && !isset($session)){
            $session = $stage->getSessionStage();
        }
        if(isset($stage) && !isset($affectation)){
            $affectation = $stage->getAffectationStage();
        }
        if(isset($stage) && !isset($annee)){
            $annee = $stage->getAnneeUniversitaire();
        }
        elseif(isset($session) && !isset($annee)){
            $annee = $session->getAnneeUniversitaire();
        }

        /** @var TerrainStage $terrain */
        if(isset($stage)) {
            $terrain = (isset($affectation)) ? $stage->getTerrainStage() : null;
        }
        /** @var CategorieStage $categorieTerrain */
        $categorieStage =  (isset($terrain)) ? $terrain->getCategorieStage() : null;

        $dataUrlService = [];
        $dataDateService = [];
        if(isset($stage)){
            $dataUrlService['stage'] = $stage;
            $dataDateService['stage'] = $stage;
        }
        if(isset($contactStage)){
            $dataUrlService['contactStage'] = $contactStage;
        }
        if(isset($etudiant)){
            $dataDateService['etudiant'] = $etudiant;
        }
        if(isset($session)){
            $dataDateService['session'] = $session;
        }
        if(isset($annee)){
            $dataDateService['anneeUniversitaire'] = $annee;
        }
        $macroService->getUrlService()->setVariables($dataUrlService);
        $macroService->getDateRendererService()->setVariables($dataDateService);

        if(isset($stage)) $variables['stage'] = $stage;
        if(isset($etudiant)) $variables['etudiant'] = $etudiant;
        if(isset($session)) $variables['session'] = $session;
        if(isset($annee)) $variables['anneeUniversitaire'] = $annee;
        if(isset($affectation)) $variables['affectationStage'] = $affectation;
        if(isset($terrain)) $variables['terrain'] = $terrain;
        if(isset($categorieStage)) $variables['categorieStage'] = $categorieStage;

        $variables['urlService'] = $macroService->getUrlService();
        $variables['dateRendererService'] = $macroService->getDateRendererService();
        $variables['parametreRendererService'] = $macroService->getParametreRendererService();

        return $variables;
    }

    // détermine si un mail type correspondant aux données fournis à déjà été envoyée
    public function mailTypeSended($codeMail, $data): bool
    {
        $keywordList = $this->getMailTypeKeyWords($codeMail, $data);
        $motsClef = implode(Mail::MOTCLEF_SEPARATEUR, $keywordList);
        $mailValidationSend = $this->getObjectManager()->getRepository(Mail::class)->findOneBy(['motsClefs' => $motsClef]);
        return isset($mailValidationSend);
    }

    /**
     * Mails envoyée automatiquement pour signaler l'affectation du stage
     * @return Stage[]
     */
    public function getStagesForMailAutoAffectation(): array
    {
        $today = new DateTime();
        //Pour les tests :
        $today->setDate(2021, 8, 29);

        $stagesToSend = [];
        /** @var SessionStage[] $sessions */
        $sessions = $this->getObjectManager()->getRepository(SessionStage::class)->findAll();
        foreach ($sessions as $session) {
            //L'année doit être verrouillée
            if (!$session->getAnneeUniversitaire()->isAnneeVerrouillee()) {
                continue;
            }
            //L'état est valide
            if (!$session->hasEtatAVenir()) {
                continue;
            }
            if ($today < $session->getDateFinCommission() || $session->getDateDebutStage() < $today) {
                continue;
            }
            /** @var Stage $stage */
            foreach ($session->getStages() as $stage) {
                // État du stage valide
                if (!$stage->hasEtatAVenir()) {
                    continue;
                }
                //Date valide -- Test a priori inutile car si les dates ne sont pas bonne le État ne l'est pas n'ont plus plus le fait que la vérification est faite également au niveau de la session
                if ($today < $session->getDateFinCommission() || $stage->getDateDebutStage() < $today) {
                    continue;
                }
                //L'affectation du stage est validée par la commission
                if (!$stage->getAffectationStage() || !$stage->getAffectationStage()->hasEtatValidee()) {
                    continue;
                }
                //Le mail n'as pas déjà été envoyée
                if ($this->mailTypeSended(CodesMailsProvider::AFFECTATION_STAGE_VALIDEE, ['stage' => $stage])) {
                    continue;
                }
                $stagesToSend[$stage->getId()] = $stage;
            }
        }
        return $stagesToSend;
    }

    /**
     * @return Stage[]
     */
    public function getStagesForMailAutoValidation(): array
    {
        $today = new DateTime();

        $stagesToSend = [];
        /** @var SessionStage[] $sessions */
        $sessions = $this->getObjectManager()->getRepository(SessionStage::class)->findAll();
        foreach ($sessions as $session) {
            //L'année doit être verrouillée
            if (!$session->getAnneeUniversitaire()->isAnneeVerrouillee()) {
                continue;
            }
            //Le statut autorise la validation
            if (!($session->hasEtatEnCours() || $session->hasEtatPhaseValidation())) {
                continue;
            }
            if ($today < $session->getDateDebutValidation() || $session->getDateFinValidation() < $today) {
                continue;
            }
            /** @var Stage $stage */
            foreach ($session->getStages() as $stage) {
//                 État du stage valide
                if (!($stage->hasEtatEnCours() || $stage->hasEtatPhaseValidation())) {
                    continue;
                }
                //Date valide -- Test a priori inutile car si les dates ne sont pas bonne le État ne l'est pas n'ont plus plus le fait que la vérification est faite également au niveau de la session
                if ($today < $session->getDateDebutValidation() || $stage->getDateFinValidation() < $today) {
                    continue;
                }
                //L'affectation du stage est validée par la commission
                if (!$stage->getAffectationStage() || !$stage->getAffectationStage()->hasEtatValidee()) {
                    continue;
                }
                //Le mail n'as pas déjà été envoyée
                if ($this->mailTypeSended(CodesMailsProvider::VALIDATION_STAGE, ['stage' => $stage])) {
                    continue;
                }
                $stagesToSend[$stage->getId()] = $stage;
            }
        }
        return $stagesToSend;
    }


    /** ***************************************************************************************/
    /**
     * Sécurité pour l'envoie des mails types que toutes les conditions sont bien fournies
     * Ces vérification sont faites par sécurité mais doivent théoriquement être vérifier avant
     * Cette fonction ne sert que pour la sécurité juste avant l'envoie du mail
     * Génére des exceptions si impossible à capturer si besoins
     * @param string $codeMail
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function canSendMailType(string $codeMail, array $data = []): bool
    {
        $template = $this->getTemplateService()->getTemplateByCode($codeMail);
        if (!$template) {
            $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
            $msg .= "<br /> Le modéle n'as pas été trouvé.";
            throw new Exception($msg);
        }

        $destinataires = $this->getMailTypeDestinataires($codeMail, $data);
        if (empty($destinataires)) {
            $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
            $msg .= "<br /> Le(s) destinataire(s) ne peuvent pas être défini(s).";
            throw new Exception($msg);
        }
        foreach ($destinataires as $to) {
            if (!$this->adressesMailsAreValides($to)) {
                $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                $msg .= "<br /> L'un des destinaire n'est pas valide.";
                throw new Exception($msg);
            }
        }

        switch ($codeMail) {
            case CodesMailsProvider::STAGE_DEBUT_CHOIX:
            case CodesMailsProvider::STAGE_DEBUT_CHOIX_RAPPEL:
                if (!isset($data['stage'])) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= "<br /> Le stage correspondant n'est pas fourni";
                    throw new Exception($msg);
                }
                /** @var Stage $stage */
                $stage = $data['stage'];
                //L'année du stage n'est pas vérouillée
                if (!$stage->getAnneeUniversitaire()->isAnneeVerrouillee()) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= sprintf("L'année universitaire %s n'est pas validé", $stage->getAnneeUniversitaire()->getLibelle());
                    throw new Exception($msg);
                }
                //Vérification sur le l'état
//                if (!$stage->hasEtatPhasePreferences()
//                    ||!$stage->getSessionStage()->hasEtatPhaseChoix()) {
//                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
//                    $msg .= sprintf("Le stage #%s n'as pas un état valide", $stage->getId());
//                    throw new Exception($msg);
//                }
//                //Vérification sur les dates
//                $today = new DateTime();
//                if ($today < $stage->getDateDebutChoix() || $stage->getDateFinChoix() < $today) {
//                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
//                    $msg .= sprintf("La phase de définition des préférences du stage #%s est définie entre le %s - %s et le %s - %s.",
//                        $stage->getId(),
//                        $stage->getDateDebutChoix()->format('d/m/Y'), $stage->getDateDebutChoix()->format('H\hi'),
//                        $stage->getDateFinChoix()->format('d/m/Y'), $stage->getDateFinChoix()->format('H\hi')
//                    );
//                    throw new Exception($msg);
//                }
            break;
            case CodesMailsProvider::AFFECTATION_STAGE_VALIDEE:
                if (!isset($data['stage'])) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= "<br /> Le stage correspondant n'est pas fourni";
                    throw new Exception($msg);
                }
                /** @var Stage $stage */
                $stage = $data['stage'];
                //L'année du stage n'est pas vérouillée
                if (!$stage->getAnneeUniversitaire()->isAnneeVerrouillee()) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= sprintf("L'année universitaire %s n'est pas validé", $stage->getAnneeUniversitaire()->getLibelle());
                    throw new Exception($msg);
                }
                $affection = $stage->getAffectationStage();
                if (!isset($affection)) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= sprintf("L'affectation du stage #%s n'est pas définie", $stage->getId());
                    throw new Exception($msg);
                }
//                //L'affectation doit avoir le état validée par la commission
                if (!$affection->hasEtatValidee()) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= sprintf("L'affectation du stage #%s n'est pas validée par la commission.", $stage->getId());
                    throw new Exception($msg);
                }
                break;
            //Pas de break volontaire pour vérifier les même conditions que pour le mail de validation classique
            case CodesMailsProvider::VALIDATION_STAGE:
                if (!isset($data['stage'])) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= "<br /> Le stage correspondant n'est pas fourni";
                    throw new Exception($msg);
                }
                if (!isset($data['contactStage'])) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= "<br /> Le contact destinataires n'as pas été fournis";
                    throw new Exception($msg);
                }
                /** @var ContactStage $contactStage */
                $contactStage = $data['contactStage'];
                /** @var Stage $stage */
                $stage = $data['stage'];
                if (!$contactStage->isActif()) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= sprintf("Le contact %s a été désactivé et ne doit donc pas recevoir de mail.", ($contactStage->getDisplayName()) ?? $contactStage->getLibelle());
                    throw new Exception($msg);
                }
                if (!$contactStage->canValiderStage()) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= sprintf("Le contact %s n'est pas autorisé à valider le stage.", ($contactStage->getDisplayName()) ?? $contactStage->getLibelle());
                    throw new Exception($msg);
                }
                if (!$contactStage->getEmail() || (!filter_var($contactStage->getEmail(), FILTER_VALIDATE_EMAIL))) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= sprintf("L'adresse mail du contact %s n'est pas valide.", ($contactStage->getDisplayName()) ?? $contactStage->getLibelle());
                    throw new Exception($msg);
                }
                if ($contactStage->getStage()->getId() != $stage->getId()) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= sprintf("Le contact %s n'est pas liée au stage #%s.",($contactStage->getDisplayName()) ?? $contactStage->getLibelle(), $stage->getId());
                    throw new Exception($msg);
                }
                //L'affectation n'est pas validée
                $affection = $stage->getAffectationStage();
                if (!$affection || !$affection->hasEtatValidee()) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= sprintf("L'affectation du stage #%s n'est pas validée par la commission.", $stage->getId());
                    throw new Exception($msg);
                }

                //Le stage n'est pas déjà validé
                $validation = $stage->getValidationStage();
                if (!$validation) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= sprintf("L'état de validation du stage #%s ne peut pas être vérifier", $stage->getId());
                    throw new Exception($msg);
                }
//                if ($validation->validationEffectue()) {
//                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
//                    $msg .= sprintf("La validation du stage #%s a déjà été effectuée.", $stage->getId());
//                    throw new Exception($msg);
//                }

                //Le token de validation existe et peux être envoyée
                if (!$contactStage->getTokenValidation()
                    || $contactStage->getTokenValidation() == ""
                    || $contactStage->getTokenExpirationDate() < new DateTime()
                ) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= "Le token de validation n'est pas valide.";
                    throw new Exception($msg);
                }
                break;
            case CodesMailsProvider::VAlIDATION_STAGE_RAPPEL:
                if (!isset($data['stage'])) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= "<br /> Le stage correspondant n'est pas fourni";
                    throw new Exception($msg);
                }
                if (!isset($data['contactStage'])) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= "<br /> Le contact destinataire n'as pas été fourni";
                    throw new Exception($msg);
                }
                /** @var ContactStage $contactStage */
                $contactStage = $data['contactStage'];
                /** @var Stage $stage */
                $stage = $data['stage'];
                if (!$contactStage->isActif()) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= sprintf("Le contact %s a été désactivé et ne doit donc pas recevoir de mail.", ($contactStage->getDisplayName()) ?? $contactStage->getLibelle());
                    throw new Exception($msg);
                }
                if (!$contactStage->canValiderStage()) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= sprintf("Le contact %s n'est pas autorisé à valider le stage.", ($contactStage->getDisplayName()) ?? $contactStage->getLibelle());
                    throw new Exception($msg);
                }
                if (!$contactStage->getEmail() || (!filter_var($contactStage->getEmail(), FILTER_VALIDATE_EMAIL))) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= sprintf("L'adresse mail du contact %s n'est pas valide.", ($contactStage->getDisplayName()) ?? $contactStage->getLibelle());
                    throw new Exception($msg);
                }
                if ($contactStage->getStage()->getId() != $stage->getId()) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= sprintf("Le contact %s n'est pas liée au stage #%s.", ($contactStage->getDisplayName()) ?? $contactStage->getLibelle(), $stage->getId());
                    throw new Exception($msg);
                }
                //L'affectation n'est pas validée
                $affection = $stage->getAffectationStage();
                if (!$affection || !$affection->hasEtatValidee()) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= sprintf("L'affectation du stage #%s n'est pas validée par la commission.", $stage->getId());
                    throw new Exception($msg);
                }

                //Le stage n'est pas déjà validé
                $validation = $stage->getValidationStage();
                if (!$validation) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= sprintf("L'état de validation du stage #%s ne peut pas être vérifier", $stage->getId());
                    throw new Exception($msg);
                }
                if ($validation->validationEffectue()) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= sprintf("La validation du stage #%s a déjà été effectuée.", $stage->getId());
                    throw new Exception($msg);
                }
                //Le token de validation existe et peux être envoyée
                if (!$contactStage->getTokenValidation()
                    || $contactStage->getTokenValidation() == ""
                    || $contactStage->getTokenExpirationDate() < new DateTime()
                ) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= "Le token de validation n'est pas valide.";
                    throw new Exception($msg);
                }
                break;
            case CodesMailsProvider::VALIDATION_STAGE_EFFECTUEE:
                if (!isset($data['stage'])) {
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= "<br /> Le stage correspondant n'est pas fourni";
                    throw new Exception($msg);
                }
                /** @var Stage $stage */
                $stage = $data['stage'];
                $validation = $stage->getValidationStage();
                if(!$validation || !$validation->validationEffectue()){
                    $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                    $msg .= sprintf("<br /> La validation du stage #%s n'as pas été efectuée", $stage->getId());
                    throw new Exception($msg);
                }
            break;
            default:
                $msg = sprintf("Impossible d'envoyer le mail de code %s. ", $codeMail);
                $msg .= "Le code du modéle n'est pas définie";
                throw new Exception($msg);
        }
        return true;
    }

    /**
     * @param string|null $mails
     * @return boolean
     */
    public function adressesMailsAreValides(?string $mails): bool
    {
        if ($mails === null) {
            return false;
        }
        if ($mails == "") {
            return false;
        }
        $mails = explode(",", $mails);
        foreach ($mails as $mail) {
            $mail = trim($mail);
            if ($mail == "") {
                return false;
            }
            if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                return false;
            }
        }
        return true;
    }
}