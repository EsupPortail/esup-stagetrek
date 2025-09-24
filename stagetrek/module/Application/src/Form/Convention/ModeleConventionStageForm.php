<?php
namespace Application\Form\Convention;

use Application\Form\Abstrait\AbstractEntityForm;
use Application\Form\Convention\Fieldset\ModeleConventionStageFieldset;
use UnicaenRenderer\Entity\Db\Template;
use UnicaenRenderer\Service\Macro\MacroServiceAwareTrait;
use UnicaenRenderer\Service\TemplateEngineManager\TemplateEngineManager;
use UnicaenRenderer\Service\TemplateEngineManager\TemplateEngineManagerAwareTrait;

class ModeleConventionStageForm extends AbstractEntityForm
{

    use MacroServiceAwareTrait;
    use TemplateEngineManagerAwareTrait;

//    TODO : a revoir choix fait pour le moment de forcer à utiliser le templateEngine par défaut (macro sous la forme VAR[])
    public function generateMacrosJsonValue(): string
    {

        $templateEngine = $this->templateEngineManager->get(Template::TEMPLATE_ENGINE_DEFAULT);
        $macros = $this->macroService->getMacros();

        $array = [];
        foreach ($macros as $macro) {
            //$description = $macro->getDescription()?strip_tags(str_replace("'", "\'", $macro->getDescription())):"";
            $array[] = [
                'id' => $macro->getCode(),
                'text' => $macro->getCode(),
                'description' => $macro->getDescription() ?: 'Aucune description',
                'value' => $templateEngine->generateMacroSourceCode($macro),
            ];
        }

        return json_encode($array, JSON_HEX_APOS | JSON_HEX_QUOT);
    }


    public function init() : static
    {
        parent::init();
        $fieldset = $this->getFormFactory()->getFormElementManager()->get(ModeleConventionStageFieldset::class);
        $this->setEntityFieldset($fieldset);
        return $this;
    }

}
