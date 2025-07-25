<?php

namespace Application\Service\ConventionStage;

use Application\Entity\Db\Stage;
use Application\Entity\Traits\Convention\HasConventionStageTrait;
use DateTime;
use Exception;
use UnicaenFichier\Entity\Db\Fichier;
use UnicaenFichier\Filter\FileName\AbstractFileNameFormatter;
use UnicaenFichier\Filter\FileName\FileNameFormatterInterface;

/**
 * @desc Met mes fichiers dans un sous-répertoire au format du code de la nature
 */
Class ConventionStageFileNameFormatter extends AbstractFileNameFormatter implements FileNameFormatterInterface
{
    const DATA_SEPARATOR = "_";
    use HasConventionStageTrait;
//    CHoix fait : Numéro de l'étudiant _ id du stage _ date d'upload _ uid du fichier.pdf

    function getFileName(Fichier $fichier) : string
    {
        if(!$this->hasConventionStage()){
            throw new Exception("ConventionStage non définie");
        }
        $date =  new DateTime();
        $uid = $fichier->getId();
        $convention = $this->getConventionStage();
        $stage = $convention->getStage();
        $etudiant = $stage->getEtudiant();
        $name = 'convention';
        $name .= self::PATH_SEPARATOR . $etudiant->getNumEtu();
        $name .= self::DATA_SEPARATOR. $stage->getId();
        $name .= self::DATA_SEPARATOR.  $date->format('Ymd-His');
        $name .= self::DATA_SEPARATOR. $uid;
        $name .= '.pdf';
        return $name;
    }

    // Pour renommer les nom originaux des fichiers aux formats similaires a ceux fournis, sans l'UID du fichier
    public function getOriginalFilename(): string
    {
        if(!$this->hasConventionStage()){
            throw new Exception("ConventionStage non définie");
        }
        $convention = $this->getConventionStage();
        $stage = $convention->getStage();
        $etudiant = $stage->getEtudiant();
        $name =  "convention-stage";
        $name .= self::DATA_SEPARATOR. $etudiant->getNumEtu();
        $name .= self::DATA_SEPARATOR. str_replace('.', "-", $stage->getNumero(true));
        $name .= '.pdf';
        return $name;

    }
}