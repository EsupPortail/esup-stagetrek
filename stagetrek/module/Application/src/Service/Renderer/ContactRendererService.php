<?php

namespace Application\Service\Renderer;

use Application\Entity\Db\Contact;
use Application\Entity\Db\ContactStage;
use Application\Entity\Db\Etudiant;
use Application\Entity\Db\SessionStage;
use Application\Entity\Db\Stage;
use Application\Entity\Db\TerrainStage;
use Application\Service\Renderer\Traits\UrlServiceAwareTrait;
use DateTime;

class ContactRendererService
{

    use UrlServiceAwareTrait;
    /** @var array */
    protected array $variables = [];


    /**
     * @param array $variables
     * @return ContactRendererService
     */
    public function setVariables(array $variables): ContactRendererService
    {
        $this->variables = $variables;
        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getVariable(string $key): mixed
    {
        if (!isset($this->variables[$key])) return null;
        return $this->variables[$key];
    }

    /**
     * @return string|null
     */
    public function getResponsablesNames(): ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return sprintf("<span class='%s' style='color:darkred;'> Le paramètre stage n'as pas été trouvée</span>", MacroService::CLASS_MACRO);

        $contacts = $stage->getContactsStages()->toArray();
        $contacts = array_filter($contacts, function (ContactStage $contact) {
            if (!$contact->isActif()) return false;
            if (!$contact->isResponsableStage()) return false;
            return true;
        });
        if (empty($contacts)) return sprintf("<span class='%s' style='color:darkred;'>Le stage n'as pas de responsable de stage de défini</span>", MacroService::CLASS_MACRO);

        $responsables = "";
        /** @var ContactStage $contact */
        foreach ($contacts as $contact) {
            if($contact->getDisplayName()===null || $contact->getDisplayName()===""){
                return sprintf("<span class='%s' style='color:darkred;'>Le contact responsable de stage (Code %s) n'as pas de nom renseigné.</span>", MacroService::CLASS_MACRO, $contact->getCode());
            }
            $responsables .= $contact->getDisplayName() . ", ";
        }
        $responsables = trim($responsables);
        return substr_replace($responsables, "", -1);
    }

    /**
     * @return string|null
     */
    public function getMailResponsables(): ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return sprintf("<span class='%s' style='color:darkred;'> Le paramètre stage n'as pas été trouvée</span>", MacroService::CLASS_MACRO);

        $contacts = $stage->getContactsStages()->toArray();
        $contacts = array_filter($contacts, function (ContactStage $contact) {
            if (!$contact->isActif()) return false;
            if (!$contact->isResponsableStage()) return false;
            return true;
        });
        if (empty($contacts)) return sprintf("<span class='%s' style='color:darkred;'>Le stage n'as pas de responsable de stage de défini</span>", MacroService::CLASS_MACRO);

