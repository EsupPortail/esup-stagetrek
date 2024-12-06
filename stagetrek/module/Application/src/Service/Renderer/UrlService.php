<?php

namespace Application\Service\Renderer;

use Application\Controller\Preference\PreferenceController;
use Application\Controller\Stage\StageController;
use Application\Controller\Stage\ValidationStageController;
use Application\Entity\Db\ContactStage;
use Application\Entity\Db\Stage;
use Exception;
use Laminas\View\Renderer\PhpRenderer;

class UrlService {

    protected ?string $uri_host =null;
    protected ?string $uri_scheme =null;
    public function getUriHost(): ?string
    {
        return $this->uri_host;
    }
    public function setUriHost(?string $uri_host): void
    {
        $this->uri_host = $uri_host;
    }
    public function getUriScheme(): ?string
    {
        return $this->uri_scheme;
    }
    public function setUriScheme(?string $uri_scheme): void
    {
        $this->uri_scheme = $uri_scheme;
    }
    protected function getUrl(string $route=null, array $params = []): string
    {
        $scheme = $this->getUriScheme();
        $host = $this->getUriHost();
        $parametres = "";
        foreach ($params as $key => $value) {
            $parametres .= "/$value";
        }
        if(!isset($scheme)){throw new Exception("Le paramètre de configuration 'uri_scheme' n'est pas défini");}
        if(!isset($host)){throw new Exception("Le paramètre de configuration 'uri_host' n'est pas défini");}
        return sprintf("%s://%s%s%s", $scheme, $host, ($route) ? "/".$route : "",$parametres);
    }



    /** @var PhpRenderer|null */
    protected ?PhpRenderer $renderer;
    /** @var array */
    protected array $variables = [];

    /**
     * @param PhpRenderer $renderer
     * @return UrlService
     */
    public function setRenderer(PhpRenderer $renderer): UrlService
    {
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * @param array $variables
     * @return UrlService
     */
    public function setVariables(array $variables): UrlService
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
        if (! isset($this->variables[$key])) return null;
        return $this->variables[$key];
    }

    /**
     * @return string
     */
    public function getUrlApp() : string
    {
        return $this->getUrl();
    }

    /**
     * @return string|null
     * @throws \Exception
     */
    public function getUrlValidationStage() : ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return null;
        /** @var ContactStage $contactStage */
        $contactStage = $this->getVariable('contactStage');
        if ($contactStage === null) return null;
        $token = ($contactStage->getTokenValidation()) ?? null;
        if ($token === null) return null;
        return $this->getUrl(VaLidationStageController::ROUTE_VALIDER, ['stage' => $stage->getId(), 'token' => $token]);
    }

    /**
     * @return string|null
     */
    public function getUrlPreferences() : ?string
    {
        /** @var Stage $stage */
        $stage = $this->getVariable('stage');
        if ($stage === null) return null;
        return $this->getUrl(PreferenceController::ROUTE_LISTER, ['stage' => $stage->getId()]);
    }

}