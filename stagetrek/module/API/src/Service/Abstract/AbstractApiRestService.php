<?php

namespace API\Service\Abstract;

use API\ApiRest\ApiRestAwareInterface;
use API\ApiRest\ApiRestAwareTrait;
use API\Service\Interfaces\ApiRestServiceInterface;
use Exception;
use Laminas\Stdlib\ArrayUtils;

abstract class AbstractApiRestService
    implements
    ApiRestAwareInterface,
    ApiRestServiceInterface
{
    use ApiRestAwareTrait;

    /**
     * Paramètres de configuration de l'API.
     *
     * @param array $params
     */
    protected array $params = [];

    /**
     * Url de l'API
     *
     * @var string|null
     */
    protected ?string $apiUrl = null;

    /**
     * Clé de configuration de l'API
     *
     * @return string
     */
    abstract static function getParamsApiKey(): string;

    /**
     * Constructor.
     *
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        if (null !== $params) {
            $this->setParams($params);
            if (array_key_exists('url', $params)) {
                $this->setApiUrl($params['url']);
            }
            else{
                throw new Exception("La configuration de l'URL du service n'est pas définie");
            }
        }
    }

    /**
     * Set params
     *
     * @param array $params
     * @return self
     */
    public function setParams(array $params): static
    {
//        if ($params instanceof \Traversable) {
            $params = ArrayUtils::iteratorToArray($params);
//        }

        $this->params = $params;

        return $this;
    }

    /**
     * Get params
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Set API url
     *
     * @param string $url
     * @return self
     */
    public function setApiUrl(string $url): static
    {
        $this->apiUrl = $url;

        return $this;
    }

    /**
     * Get API url
     *
     * @return string|null
     */
    public function getApiUrl(): ?string
    {
        return $this->apiUrl;
    }
}