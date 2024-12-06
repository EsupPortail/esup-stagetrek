<?php

namespace Application\Form\Misc\View\Helper;

use Laminas\Form\ElementInterface;
use Laminas\View\Helper\AbstractHelper;
use UnicaenApp\Form\Element\SearchAndSelect;

class FormControlText extends AbstractHelper
{
    /**
     * @var string
     */
    private string $prefixSuffixTemplate = '<div class="input-group-addon">%s</div>';

    /**
     * @var bool
     */
    protected bool $includeErrors = true;

    /**
     * @var string|null
     */
    protected ?string $prefix;

    /**
     * @var string|null
     */
    protected ?string $suffix;

    /**
     * @var array
     */
    protected array $helpContent = ['before' => '', 'after' => ''];

    /**
     * Invoke helper as function
     *
     * Proxies to {@link render()}.
     *
     * @param  ElementInterface|null $element
     * @return string|FormControlText
     */
    public function __invoke(ElementInterface $element = null)
    {
        $this->setHelpContent(null);
        $this->setPrefix(null);
        $this->setSuffix(null);

        if (!$element) {
            return $this;
        }

        return $this->render($element);
    }

    /**
     * Render an element
     *
     * Introspects the element type and attributes to determine which
     * helper to utilize when rendering.
     *
     * @param  ElementInterface $element
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $this->normalizeElement($element);

        // label
        $label = $this->labelHtml($element);

        // input
        $helperString = ($element instanceof SearchAndSelect)
            ? 'formSearchAndSelect'
            : 'formText';
        $helper = $this->getView()->plugin($helperString);

        $inputGroup = [];
        if ($this->getPrefix()) {
            $inputGroup[] = sprintf($this->prefixSuffixTemplate, $this->getPrefix());
        }
        $inputGroup[] = $helper($element);
        if ($this->getSuffix()) {
            $inputGroup[] = sprintf($this->prefixSuffixTemplate, $this->getSuffix());
        }

        $input = count($inputGroup) == 1
            ? $inputGroup[0]
            : sprintf('<div class="input-group">%s</div>', implode('', $inputGroup));


        $helpContentBefore = null;
        if (!empty($this->helpContent['before'])) {
            $helpContentBefore = sprintf('<p class="help-block help-block-before">%s<p>', $this->helpContent['before']);
        }
        $helpContentAfter = null;
        if (!empty($this->helpContent['after'])) {
            $helpContentAfter = sprintf('<p class="help-block help-block-after">%s<p>', $this->helpContent['after']);
        }

        // errors
        $helper = $this->getView()->plugin('formElementErrors');
        $errors = ($this->includeErrors) ? $helper($element, ['class' => 'error text-danger']) : '';

        // container class attribute
        $containerClass = ['form-group', 'has-feedback', 'has-clear'];
        $containerClass[] = $errors ? 'has-error' : null;
        $containerClass = implode(' ', $containerClass);


        $html = <<<EOT
<div class="$containerClass">
    $label
    $helpContentBefore
    $input
    <span class="form-control-clear form-control-feedback hidden" title="Effacer le champ texte">
        <i class="fas fa-times"></i>
    </span>
    $helpContentAfter
    $errors
</div>
<style>
    ::-ms-clear {
        display: none;
    }
    
    .form-control-clear {
        z-index: 10;
        pointer-events: auto;
        cursor: pointer;
    }
</style>
EOT;

        $html .= PHP_EOL . '<script>' . $this->getJavascript($element) . '</script>' . PHP_EOL;

        return $html;
    }

    /**
     * @param ElementInterface $element
     */
    private function normalizeElement(ElementInterface $element)
    {
        $class = $element->getAttribute('class');
        $labelClass = array_key_exists('class', $tmp = (array)$element->getLabelAttributes()) ? $tmp['class'] : '';
        $element
            ->setAttribute('class', $class . ' form-control')
            ->setLabelAttributes(['class' => $labelClass . ' control-label']);
    }

    /**
     * @param ElementInterface $element
     * @return string
     */
    private function labelHtml(ElementInterface $element)
    {
        $html = '';

        if ($element->getLabel()) {
            $helper = $this->getView()->plugin('formLabel');
            $html = $helper($element);
        }

        if ($element->hasAttribute('info_icon')) {
            $info = $element->getAttribute('info_icon');
            $html .= sprintf(
                '&nbsp;<span data-toggle="tooltip" data-placement="bottom" class="info-icon fas fa-info-circle" title="%s"></span>',
                $info);
        }

        return $html;
    }

    /**
     * @return string
     */
    private function getJavascript($element)
    {
        $js = '
            $(function() {
                $(\'.has-clear input[name="'. $element->getName() .'"]\')
                    .on("input propertychange", function() {
                        var clearButton = $(this).closest(".has-clear").children(".form-control-clear");
                        if($(this).prop("readonly") || $(this).prop("disabled")) {
                            clearButton.hide();
                        }
                        else {
                            var visible = Boolean($(this).val());
                            clearButton.toggleClass("hidden", !visible);
                        }
                    })
                    .trigger("propertychange");
            
                $(".form-control-clear")
                    .click(function() {
                        $(this).closest(".has-clear")
                            .find(\'input[name="'. $element->getName() .'"]\').val("")
                            .trigger("propertychange")
                            .focus();
                    });
             });
        ';

        return $js;
    }

    /**
     * @return boolean
     */
    public function getIncludeErrors()
    {
        return $this->includeErrors;
    }

    /**
     * Spécifie si les erreurs de validation éventuelles doivent être inclues ou non dans le rendu.
     *
     * @param bool $includeErrors
     * @return self
     */
    public function setIncludeErrors($includeErrors = true) : static
    {
        $this->includeErrors = $includeErrors;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string|null $prefix
     *
     * @return $this
     */
    public function setPrefix($prefix = null) : static
    {
        $this->prefix = $prefix;

        return $this;
    }



    /**
     * @return string|null
     */
    public function getSuffix()
    {
        return $this->suffix;
    }



    /**
     * @param string|null $suffix
     *
     * @return $this
     */
    public function setSuffix($suffix = null) : static
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * Spécifie le texte d'aide ou de description affiché dans un <code><p class="help-block"></code>.
     *
     * @param string $helpContent Texte d'aide
     * @param string $placement 'before' ou 'after'
     *
     * @return $this
     */
    public function setHelpContent($helpContent, $placement = 'after') : static
    {
        $this->helpContent[$placement] = $helpContent;

        return $this;
    }
}