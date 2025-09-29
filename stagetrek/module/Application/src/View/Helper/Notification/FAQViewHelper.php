<?php


namespace Application\View\Helper\Notification;

use Application\Controller\Notification\FaqQuestionController as Controller;
use Application\Entity\Db\Faq;
use Application\Entity\Traits\Notification\HasFaqQuestionTrait;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\FaqPrivileges;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Laminas\Form\Form;

class FAQViewHelper extends AbstractEntityActionViewHelper
{

    use HasFaqQuestionTrait;

    /**
     * @param Faq|null $faq
     * @return self
     */
    public function __invoke(Faq $faq = null): static
    {
        $this->question = $faq;
        return $this;
    }

    public function toString() : string
    {
        return $this->render();
    }

    public function render() : string
    {
        if (!$this->question) return "";
        return $this->getView()->render("application/notification/faq-question/partial/question", ['question' => $this->getQuestion()]);
    }

    public function actionAllowed(string $action) : bool
    {
        return match ($action) {
            Controller::ACTION_AJOUTER => $this->hasPrivilege(FaqPrivileges::FAQ_QUESTION_AJOUTER),
            Controller::ACTION_MODIFIER => $this->hasQuestion() && $this->hasPrivilege(FaqPrivileges::FAQ_QUESTION_MODIFIER),
            Controller::ACTION_SUPPRIMER => $this->hasQuestion() && $this->hasPrivilege(FaqPrivileges::FAQ_QUESTION_SUPPRIMER),
            default => false,
        };
    }

    function renderForm(Form $form): string
    {
        return $this->getView()->render("application/notification/faq-question/forms/form-faq-question", ['form' => $form]);
    }

    /**
     * @param array|null $entities
     * @param array|null $params
     * @return string
     */
    public function renderListe(?array $entities = [], ?array $params = []) : string
    {
        $params = array_merge(['questions' => $entities], $params);
        return $this->getView()->render('application/notification/faq-question/listes/liste-faq-questions', $params);
    }


    public function lienAjouter(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_AJOUTER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_AJOUTER, [], [], true);
        $libelle = ($libelle) ?? Label::render(Label::AJOUTER, Icone::AJOUTER);
        $attributes['title'] = ($attributes['title']) ??  "Ajouter une question";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_AJOUTER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $question = $this->getQuestion();
        $url = $this->getUrl(Controller::ROUTE_MODIFIER, ['faq' => $question->getId()], [], true);
        $libelle = ($libelle) ?? Label::render(Label::MODIFIER, Icone::MODIFIER);
        $attributes['title'] = ($attributes['title']) ??"Modifier la question";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSupprimer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SUPPRIMER)) {
            return "";
        }
        $question = $this->getQuestion();
        $url = $this->getUrl(Controller::ROUTE_SUPPRIMER, ['faq' => $question->getId()], [], true);
        $libelle = Label::render(Label::SUPPRIMER, Icone::SUPPRIMER);
        $attributes['title'] = ($attributes['title']) ?? "Supprimer la question";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-danger ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_SUPPRIMER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }

}