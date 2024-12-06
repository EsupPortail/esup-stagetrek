<?php
namespace Application\Service\Stage;

use Application\Entity\Db\ContactStage;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use Application\Entity\Db\ValidationStage;
use Application\Provider\EtatType\ValidationStageEtatTypeProvider;
use Application\Provider\Mailing\CodesMailsProvider;
use Application\Provider\Roles\UserProvider;
use Application\Service\Contrainte\Traits\ContrainteCursusEtudiantServiceAwareTrait;
use Application\Service\Etudiant\Traits\EtudiantServiceAwareTrait;
use Application\Service\Mail\MailService;
use Application\Service\Misc\CommonEntityService;
use Application\Service\Misc\Traits\EntityEtatServiceAwareTrait;
use Application\Service\Parametre\Traits\ParametreServiceAwareTrait;
use Application\Service\Stage\Traits\StageServiceAwareTrait;
use DateTime;
use Exception;
use UnicaenEtat\Entity\Db\HasEtatsInterface;
use UnicaenMail\Service\Mail\MailServiceAwareTrait;
use UnicaenUtilisateur\Entity\Db\User;

/**
 * Class ValidationStageService
 * @package Application\Service\Stage
 */
class ValidationStageService extends CommonEntityService
{
    use ParametreServiceAwareTrait;
    use StageServiceAwareTrait;
    use EtudiantServiceAwareTrait;
    use ContrainteCursusEtudiantServiceAwareTrait;

      /** @return string */
    public function getEntityClass(): string
    {
        return ValidationStage::class;
    }

    /**
     * @param mixed $entity
     * @param string|null $serviceEntityClass classe de l'entité
     * @return SessionStage
     * @throws \Exception
     */
    public function add(mixed $entity, string $serviceEntityClass = null): mixed
    { //TODO : peut être a revoir
        throw new Exception('La création de la validation passe par la création du stage.');
    }

    /**
     * @param mixed $entity
     * @param string|null $serviceEntityClass classe de l'entité
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function update(mixed $entity, string $serviceEntityClass = null): mixed
    {
        /** @var ValidationStage $validation */
        $validation = $entity;
        $this->getObjectManager()->persist($validation);
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush();

            $this->updateEtat($validation);

