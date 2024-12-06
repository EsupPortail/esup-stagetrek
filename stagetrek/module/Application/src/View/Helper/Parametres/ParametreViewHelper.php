<?php


namespace Application\View\Helper\Parametres;

use Application\Controller\Parametre\ParametreController as Controller;
use Application\Entity\Db\Parametre;
use Application\Entity\Traits\Parametre\HasParametreTrait;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\ParametrePrivileges;
use Application\Service\Parametre\Traits\ParametreServiceAwareTrait;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Laminas\Form\Form;

/**
 * Class ParametreStageViewHelper
 */
class ParametreViewHelper extends AbstractEntityActionViewHelper
{
    use HasParametreTrait;
    use ParametreServiceAwareTrait;

    /**
     * @param Parametre|null $parametre
     * @return self
     */
    public function __invoke(?Parametre $parametre = null) : static
    {
        $this->parametre = $parametre;
        return $this;
    }

    public function renderListe(?array $entities = [], ?array $params=[]): string
    {
        $params = array_merge(['parametres' => $entities], $params);
        return $this->getView()->render('application/parametre/parametre/listes/liste-parametres',$params);
    }

    function renderForm(Form $form): string
    {
        return $this->getView()->render("application/parametre/parametre/forms/form-parametre", ['form' => $form]);
    }

    function actionAllowed(string $action): bool
    {
        return match ($action) {
            Controller::ACTION_MODIFIER => $this->hasParametre() && $this->hasPrivilege(ParametrePrivileges::PARAMETRE_MODIFIER),
            default => false,
        };
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $parametre = $this->getParametre();
        $url = $this->getUrl(Controller::ROUTE_MODIFIER, ['parametre' => $parametre->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::MODIFIER, Label::MODIFIER);
        $attributes['title'] = ($attributes['title']) ??"Modifier le parametre";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

}