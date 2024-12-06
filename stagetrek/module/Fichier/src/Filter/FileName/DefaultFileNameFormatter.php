<?php

namespace Fichier\Filter\FileName;

use DateTime;
use Fichier\Entity\Db\Fichier;

/**
 * @desc Interface permettant de définir comment nommer les fichiers
 * A définir dans la config ['fichier' => [ 'file-name-formatter' => DefaultFileNameFormatter::classe ]],
 * L'objectif est de pouvoir personnaliser comment on nomme nos fichier
 */
Class DefaultFileNameFormatter extends AbstractFileNameFormatter implements FileNameFormatterInterface
{
    function getFileName(Fichier $fichier) : string
    {
        $date = new DateTime();
        $uid = $fichier->getId();
        $name = $fichier->getNomOriginal();
        return $date->format('Ymd-His') . self::DATA_SEPARATOR. $uid . self::DATA_SEPARATOR. $name;
    }

}