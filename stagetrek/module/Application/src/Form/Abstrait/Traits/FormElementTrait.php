<?php

namespace Application\Form\Abstrait\Traits;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Laminas\View\HelperPluginManager;

trait FormElementTrait
{
    use ProvidesObjectManager;

    /**
     * @var HelperPluginManager
     */
    protected $viewHelperManager;

    /**
     * @param HelperPluginManager $viewHelperManager
     * @return FormElementTrait
     */
    public function setViewHelperManager($viewHelperManager)
    {
        $this->viewHelperManager = $viewHelperManager;

        return $this;
    }

    /**
     * @return HelperPluginManager
     */
    public function getViewHelperManager()
    {
        return $this->viewHelperManager;
    }

    /**
     * Generates a url given the name of a route.
     *
     * @see    RouteInterface::assemble()
     *
     * @param  string            $name               Name of the route
     * @param  array             $params             Parameters for the link
     * @param  array|\Traversable $options            Options for the route
     * @param  bool              $reuseMatchedParams Whether to reuse matched parameters
     *
     * @return string Url                         For the link href attribute
     */
    protected function getUrl($name = null, $params = [], $options = [], $reuseMatchedParams = false)
    {
        $urlVh = $this->getViewHelperManager()->get('url');
        /* @var $urlVh \Laminas\View\Helper\Url */
        return $urlVh->__invoke($name, $params, $options, $reuseMatchedParams);
    }

    /**
     * @return string
     */
    protected function getCurrentUrl($forceCanonical = false)
    {
        return $this->getUrl(null, [], ['force_canonical' => $forceCanonical], true);
    }
}