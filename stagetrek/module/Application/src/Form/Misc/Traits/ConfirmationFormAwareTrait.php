<?php


namespace Application\Form\Misc\Traits;

use Application\Form\Misc\ConfirmationForm;
use Laminas\Http\PhpEnvironment\Request;

/**
 * Traits ConfirmationFormAwareTrait
 * @package Application\Form\Confirmation\Traits
 */
trait ConfirmationFormAwareTrait
{
    /**
     * @var ConfirmationForm|null $confirmationForm
     */
    protected ?ConfirmationForm $confirmationForm = null;

    /**
     * @return ConfirmationForm
     */
    public function getConfirmationForm(): ConfirmationForm
    {
        return $this->confirmationForm;
    }

    /**
     * @param ConfirmationForm $confirmationForm
     */
    public function setConfirmationForm($confirmationForm) : static
    {
        $this->confirmationForm = $confirmationForm;
        return $this;
    }

    /** @return bool */
    public function actionConfirmed(): bool
    {
        $form = $this->getConfirmationForm();
        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            if (isset($data[$form::INPUT_RESPONSE]) &&  $data[$form::INPUT_RESPONSE] == $form::CONFIRM_VALUE) {
                $form->setHasBeenConfirmed(true);
                return true;
            }
        }
        $form->setHasBeenConfirmed(false);
        return false;
    }

    public function actionCancelled(): bool
    {
        $form = $this->getConfirmationForm();
        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            if (isset($data[$form::INPUT_RESPONSE]) &&  $data[$form::INPUT_RESPONSE] == $form::CANCEL_VALUE) {
                return true;
            }
        }
        return false;
    }

}