<?php

namespace Application\Service\Renderer;

use Application\Entity\Db\Etudiant;

class AdresseRendererService
{
    /** @var array */
    protected array $variables;

    /**
     * @param array $variables
     * @return AdresseRendererService
     */
    public function setVariables(array $variables): AdresseRendererService
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
    public function getAdresseEtudiant(): ?string
    {
        /** @var Etudiant $etudiant */
        $etudiant = $this->getVariable('etudiant');
        if ($etudiant === null) return null;
        $adresse = $etudiant->getAdresse();
        if ($adresse === null) return null;
        return trim(sprintf("%s %s %s %s %s",
            $adresse->getAdresse(),
            $adresse->getComplement(),
            $adresse->getCodePostal(),
            $adresse->getVille(),
            $adresse->getCedex(),
        ));
    }


}