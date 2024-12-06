<?php

namespace Application\Service\Renderer;

use Application\Entity\Db\ContactStage;
use Application\Entity\Db\Stage;

class ContactRendererService
{

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


}