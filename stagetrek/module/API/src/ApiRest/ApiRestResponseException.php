<?php

namespace API\ApiRest;

use Laminas\Http\Response;

class ApiRestResponseException extends ApiRestException
{
    /**
     * @var Response|null
     */
    protected ?Response $response = null;

    /**
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        parent::__construct($response->getStatusCode(), $response->getContent());

        $this->setResponse($response);
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     * @return self
     */
    public function setResponse(Response $response): static
    {
        $this->response = $response;
        return $this;
    }
}