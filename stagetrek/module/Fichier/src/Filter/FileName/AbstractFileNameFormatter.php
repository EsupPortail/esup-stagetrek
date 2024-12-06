<?php

namespace Fichier\Filter\FileName;

use Fichier\Entity\Db\Fichier;

/**
 * @desc Interface permettant de définir comment nommer les fichiers
 * A définir dans la config ['fichier' => [ 'file-name-formatter' => DefaultFileNameFormatter::classe ]],
 * L'objectif est de pouvoir personnaliser comment on nomme nos fichier
 */
Abstract class AbstractFileNameFormatter implements FileNameFormatterInterface
{

    const PATH_SEPARATOR = "/";
    const DATA_SEPARATOR = "-";

   abstract function getFileName(Fichier $fichier): string;

}