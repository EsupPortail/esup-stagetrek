<?php

namespace Fichier\Filter\FileName;

use DateTime;
use Fichier\Entity\Db\Fichier;

/**
 * @desc Met mes fichiers dans un sous-répertoire au format du code de la nature
 */
Class NatureBasedFileNameFormatter extends AbstractFileNameFormatter implements FileNameFormatterInterface
{
    function getFileName(Fichier $fichier) : string
    {
        $date = new DateTime();
        $uid = $fichier->getId();
        $name = $fichier->getNomOriginal();

        $nature = $fichier->getNature();
        $codeNature = trim($nature->getCode());
        $codeNature = str_replace(' ', self::DATA_SEPARATOR, $codeNature);
        return $codeNature . self::PATH_SEPARATOR
            . $date->format('Ymd-His') . self::DATA_SEPARATOR
            . $uid . self::DATA_SEPARATOR
            . $name;
    }

}