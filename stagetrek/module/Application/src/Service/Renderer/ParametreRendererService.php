<?php

namespace Application\Service\Renderer;

use Application\Entity\Db\Parametre;
use Application\Service\Parametre\Traits\ParametreServiceAwareTrait;

class ParametreRendererService
{
    use ParametreServiceAwareTrait;
    /** @var array */
    protected array $variables;

    /**
     * @param array $variables
     * @return AdresseRendererService
     */
    public function setVariables(array $variables): ParametreRendererService
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


    public function getDirecteurCHU(): ?string
    {
        return sprintf("%s", $this->getParametreService()->getParametreValue(Parametre::DIRECTEUR_CHU));
    }

    public function getDoyenUFR(): ?string
    {
        return sprintf("%s", $this->getParametreService()->getParametreValue(Parametre::DOYEN_UFR_SANTE));
    }

    public function getNomCHU(): ?string
    {
        return sprintf("%s", $this->getParametreService()->getParametreValue(Parametre::NOM_CHU));
    }

    public function getNbChoixPossible(): ?string
    {
        return sprintf("%s", $this->getParametreService()->getParametreValue(Parametre::NB_PREFERENCES));
    }

    public function getAdresseUFR(): ?string
    {
        return sprintf("%s", $this->getParametreService()->getParametreValue(Parametre::ADRESSE_UFR_SANTE));
    }


    public function getFaxUFR(): ?string
    {
        return sprintf("%s", $this->getParametreService()->getParametreValue(Parametre::FAX_UFR_SANTE));
    }


    public function getMailUFR(): ?string
    {
        return sprintf("%s", $this->getParametreService()->getParametreValue(Parametre::MAIL_UFR_SANTE));
    }

    public function getNomUFR(): ?string
    {
        return sprintf("%s", $this->getParametreService()->getParametreValue(Parametre::NOM_UFR_SANTE));
    }

    public function getTelUFR(): ?string
    {
        return sprintf("%s", $this->getParametreService()->getParametreValue(Parametre::TELEPHONE_UFR_SANTE));
    }


}