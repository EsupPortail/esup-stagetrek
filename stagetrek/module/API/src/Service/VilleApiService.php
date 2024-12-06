<?php

namespace API\Service;


use API\ApiRest\ApiRestException;
use API\Service\Abstract\AbstractApiRestService;
use Laminas\Json\Json;

class VilleApiService extends AbstractApiRestService
{
    const API_KEY = 'geo_gouv';

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
     * Trouver une ville par son code INSEE
     *
     * @param string $code
     * @param array $fields
     * @return mixed
     */
    public function find(string $code, array $fields = []): mixed
    {
        $fields = array_intersect($fields, $this->fields);
        $url = sprintf($this->getApiUrl() . '/communes/%1$s?fields=%2$s&format=json', $code, implode(',', $fields));

        try {
            return Json::decode($this->getApiRest()->get($url)->getBody());
        } catch (ApiRestException) {
            return null;
        }

    }

    /**
     * Trouver une ville par son nom
     *
     * @param string $name
     * @param array $fields
     * @return mixed
     */
    public function findByName(string $name, array $fields = []): mixed
    {
        $fields = array_intersect($fields, $this->fields);
        $url = sprintf($this->getApiUrl() . '/communes?nom=%1$s&fields=%2$s&format=json', $name, implode(',', $fields));

        try {
            return Json::decode($this->getApiRest()->get($url)->getBody());
        } catch (ApiRestException) {
            return [];
        }
    }

    /**
     * Trouver une ville par son code postal
     *
     * @param string $name
     * @param array $fields
     * @return mixed
     */
    public function findByCodePostal(string $name, array $fields = []): mixed
    {
        $fields = array_intersect($fields, $this->fields);
        $url = sprintf($this->getApiUrl() . '/communes?codePostal=%1$s&fields=%2$s&format=json', $name, implode(',', $fields));

        try {
            return Json::decode($this->getApiRest()->get($url)->getBody());
        } catch (ApiRestException) {
            return [];
        }
    }
}
