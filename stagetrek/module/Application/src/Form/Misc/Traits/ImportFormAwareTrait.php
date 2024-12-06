<?php


namespace Application\Form\Misc\Traits;

use Application\Form\Misc\ImportForm;

/**
 * Traits ImportFormAwareTrait
 * @package Application\Form\Confirmation\Traits
 */
trait ImportFormAwareTrait
{
    /**
     * @var ImportForm|null $importForm
     */
    protected ?ImportForm $importForm = null;

    /**
     * @return ImportForm
     */
    public function getImportForm(): ImportForm
    {
        return $this->importForm;
    }

    /**
     * @param ImportForm $importForm
     */
    public function setImportForm(ImportForm $importForm): void
    {
        $this->importForm = $importForm;
    }

}