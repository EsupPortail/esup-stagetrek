<?php

namespace Application\View\Helper\Misc;

use Laminas\View\Helper\AbstractHelper;

/**
 * TODO : revoir si je l'utilise encore après avoir fait toute mes pages avec le formulaire de demande de confirmation. a priori non
 * Class PopAjaxConfirmationViewHelper
 * @package Application\View\Helper\Messages
 */
class PopAjaxConfirmationViewHelper extends AbstractHelper
{
    /**
     * @var string
     */
    private $href;

    /**
     * @var string
     */
    private $html;

    /**
     * @var array
     */
    private $class;

    /**
     * @var string
     */
    private $title;

    /**
     * @var array
     */
    private $popAjax;

    /**
     * @var string
     */
    private $event;


    /**
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * @param string $href
     * @return PopAjaxConfirmationViewHelper
     */
    public function setHref($href)
    {
        $this->href = $href;
        return $this;
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @param string $html
     * @return PopAjaxConfirmationViewHelper
     */
    public function setHtml($html)
    {
        $this->html = $html;
        return $this;
    }

    /**
     * @return array
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param array $class
     * @return PopAjaxConfirmationViewHelper
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return PopAjaxConfirmationViewHelper
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return array
     */
    public function getPopAjax()
    {
        return $this->popAjax;
    }

    /**
     * @param array $popAjax
     * @return PopAjaxConfirmationViewHelper
     */
    public function setPopAjax($popAjax)
    {
        $this->popAjax = $popAjax;
        return $this;
    }

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param string $event
     * @return PopAjaxConfirmationViewHelper
     */
    public function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * @param string $href url appelé par la popAjax de confirmation
     * @param string $html contenu du lien
     * @param array $popAjax contenu et titre de la popAjax
     * @param array $class classe(s) ajouté(s) sur le lien
     * @param string $title attribut title de lien
     * @param string $event nom d'un évènement à transmettre
     * @return self
     */
    public function __invoke($href, $html, $popAjax = [], $class = [], $title = null, $event = null)
    {
        $this->setHref($href);
        $this->setHtml($html);
        $this->setClass($class);
        $this->setTitle($title);
        $this->setPopAjax($popAjax);
        $this->setEvent($event);

        return $this;
    }

    public function __toString()
    {
        return $this->render();
    }

    public function render()
    {
        return sprintf(
            '<a href="%s" 
                class="pop-ajax %s"
                data-confirm="true"
                data-toggle="tooltip"  
                %s
                data-placement="%s"
                %s 
                data-content="
                    %s
                    <div class=\'text-center\'>
                        <div class=\'btn-group\'>
                            <button type=\'submit\' class=\'btn btn-success mx-5\'><i class=\'fas fa-check\'></i> Oui</button>
                            <button type=\'button\' class=\'btn btn-danger pop-ajax-hide mx-5\'><i class=\'fas fa-times\'></i> Non</button>
                        </div>
                    </div>"
                data-submit-close="true"
                %s
                data-confirm-button=""
                data-cancel-button="">
                %s</a>',
            $this->href,
            implode(" ", $this->class),
            $this->title ? sprintf('data-original-title="%s"', $this->title) : '',
            (isset($this->popAjax['placement'])) ? $this->popAjax['placement'] : 'top',
            (isset($this->popAjax['title'])) ? sprintf('data-title="%s"', $this->popAjax['title']) : '',
            (isset($this->popAjax['content'])) ? $this->popAjax['content'] : '',
            $this->event ? sprintf('data-submit-event="%s"', $this->event) : '',
            $this->html
        );
    }
}