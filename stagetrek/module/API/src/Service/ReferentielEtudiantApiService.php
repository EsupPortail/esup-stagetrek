<?php

namespace API\Service;


use API\ApiRest\ApiRestException;
use API\Service\Abstract\AbstractApiRestService;
use DateTime;
use Exception;
use Laminas\Json\Json;

class ReferentielEtudiantApiService extends AbstractApiRestService
{
    const API_KEY = 'referentiel_etudiant';
    const PARAM_URL = 'url';

    /**
     * @return string
     */
    static public function getParamsApiKey(): string
    {
        return self::API_KEY;
    }

    /**
     * Trouver une ville par son nom
     *
     * @param string $name
     * @return mixed
     */
    public function findByCodePromo(string $codeVet, string $codeAnnee=null): mixed
    {
        $url = $this->getApiUrl();
        $token = ($this->getParams()['token'] && $this->getParams()['token'] != "") ? $this->getParams()['token'] : null;
        $this->getApiRest()->getRequest()->getHeaders()->addHeaderLine("Authorization", $token);
        $fullUrl = sprintf("%s/%s?code=%s&annee=%s",$url, $token, $codeVet, $codeAnnee);
        return $this->getApiRest()->get($fullUrl)->getBody();
    }
}
