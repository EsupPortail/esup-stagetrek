<?php

namespace Application\Service\Renderer\Factory;

use Application\Service\Renderer\UrlService;
use Interop\Container\ContainerInterface;

class UrlServiceFactory {

    /**
     * @param ContainerInterface $container
     * @return UrlService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container) : UrlService
    {
        $service = new UrlService();
        $service->setRenderer($container->get('ViewRenderer'));

        $options     = $container->get('config');
        if(isset($options['StageTrek']['http_client']['uri_host'])){
            $service->setUriHost($options['StageTrek']['http_client']['uri_host']);
        }
        if(isset($options['StageTrek']['http_client']['uri_scheme'])){
            $service->setUriScheme($options['StageTrek']['http_client']['uri_scheme']);
        }
        return $service;
    }
}
