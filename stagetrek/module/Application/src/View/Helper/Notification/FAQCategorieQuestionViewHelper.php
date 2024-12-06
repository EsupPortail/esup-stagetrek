<?php


namespace Application\View\Helper\Notification;

use Application\Controller\Notification\FaqCategorieController as Controller;
use Application\Entity\Db\FaqCategorieQuestion;
use Application\Entity\Traits\Notification\HasFaqCategorieQuestionTrait;
use Application\Misc\ArrayRessource;
use Application\Provider\Misc\Icone;
use Application\Provider\Misc\Label;
use Application\Provider\Privilege\FaqPrivileges;
use Application\View\Helper\Interfaces\AbstractEntityActionViewHelper;
use Laminas\Form\Form;

class FAQCategorieQuestionViewHelper extends AbstractEntityActionViewHelper
{
    use HasFaqCategorieQuestionTrait;

    /**
     * @param FaqCategorieQuestion|null $faqCategorieQuestion
     * @return self
     */
    public function __invoke(FaqCategorieQuestion $faqCategorieQuestion = null): static
    {
        $this->faqCategorieQuestion = $faqCategorieQuestion;
        return $this;
    }


    public function actionAllowed(string $action) : bool
    {
        $ressources = new ArrayRessource();
        if($this->hasFaqCategorieQuestion()){
            $ressources->add(FaqCategorieQuestion::RESOURCE_ID, $this->getFaqCategorieQuestion());
        }
        return match ($action) {
            Controller::ACTION_AJOUTER => $this->hasPrivilege(FaqPrivileges::FAQ_CATEGORIE_AJOUTER),
            Controller::ACTION_MODIFIER => $this->hasFaqCategorieQuestion() && $this->hasPrivilege(FaqPrivileges::FAQ_CATEGORIE_MODIFIER),
            Controller::ACTION_SUPPRIMER => $this->hasFaqCategorieQuestion() && $this->callAssertion($ressources, FaqPrivileges::FAQ_CATEGORIE_SUPPRIMER),
            default => false,
        };
    }

    function renderForm(Form $form): string
    {
        $params=['form' => $form];
        return $this->getView()->render("application/notification/faq-categorie/forms/form-faq-categorie", $params);
    }

    /**
     * @param array|null $entities
     * @param array|null $params
     * @return string
     */
    public function renderListe(?array $entities = [], ?array $params = []) : string
    {
        $params = array_merge(['categories' => $entities], $params);
        return $this->getView()->render('application/notification/faq-categorie/listes/liste-faq-categories-questions', $params);
    }

    public function lienAjouter(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_AJOUTER)) {
            return "";
        }
        $url = $this->getUrl(Controller::ROUTE_AJOUTER, [], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::AJOUTER, Label::AJOUTER);
        $attributes['title'] = ($attributes['title']) ??  "Ajouter une catégorie de question";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-success ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_AJOUTER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienModifier(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_MODIFIER)) {
            return "";
        }
        $categorie = $this->getFaqCategorieQuestion();
        $url = $this->getUrl(Controller::ROUTE_MODIFIER, ['faqCategorieQuestion' => $categorie->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::MODIFIER, Label::MODIFIER);
        $attributes['title'] = ($attributes['title']) ??"Modifier la catégorie de question";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-primary ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ?? Controller::EVENT_MODIFIER;
        return $this->generateActionLink($url, $libelle, $attributes);
    }

    public function lienSupprimer(?string $libelle = null, ?array $attributes = []): string
    {
        if (!$this->actionAllowed(Controller::ACTION_SUPPRIMER)) {
            return "";
        }
        $categorie = $this->getFaqCategorieQuestion();
        $url = $this->getUrl(Controller::ROUTE_SUPPRIMER, ['faqCategorieQuestion' => $categorie->getId()], [], true);
        $libelle = ($libelle) ?? sprintf("%s %s", Icone::SUPPRIMER, Label::SUPPRIMER);
        $attributes['title'] = ($attributes['title']) ?? "Supprimer la categorie";
        $attributes['class'] = ($attributes['class']) ?? "btn btn-danger ajax-modal";
        $attributes['data-event'] = ($attributes['data-event']) ??  Controller::EVENT_SUPPRIMER;

        return $this->generateActionLink($url, $libelle, $attributes);
    }
}