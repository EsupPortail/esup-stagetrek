<?php

namespace API\Service;


use API\ApiRest\ApiRestException;
use API\Service\Abstract\AbstractApiRestService;
use Laminas\Json\Json;

class ReferentielEtudiantApiService extends AbstractApiRestService
{
    const API_KEY = 'referentiel_etudiant';
    const PARAM_URL = 'url';

    protected array $fields = [
        'nom',
        'code',
        'codesPostaux',
        'centre',
        'surface',
        'contour',
        'codeDepartement',
        'departement',
        'codeRegion',
        'region',
        'population'
    ];

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
     * @param array $fields
     * @return mixed
     */
    public function findByCodePromo(string $code): mixed
    {
        $url = $this->getApiUrl();
        $token = ($this->getParams()['token'] && $this->getParams()['token'] != "") ? "/".$this->getParams()['token'] : null;
        $fullUrl = sprintf("%s%s?code=%s",$url, $token, $code);
        return $this->getApiRest()->get($fullUrl)->getBody();
    }
}
