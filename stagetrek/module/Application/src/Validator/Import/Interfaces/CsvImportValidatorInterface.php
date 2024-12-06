<?php

namespace Application\Validator\Import\Interfaces;


/**
 * @desc Validateur permettant de déterminer si un fichier d'import est valide
 * @package Application\Validator\Import
 */
interface CsvImportValidatorInterface
{
    public function isValid(mixed $value): bool;
        /** @return array */
    public static function getImportHeader() : array;

    public function getNotAllowedImportMessage(): string;
    public function getErrorMessage() : ?string;
    public function getExceptionMessage() : ?string;

}
