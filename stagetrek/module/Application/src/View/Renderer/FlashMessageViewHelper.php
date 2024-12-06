<?php

namespace Application\View\Renderer;

use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger as PluginFlashMessenger;
use Laminas\Mvc\Plugin\FlashMessenger\View\Helper\FlashMessenger;

class FlashMessageViewHelper extends FlashMessenger
{
    protected $uiClasses = [
        PluginFlashMessenger::NAMESPACE_DEFAULT => 'default',
        PluginFlashMessenger::NAMESPACE_ERROR   => 'danger',
        PluginFlashMessenger::NAMESPACE_INFO    => 'info',
        PluginFlashMessenger::NAMESPACE_SUCCESS => 'success',
        PluginFlashMessenger::NAMESPACE_WARNING => 'warning',
    ];

    protected $severityOrder = [
        PluginFlashMessenger::NAMESPACE_SUCCESS => 1,
        PluginFlashMessenger::NAMESPACE_ERROR   => 2,
        PluginFlashMessenger::NAMESPACE_WARNING => 3,
        PluginFlashMessenger::NAMESPACE_INFO    => 4,
        PluginFlashMessenger::NAMESPACE_DEFAULT => 5,
    ];

    /**
     * Durée d'affichage de l'alerte en ms
     *
     * A spécifier dans la vue où on affiche le message
     * Ex: $this->flashMessage()->setDuration(30000);
     *
     * @var integer
     */
    protected $duration = 5000;


    /**
     * @return array
     */
    public function getUiClasses()
    {
        $classes  = $this->uiClasses;
        $order    = $this->severityOrder;

        uksort($classes, function ($s1, $s2) use ($order) {
            if ($order[$s1] < $order[$s2]) {
                return -1;
            }
            if ($order[$s1] > $order[$s2]) {
                return 1;
            }

            return 0;
        });

        return $classes;
    }

    /**
     * @param string $severity
     * @return mixed
     */
    public function getUiClass($severity = PluginFlashMessenger::NAMESPACE_DEFAULT)
    {
        $key = array_key_exists($severity, $this->uiClasses) ? $severity : PluginFlashMessenger::NAMESPACE_DEFAULT;
        return $this->uiClasses[$key];
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     * @return $this
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }
}