            $etudiant = $validation->getStage()->getEtudiant();
            $contraintes = $etudiant->getContraintesCursusEtudiants()->toArray();
            $stage = $validation->getStage();
            $this->getStageService()->updateEtat($stage);
            $this->getContrainteCursusEtudiantService()->updateEtats($contraintes);
            $this->getEtudiantService()->updateEtat($etudiant);
        }
        return $entity;
    }

    /**
     * @param mixed $entity
     * @param string|null $serviceEntityClass classe de l'entité
     * @return $this
     * @throws \Exception
     */
    public function delete(mixed $entity, string $serviceEntityClass = null) : static
    { //TODO : peut être a revoir
        throw new Exception('La suppression de la validation se fait par cascade lors de la suppression du stage concerné.');
    }


    use EntityEtatServiceAwareTrait;
    protected function computeEtat(HasEtatsInterface $entity): string
    {
        if(!$entity instanceof ValidationStage){
            throw new Exception("L'entité fournise n'est pas un stage.");
        }

        $validation = $entity;
        $stage = $validation->getStage();

        //Validation effectuée
        switch (true){
            case $validation->isValide() :
                return ValidationStageEtatTypeProvider::VALIDE;
            case $validation->isInvalide() :
                return ValidationStageEtatTypeProvider::INVAlIDE;
            case $stage->isNonEffectue() :
                $this->setEtatInfo("Le stage a été marqué comme non effectué");
                return ValidationStageEtatTypeProvider::STAGE_NON_EFFECTUE;
        }
        //Etudiant en disponibilité
        $etudiant = $stage->getEtudiant();
        $dispo = $etudiant->getDisponibilites();
        $today = new DateTime();
        foreach ($dispo as $d){
            $debut = $d->getDateDebut();
            $fin = $d->getDateFin();
            if(($stage->getDateDebutStage() <= $debut && $debut < $stage->getDateFinStage())
                || ($stage->getDateDebutStage() <= $fin && $fin <= $stage->getDateFinStage())
                || ($debut <= $stage->getDateDebutStage() &&  $stage->getDateDebutStage() <= $fin)
                || ($debut <= $stage->getDateFinStage() &&  $stage->getDateFinStage() <= $fin)
            ){
                if($today <= $fin){
                    $this->setEtatInfo(sprintf("L'étudiant est en disponibilité du %s au %s", $debut->format('d/m/Y'), $fin->format('d/m/Y')));
                }
                else{
                    $this->setEtatInfo("L'étudiant était en disponibilité durant la période du stage");
                }
                return ValidationStageEtatTypeProvider::EN_DISPO;
            }
        }

//
        if($today < $stage->getDateFinCommission()){
            return ValidationStageEtatTypeProvider::FUTUR;
        }

        //Mise en erreur pour un stage sans affectation
        $affectation = $stage->getAffectationStage();
        if(!isset($affectation)) {//logiquement impossible car l'abscence d'affectation signifierai qu'il n'y a pas de stage et donc de validation
            $this->setEtatInfo("L'affectation correspondant au stage n'as pas été trouvée");
            return ValidationStageEtatTypeProvider::EN_ERREUR;
        }

        if($stage->getDateDebutStage() <= $today && !$affectation->isValidee()){
            $this->setEtatInfo("L'affectation du stage n'as n'as pas été validée par la commission");
            return ValidationStageEtatTypeProvider::EN_ERREUR;
        }

        //Mise en alete pour un stage n'ayant personne pour le validé
        $contacts = $stage->getContactsStages();
        $canBeValidate = false;
        /** @var ContactStage $c */
        foreach ($contacts as $c){
            if($c->canValiderStage()){$canBeValidate = true;}
        }
        if(!$canBeValidate){
            $this->setEtatInfo("Le stage n'as aucun contact autorisé à effectuer la validation");
            return ValidationStageEtatTypeProvider::EN_ALERTE;
        }

        if($stage->getDateFinValidation() < $today){
            return ValidationStageEtatTypeProvider::EN_RETARD;
        }
        elseif($stage->getDateDebutValidation() < $today){
            return ValidationStageEtatTypeProvider::EN_ATTENTE;
        }
        else {
            return ValidationStageEtatTypeProvider::FUTUR;
        }

    }

    use MailServiceAwareTrait;
    //TODO : modifier ces fonctions pour passer par des templates
    public function sendMailEchecValidationStage(?Stage $stage, $token, $codeErreur, $msg): void
    {
        $appUSer = $this->getObjectManager()->getRepository(User::class)->findOneBy(['username' => UserProvider::APP_USER]);
        if (!isset($appUSer)) { return;}
        $to = $appUSer->getEmail();
        $sujet = "Echec de validation d'un stage";
        $corps = "<p>Une tentative de validation de stage a échouée.</p>";
        $corps .= "<hr/>";
        $corps .= sprintf("<p><b>Erreur :</b> %s</p>", $codeErreur);
        $corps .= sprintf("<p><b>Message d'erreur :</b><div>%s</div></p>", $msg);

        /** @var ContactStage $contact */
        $contact = null;
        if($stage && $token){
            /** @var ContactStage $contactStage */
            foreach ($stage->getContactsStages() as $contactStage) {
                $tokenContact = $contactStage->getTokenValidation();
                if (strcmp(($tokenContact) ?? "", $token) == 0) {
                    $contact = $contactStage;
                    break;
                }
            }
        }
        $etudiant = ($stage) ? $stage->getEtudiant() : null;
        $corps .= "<hr/>";
        $corps .= "<p><b>Informations diverses :</b><ul>";
        $corps .= sprintf("<li>Stage %s : %s</li>", ($stage) ? $stage->getLibelle() : "Indéterminé", ($stage) ? "(#".$stage->getId().")" : "");
        $corps .= sprintf("<li>Etudiant %s %s</li>", ($etudiant) ? $etudiant->getDisplayName() : "Indéterminé", ($etudiant) ? $etudiant->getNumEtu() : "");
        $corps .= sprintf("<li>Contact du stage : %s %s</li>", ($contact) ? $contact->getLibelle() : "Indéterminé", ($contact) ? " (Code ". $contact->getCode() .")" : "");
        $corps .= "</ul></p>";

        $mail = $this->getMailService()->sendMail($to, $sujet, $corps);
        $motsClef = [
            'stageId='. (($stage) ? $stage->getId() : "n/a"),
            'contactStageId='. (($contact) ? $contact->getId() : "n/a"),
        ];
        $mail->setMotsClefs($motsClef);
        $this->getMailService()->update($mail);    }


    //TODO : a vérifier, a priori fonction non utilisé, mails géré depuis les événements
    /**
     * @throws \Exception
     */
    public function sendMailStageValidationEffectuee(Stage $stage): void
    {
        $etudiant = $stage->getEtudiant();
        $validation = $stage->getValidationStage();
        /** @var MailService $mailService */
        $mailService = $this->getMailService();
        $data = ['stage'=>$stage];
        if(!$mailService->mailTypeSended(CodesMailsProvider::VALIDATION_STAGE_EFFECTUEE, $data)) {
            $mailService->sendMailType(CodesMailsProvider::VALIDATION_STAGE_EFFECTUEE, $data);
        }
        //Envoie d'un mail a la commission;
        //Pas de mails s'il n'y a pas de commentaire
        if(!$validation->getCommentaireCache() || $validation->getCommentaireCache() == "" ){return;}
        $appUSer = $this->getObjectManager()->getRepository(User::class)->findOneBy(['username' => UserProvider::APP_USER]);
        if (!isset($appUSer)) {return;}

        $to = $appUSer->getEmail();
        $sujet = sprintf("Validation du stage %s de %s",
            $stage->getLibelle(), $etudiant->getDisplayName()
        );
        $corps = sprintf("<p>Le stage %s de %s a été %s par %s</p>",
            $stage->getLibelle(), $etudiant->getDisplayName(),
            ($stage->getValidationStage()->isValide()) ? "validé" : "invalidé", $validation->getValidateBy()
        );
        if($validation->getCommentaireCache() != ""){
            $corps .= sprintf("<hr/><p><b>Commentaires portées à l'attention de la commission :</b><div>%s</div></p>",
                $validation->getCommentaireCache()
            );
        }

        $mail = $this->getMailService()->sendMail($to, $sujet, $corps);
        $motsClef = ['stageId='. $stage->getId()];
        $mail->setMotsClefs($motsClef);
        $this->getMailService()->update($mail);
    }
}