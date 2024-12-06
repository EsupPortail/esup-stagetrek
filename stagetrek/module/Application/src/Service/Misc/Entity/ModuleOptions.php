<?php

namespace Application\Service\Misc\Entity;

use Laminas\Stdlib\AbstractOptions;
use Laminas\Stdlib\ArrayUtils;

class ModuleOptions extends AbstractOptions
{
    /**
     * @var array
     */
    protected array $encryption = [];

    /**
     * @var array
     */
    protected array $http_client = [];


    /**
     * Getter for encryption params
     *
     * @return array
     */
    public function getEncryption(): array
    {
        return $this->encryption;
    }

    /**
     * Setter for encryption params
     *
     * @param array $encryption
     * @return self
     */
    public function setEncryption(array $encryption): static
    {
        $this->encryption = ArrayUtils::merge($this->encryption, $encryption);

        return $this;
    }

    /**
     * Getter for sgc params
     *
     * @return array
     */
    public function getHttpclient(): array
    {
        return $this->http_client;
    }

    /**
     * Setter for sgc params
     *
     * @param array $http_client
     * @return self
     */
    public function setHttpclient(array $http_client): static
    {
        $this->http_client = ArrayUtils::merge($this->http_client, $http_client);

        return $this;
    }


    protected ?string $applicationEnv = null;
    /**
     * @return string
     */
    public function getApplicationEnv(): ?string
    {
        return $this->applicationEnv;
    }

    /**
     * @param string $applicationEnv
     */
    public function setApplicationEnv(string $applicationEnv): void
    {
        $this->applicationEnv = $applicationEnv;
    }
}
