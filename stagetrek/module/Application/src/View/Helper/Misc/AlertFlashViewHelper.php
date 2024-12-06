<?php

namespace Application\View\Helper\Misc;
use UnicaenApp\Exception\LogicException;
use UnicaenApp\View\Helper\Messenger;

/**
 * Class AlertFlashViewHelper
 * @package Application\View\Helper\Messages
 */
class AlertFlashViewHelper extends Messenger
{
    const FIXED_BOTTOM = 'bottom';
    const FIXED_TOP = 'top';

    /**
     * @var string[]
     */
    protected $fixedValues = [self::FIXED_BOTTOM, self::FIXED_TOP];

    /**
     * Fixation du mmessage en haut ou en bas de la page
     *
     * @var string
     */
    protected $fixed = self::FIXED_BOTTOM;

    /**
     * Durée d'affichage de l'alerte en ms
     *
     * @var integer
     */
    protected $duration = 3000;


    /**
     * Génère le code HTML.
     *
     * @return string
     */
    protected function render()
    {
        if (!$this->hasMessages()) {
            return '';
        }

        $htmlMessages = [];
        foreach ($this->getSortedMessages() as $severity => $array) {
            foreach ($array as $priority => $message) {
                $htmlMessages[] = sprintf(
                    $this->getTemplate(is_string($severity) ? $severity : (is_string($priority) ? $priority : 'info')),
                    implode('<br />', (array)$message)
                );
            }
        }

        $out = implode(PHP_EOL, $htmlMessages);

        $template = <<<EOT
{$out}

<script type="text/javascript">
$(document).ready(function() {
    var tab = $('.alert-flash');
    var size = tab.length;
    (function fade(i) {
        element = $(tab[i]);
        element.appendTo('body');
        if(i < size){
            element.slideDown(500).delay({$this->duration}).slideUp(500, function(){
                this.remove();
                fade(++i);
            });
        }
    })(0);
});
</script>
EOT;

        return $template . PHP_EOL;
    }

    /**
     * @param string $severity
     * @param null $containerId
     * @return string
     */
    public function getTemplate($severity, $containerId = null)
    {
        if (!isset($this->uiClasses[$severity])) {
            throw new LogicException("Sévérité inconnue: " . $severity);
        }

        $containerId = ($containerId) ?: uniqid('alert-div-');
        $alertClass = sprintf('alert-%s', $this->uiClasses[$severity][0]);
        $iconMarkup = $this->withIcon ? "<span class=\"glyphicon glyphicon-{$this->uiClasses[$severity][1]}\"></span> " : null;

        $template = <<<EOT
<div id="{$containerId}" class="alert-flash alert {$alertClass} navbar-fixed-{$this->fixed} fade in" role="alert" style="display: none;">
    <div class="container">
        <p class="text-center">
            $iconMarkup
            <span class="message">%s</span></p>
    </div>
</div>
EOT;

        return $template . PHP_EOL;
    }

    /**
     * @return string
     */
    public function getFixed()
    {
        return $this->fixed;
    }

    /**
     * @param string $fixed
     */
    public function setFixed($fixed)
    {
        if (!in_array($fixed, $this->fixedValues)) {
            throw new LogicException("Valeurs autorisées: " . implode(', ', $this->fixedValues));
        }

        $this->fixed = $fixed;

        return $this;
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
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }
}