<?php

namespace API\ApiRest;

use Laminas\Http\Client as HttpClient;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Stdlib\Parameters;
use Laminas\Uri\Http as HttpUri;

class ApiRest
{
    /**
     * @var HttpClient|null
     */
    protected ?HttpClient $httpClient = null;

    /**
     * @var Request|null
     */
    protected ?Request $request = null;

    /**
     * RestClient constructor.
     *
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Set the configuration array for the adapter
     *
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options = []): static
    {
        $this->httpClient->getAdapter()->setOptions($options);
        return $this;
    }

    /**
     * Retrieve the array of all Curl configuration options
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->httpClient->getAdapter()->getConfig();
    }

    /**
     * Retourne la requête qui sera utilisée pour interrogée l'API.
     *
     * Utile hacker la requête, exemple :
     *      $request->getHeaders()
     *          ->addHeaderLine("Content-Type", "application/xml")
     *          ->addHeaderLine("Accept", "application/xml")
     *          ->addHeaderLine("Accept-Encoding", "0")
     *          ->addHeaderLine("Encoding", "");
     *
     * @return Request
     */
    public function getRequest(): Request
    {
        if ($this->request === null) {
            $this->request = new Request();
        }

        return $this->request;
    }

    /**
     * @param string $xml
     * @return self
     */
    public function setRequestBodyAsXml(string $xml): self
    {
        // hacking de la requête pour forcer le content-type et spécifier un "raw body"
        $request = $this->getRequest();
        $request->setContent($xml);
        $request->getHeaders()
            ->addHeaderLine("Content-Type", "application/xml")
            ->addHeaderLine("Accept", "application/xml");

        return $this;
    }

    /**
     * @param string|HttpUri $url
     * @return Response
     */
    public function get(string|HttpUri $url) : Response
    {
        return $this->dispatchRequest($url, "GET");
    }

    /**
     * @param string|HttpUri $url
     * @param array|null $data
     * @param array $files
     * @return Response
     */
    public function post(string|HttpUri  $url, ?array $data = null, array $files = []): Response
    {
        return $this->dispatchRequest($url, "POST", $data, $files);
    }

    /**
     * @param string|HttpUri $url
     * @param array|string|null $data
     * @param array $files
     * @return Response
     */
    public function put(string|HttpUri $url, array|string $data = null, array $files = []): Response
    {
        return $this->dispatchRequest($url, "PUT", $data, $files);
    }

    /**
     * @param string|HttpUri $url
     * @return Response
     */
    public function delete(string|HttpUri $url): Response
    {
        return $this->dispatchRequest($url, "DELETE");
    }

    /**
     * @param string|HttpUri $url
     * @param string $method
     * @param null|array $data
     * @param null|array $files
     * @return Response
     */
    protected function dispatchRequest(string|HttpUri $url, string $method, ?array $data = null, ?array $files = null): Response
    {
        $request = $this->getRequest();

        $request->setUri($url);
        $request->setMethod($method);

        if ($data) {
            $request->setPost(new Parameters($data));
        }

        if ($files) {
            $formatFiles = [];
            foreach ($files as $formname => $filename) {
                $formatFiles[] = [
                    'formname' => $formname,
                    'ctype' => mime_content_type($filename),
                    'data' => file_get_contents($filename),
                    'filename' => $filename
                ];
            }
            $request->setFiles(new Parameters($formatFiles));
        }
        /** @var Response $response */
        $response = $this->httpClient->dispatch($request);
        if (!$response->isSuccess()) {
            // la réponse retournée par l'API contient le problème rencontré, on l'injecte dans l'exception
            throw new ApiRestResponseException($response);
        }

        return $response;
    }

    /**
     * @return \Laminas\Http\Client|null
     */
    public function getHttpClient(): ?HttpClient
    {
        return $this->httpClient;
    }
}