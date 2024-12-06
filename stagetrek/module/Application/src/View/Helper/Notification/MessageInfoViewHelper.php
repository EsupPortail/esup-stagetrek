<?php


namespace Application\View\Helper\Notification;

use Application\Controller\Notification\MessageInfoController as Controller;
use Application\Entity\Db\MessageInfo;
use Application\Entity\Traits\Notification\HasMessageInfoTrait;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Notification\MessageInfoProvider;
use Application\Provider\Privilege\MessagePrivilege;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Laminas\Form\Form;

/**
 * Class Mes
 */
class MessageInfoViewHelper extends AbstractEntityActionViewHelper
{
    use HasMessageInfoTrait;

    /**
     * @param MessageInfo|null $messageInfo
     * @return self
     */
    public function __invoke(MessageInfo $messageInfo = null): static
    {
        $this->messageInfo = $messageInfo;
        return $this;
    }

    /**
     * @return string
     */
    public function render() :string
    {
        return ($this->messageInfo) ? $this->messageInfo->getTitle(). " : ".$this->messageInfo->getMessage() : "";
    }

    public function actionAllowed(string $action) : bool
    {
        return match ($action) {
            Controller::ACTION_AJOUTER => $this->hasPrivilege(MessagePrivilege::MESSAGE_INFO_AJOUTER),
            Controller::ACTION_MODIFIER => $this->hasMessageInfo() && $this->hasPrivilege(MessagePrivilege::MESSAGE_INFO_MODIFIER),
            Controller::ACTION_SUPPRIMER => $this->hasMessageInfo() && $this->hasPrivilege(MessagePrivilege::MESSAGE_INFO_SUPPRIMER),
            default => false,
        };
    }

    function renderForm(Form $form): string
    {
        return $this->getView()->render("application/notification/message-info/forms/form-message-info", ['form' => $form]);
    }

    /**
     * @param array|null $entities
     * @param array|null $params
     * @return string
     */
    public function renderListe(?array $entities = [], ?array $params = []): string
    {
        $params = array_merge(['messages' => $entities], $params);
        return $this->getView()->render('application/notification/message-info/listes/liste-messages-infos',$params);
    }

    public function renderPriorityIcone(): string
    {
        if (!isset($this->messageInfo)) {
            return "<span class='icon icon-times-circle text-muted'></span>";
        }
        return match ($this->messageInfo->getPriority()) {
            MessageInfoProvider::INFO => "<span class='icon icon-info text-info'></span>",
            MessageInfoProvider::SUCCESS => "<span class='icon icon-check-circle text-success'></span>",
            MessageInfoProvider::WARNING => "<span class='icon icon-warning text-warning'></span>",
            MessageInfoProvider::ERROR => "<span class='icon icon-warning text-danger'></span>",
            default => "<span class='icon icon-times-circle text-muted'></span>",
        };
    }

    public function lienAjouter(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_AJOUTER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_AJOUTER, [], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::AJOUTER, Label::AJOUTER);
        $attributes['title'] = ($attributes['title']) ??  "Ajouter un message d'information";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_AJOUTER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $messageInfo = $this->getMessageInfo();
        $url = $this->getUrl(Controller::ROUTE_MODIFIER, ['messageInfo' => $messageInfo->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::MODIFIER, Label::MODIFIER);
        $attributes['title'] = ($attributes['title']) ??"Modifier le message d'information";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSupprimer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SUPPRIMER)) {
            return "";
        }
        $messageInfo = $this->getMessageInfo();
        $url = $this->getUrl(Controller::ROUTE_SUPPRIMER, ['messageInfo' => $messageInfo->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::SUPPRIMER, Label::SUPPRIMER);
        $attributes['title'] = ($attributes['title']) ?? "Supprimer le message d'information";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-danger ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_SUPPRIMER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }
}