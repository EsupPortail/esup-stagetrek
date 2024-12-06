<?php

namespace Application\View\Renderer;

use Laminas\View\Helper\AbstractHelper;

class FlashMessageDisplayViewHelper extends AbstractHelper
{
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    public function render()
    {
        $html = '';
        $fm = $this->view->flashMessage();
        foreach ($fm->getUiClasses() as $severity => $class) {
            if ($fm->hasMessages($severity)) {
                $html .=
                    '<div class="flash-msg alert alert-'.$class.' alert-dismissible fade in show">
                        <div class="container">
                            <i class="icon-'.$class.'"></i>'
                            . $fm->render($severity, [], false) . '
                        </div>
                    </div>';
                $html .= '
                <script type="text/javascript">
                    $(function() {
                        if (!$(\'#flash-message\').is(\':empty\')) {
                            $(\'#flash-message .alert\').delay('.$fm->getDuration().').fadeOut();
                            $(\'#flash-message\').animate({opacity: 1});
                        }
                    });
                </script>
                ';
                $fm->clearCurrentMessagesFromContainer();
            }
        }
        return $html;
    }
}