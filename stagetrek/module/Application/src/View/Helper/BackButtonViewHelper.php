<?php

namespace Application\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class BackButtonViewHelper extends AbstractHelper
{
    protected $libelle;

    /**
     * @param mixed $value
     * @return mixed|string
     */
    public function __invoke($libelle="Retour")
    {
        $this->libelle = $libelle;
        return $this;
    }

    /**
     * @return string|null
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * @return string|null
     */
    public function render()
    {
        return
            sprintf('<a class="btn btn-back"
                title="Retour">
                <i class="fas fa-reply"></i> %s
            </a>', $this->libelle);
    }

    public function backTo($url){
        return sprintf(
            '<a class="btn btn-back"    
                href="%s"
                >
                <i class="fas fa-reply"></i> %s
            </a>', $url, $this->libelle
        );
    }
}