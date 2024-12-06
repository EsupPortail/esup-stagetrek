<?php

namespace Application\Service\Renderer;

use Application\Entity\Db\ContactStage;
use Application\Entity\Db\Stage;

class PdfRendererService
{

    /** @var array */
    protected array $variables = [];


    /**
     * @param array $variables
     * @return ContactRendererService
     */
    public function setVariables(array $variables): PdfRendererService
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

//    Donnée par mpdf
    public function getNumeroPage(): string
    {
        return "{PAGENO}";
    }
    public function getNbPages(): string
    {
        return "{nbpg}";
    }


}