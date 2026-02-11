<?php


namespace Application\Form\Stages\Fieldset;

use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\Groupe;
use Application\Entity\Traits\AnneeUniversitaire\HasAnneeUniversitaireTrait;
use Application\Entity\Traits\Groupe\HasGroupeTrait;
use Application\Entity\Traits\Stage\HasSessionStageTrait;
use Application\Form\Groupe\Element\GroupeSelectPicker;
use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Interfaces\DefaultInputKeyInterface;
use Application\Form\Misc\Interfaces\HasTagInputInterface;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\Misc\Traits\LibelleInputAwareTrait;
use Application\Form\Misc\Traits\TagInputAwareTrait;
use Application\Form\Stages\Validator\SessionStageValidator;
use Application\Provider\Tag\CategorieTagProvider;
use DateTime;
use Exception;
use Laminas\Filter\DateTimeSelect;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Date;
use Laminas\Validator\Callback;
use UnicaenTag\Entity\Db\Tag;

/**
 * Class SessionStageFieldset
 * @package Application\Form\SessionsStages\Fieldset
 */
class
PeriodeStageFieldset extends AbstractEntityFieldset
{
    use IdInputAwareTrait;


    public function init() : static
    {
        $this->initIdInput();
        $this->initDatesInputs();
        return $this;
    }

    use HasSessionStageTrait;

    const DATE_DEBUT = "dateDebut";
    const DATE_FIN = "dateFin";
    private function initDatesInputs() : void
    {
        $this->add([
            'name' => self::DATE_DEBUT,
            'type' => Date::class,
            'options' => [
                'label' => "Date de début",
            ],
            'attributes' => [
                'id' => self::DATE_DEBUT,
            ],
        ]);

        $this->add([
            'name' => self::DATE_FIN,
            'type' => Date::class,
            'options' => [
                'label' => "Date de fin",
            ],
            'attributes' => [
                'id' => self::DATE_FIN,
            ],
        ]);

        $this->setInputfilterSpecification(self::DATE_DEBUT, [
            'name' => self::DATE_DEBUT,
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                'name' => Callback::class,
                'options' => [
                    'messages' => [
                        Callback::INVALID_VALUE => "La date de début doit précédé la date de fin",
                    ],
                    'callback' => function ($value, $context = []) {
                        $date1 = ($context[self::DATE_DEBUT]) ?? null;
                        $date2 = ($context[self::DATE_FIN]) ?? null;
                        if(!isset($date1) || !isset($date2) || $date1==""||$date2==""){
                            return true; //False mais pas a cause de la précédances
                        }
                        $date1 = DateTime::createFromFormat('Y-m-d', $date1);
                        $date2 = DateTime::createFromFormat('Y-m-d', $date2);
                        if(!$date1 || !$date2){return false;} //cas de date mal formée
                        return $date1 < $date2;
                    }
                ],],
            ],
        ]);

        $this->setInputfilterSpecification(self::DATE_FIN, [
            'name' => self::DATE_FIN,
            'required' => true,
        ]);

        $this->setInputfilterSpecification(DefaultInputKeyInterface::ID, [
            'name' => DefaultInputKeyInterface::ID,
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
            'validators' => [
                [
                'name' => Callback::class,
                'options' => [
                    'messages' => [
                        Callback::INVALID_VALUE => "La période de stage doit être incluse dans les dates de la session.",
                    ],
                    'callback' => function ($value, $context = []) {
                        $debut = ($context[self::DATE_DEBUT]) ?? null;
                        $fin = ($context[self::DATE_FIN]) ?? null;
                        if(!isset($debut)|| $debut=="" || !isset($fin) || $fin==""){
                            return true; //False mais pas a cause de la précédances
                        }
                        $debut = DateTime::createFromFormat('Y-m-d', $debut);
                        $fin = DateTime::createFromFormat('Y-m-d', $fin);
                        $session = $this->getSessionStage();
                        $date1 = $session->getDateDebutStage();
                        $date2 = $session->getDateFinStage();
                        return ($date1 <= $debut && $fin <= $date2);
                    }
                ]],
            ]
        ]);

    }

}