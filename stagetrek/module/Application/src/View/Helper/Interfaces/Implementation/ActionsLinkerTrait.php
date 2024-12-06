<?php


namespace Application\View\Helper\Interfaces\Implementation;

use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\View\Renderer\RendererInterface as Renderer;
use UnicaenPrivilege\Provider\Privilege\Privileges;

/**
 * @method Renderer getView()
 */
trait ActionsLinkerTrait
{
    public function getUrl($name = null, array $params = array(), $options = array(), $reuseMatchedParams = false) :string
    {
        return $this->getView()->url($name, $params, $options, $reuseMatchedParams);
    }

    public function generateActionLink(?string $url=null, ?string $libelle=null, array $attributes=[]) : string
    {
        $attr = "";
        //Rajout éventuel de tooltip
        if (isset($attributes['title'])) {
            if (!isset($attributes['data-bs-toggle'])) {
                $attributes['data-bs-toggle'] = 'tooltip';
            }
            if (!isset($attributes['data-bs-html'])) {
                $attributes['data-bs-html'] = 'true';
            }
            if (!isset($attributes['data-bs-placement'])) {
                $attributes['data-bs-placement'] = 'bottom';
            }
        }
        foreach ($attributes as $key => $value) {
            $attr .= sprintf("%s='%s' ", $key, $this->view->escapeHtml($value));
        }
        $url = (isset($url) && $url != "") ? "href='" . $url . "'" : "";
        return sprintf("<a %s %s>%s</a>", $url, $attr, $libelle);
    }


    function actionAllowed(string $action): bool
    {
        return false;//Par défaut
    }

    /**
     * @param string|null $privilege
     * @return bool
     */
    public function hasPrivilege(?string $privilege): bool
    {
        if(!isset($privilege)){return false;}
        return $this->getView()->isAllowed(Privileges::getResourceId($privilege));
    }

    public function callAssertion(ResourceInterface $resource, string $privilege = null): bool
    {
        return $this->getView()->isAllowed($resource, $privilege);
    }

}