        $responsables = "";
        /** @var ContactStage $contact */
        foreach ($contacts as $contact) {
            if($contact->getEmail()===null || $contact->getEmail()===""){
                return sprintf("<span class='%s' style='color:darkred;'>Le contact responsable de stage (Code %s) n'as pas d'adresse mail.</span>", MacroService::CLASS_MACRO, $contact->getCode());
            }
            $responsables .= $contact->getEmail() . ", ";
        }
        $responsables = trim($responsables);
        return substr_replace($responsables, "", -1);
    }

    /**
     * @return string|null
     */
    public function getlisteEtudiantsEncadres(): ?string
    {
        /** @var SessionStage $session */
        $session = $this->getVariable('session');
        if(!isset($session)|| !$session instanceof SessionStage) return null;
        /** @var Contact $contact */
        $contact = $this->getVariable('contact');
        if(!isset($contact) || !$contact instanceof Contact) return null;

        //on récupére parmis la session la liste des stages ayant pour responsable le contact et que celui ci est définie comme valideur
        $stages = $session->getStages()->toArray();
        $stages = array_filter($stages, function (Stage $stage) use ($contact){
            $terrain = $stage->getTerrainStage();
            if(!isset($terrain)){return false;}
            $ct = $contact->getContactForTerrain($terrain);
            if(!isset($ct)){return false;}
            if(!$ct->sendMailAutoListeEtudiantsStage()){return false;}
            if($stage->isNonEffectue()){return false;}
            if($stage->hasEtatDesactive()) {return false;}
            if($stage->hasEtatEnDisponibilite()) {return false;}
            $contactsStages = $stage->getContactsStages()->toArray();
            /** @var ContactStage $contactStage */
            foreach ($contactsStages as $contactStage) {
                if($contactStage->getContact()->getId()==$contact->getId()){
                    return true;
                }
            }
            return false;
        });

        $stages = $this->trierStages($stages);

        $currentTerrain = null;
        $html = "<div>";
        /** @var Stage $stage */
        foreach ($stages as $stage) {
            /** @var ContactStage $cs */
            $cs = null;
            $contactsStages = $stage->getContactsStages()->toArray();
            /** @var ContactStage $contactStage */
            foreach ($contactsStages as $contactStage) {
                if($contactStage->getContact()->getId()==$contact->getId()){
                    $cs  = $contactStage;
                }
            }

            /** @var TerrainStage $terrain */
            $terrain = $stage->getTerrainStage();
            if(!isset($terrain)){continue;} //cas ou il n'y a pas d'affectation
            $etudiant = $stage->getEtudiant();
//            Si pas de liens valide, on passe
            if(isset($currentTerrain) && $terrain->getId() != $currentTerrain->getId()){
                $html .= "</ul>";
            }
            if(!isset($currentTerrain) ||($terrain->getId() != $currentTerrain->getId())){
                $currentTerrain = $terrain;
                $html .=sprintf("<div><strong>%s</strong></div><ul>",
                    $terrain->getLibelle()
                );
            }
            $html .= sprintf("<li>%s</li>", $etudiant->getDisplayName());
        }
        $html .= "</div>";
        return $html;

        return null;
    }

    /**
     * @return string|null
     */
    public function getListeUrlValidations(): ?string
    {
        /** @var SessionStage $session */
        $session = $this->getVariable('session');
        if(!isset($session)) return null;
        /** @var ContactStage $contact */
        $contact = $this->getVariable('contact');
        if(!isset($contact)) return null;
        //on récupére parmis la session la liste des stages ayant pour responsable le contact et que celui ci est définie comme valideur
        $stages = $session->getStages()->toArray();
        $stages = array_filter($stages, function (Stage $stage) use ($contact){
            $terrain = $stage->getTerrainStage();
            //Quelques vérification avant pour éviter des erreurs
            $today = new DateTime();
            if ($stage->getValidationStage()->validationEffectue() && $stage->getDateFinValidation() < $today){return false;}
            //On ne prend pas en compte les stage en alerte/erreur ...
            if ($stage->hasEtatEnErreur()) {return false;}
            if ($stage->hasEtatEnAlerte())  {return false;}
            if($stage->isNonEffectue()){return false;}
            if($stage->hasEtatDesactive()) {return false;}
            if($stage->hasEtatEnDisponibilite()) {return false;}
            if(!isset($terrain)){return false;}
            $contactsStages = $stage->getContactsStages()->toArray();
            /** @var ContactStage $contactStage */
            foreach ($contactsStages as $contactStage) {
                if($contactStage->getContact()->getId()==$contact->getId()){
//                    On vérifie que le contact est bien autorisé a valider le stage
                    if(!$contactStage->canValiderStage()){return false;}
                    if(!$contactStage->sendMailAutoValidationStage()){return false;}
                    if(!$contactStage->tokenValide()){return false;}
                    return true;
                }
            }
            return false;
        });

        $stages = $this->trierStages($stages);

        $currentTerrain = null;
        $html = "<div>";
        /** @var Stage $stage */
        foreach ($stages as $stage) {
            /** @var ContactStage $cs */
            $cs = null;
            $contactsStages = $stage->getContactsStages()->toArray();
            /** @var ContactStage $contactStage */
            foreach ($contactsStages as $contactStage) {
                if($contactStage->getContact()->getId()==$contact->getId()){
                    $cs  = $contactStage;
                }
            }

            $this->getUrlService()->setVariables(['stage' => $stage, 'contactStage'=>$cs]);
            /** @var TerrainStage $terrain */
            $terrain = $stage->getTerrainStage();
            if(!isset($terrain)){continue;} //cas ou il n'y a pas d'affectation
            $etudiant = $stage->getEtudiant();
            $lien = $this->getUrlService()->getUrlValidationStage();
            if(!isset($lien)) {
                continue;
            }
//            Si pas de liens valide, on passe
            if(isset($currentTerrain) && $terrain->getId() != $currentTerrain->getId()){
                $html .= "</ul>";
            }
            if(!isset($currentTerrain) ||($terrain->getId() != $currentTerrain->getId())){
                $currentTerrain = $terrain;
                $html .=sprintf("<div><strong>%s</strong></div><ul>",
                    $terrain->getLibelle()
                );
            }
            $html .= sprintf("<li><a href='%s'>%s</a></li>", $lien, $etudiant->getDisplayName());
        }
        $html .= "</div>";
        return $html;
    }

    /** Trie une liste des stages associés aux contacts */
    protected function trierStages(array $stages) : array
    {
        usort($stages, function (Stage $s1, Stage $s2) {
            $t1 = $s1->getTerrainStage();
            $t2 = $s2->getTerrainStage();
//            Trie d'abord par terrain
            if($t1->getId() != $t2->getId()){
                $compTerrain = TerrainStage::sort([$t1, $t2]);
                /** @var TerrainStage $first */
                $first = current($compTerrain);
                return ($first->getId() == $t1->getId()) ? 1 : -1;
            }
//            Puis par étudiants
            $e1 = $s1->getEtudiant();
            $e2 = $s2->getEtudiant();
            if($e1->getId() != $e2->getId()){
                return strcmp($e1->getDisplayName(), $e2->getDisplayName());
            }
            return 0;
        });

        return $stages;
    }

}