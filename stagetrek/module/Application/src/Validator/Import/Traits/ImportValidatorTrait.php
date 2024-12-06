<?php

namespace Application\Validator\Import\Traits;

use Application\Validator\Import\Interfaces\AbstractCsvImportValidator;
use Application\Validator\Import\Interfaces\CsvImportValidatorInterface;

trait ImportValidatorTrait
{
    /** @var CsvImportValidatorInterface|null $importValidator */
    protected ?CsvImportValidatorInterface $importValidator = null;

    /**
     * @return  AbstractCsvImportValidator
     */
    public function getImportValidator(): ?CsvImportValidatorInterface
    {
        return $this->importValidator;
    }

    /**
     * @param CsvImportValidatorInterface $importValidator
     */
    public function setImportValidator(CsvImportValidatorInterface $importValidator) : static
    {
        $this->importValidator = $importValidator;
        return $this;
    }
}