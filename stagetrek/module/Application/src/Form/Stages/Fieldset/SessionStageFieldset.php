<?php


namespace Application\Form\Stages\Fieldset;

use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\Groupe;
use Application\Entity\Traits\AnneeUniversitaire\HasAnneeUniversitaireTrait;
use Application\Entity\Traits\Groupe\HasGroupeTrait;
use Application\Form\Groupe\Element\GroupeSelectPicker;
use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\Misc\Traits\LibelleInputAwareTrait;
use Application\Form\Stages\Validator\SessionStageValidator;
use Laminas\Filter\DateTimeSelect;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Date;

/**
 * Class SessionStageFieldset
 * @package Application\Form\SessionsStages\Fieldset
 */
class SessionStageFieldset extends AbstractEntityFieldset
{
    use IdInputAwareTrait;
    use LibelleInputAwareTrait;


    public function init() : static
    {
        $this->initIdInput();
        $this->initLibelleInput();
        $this->initGroupeInput();
        $this->initDatesInputs();
        $this->initPropertiesInputs();
        return $this;
    }
    use HasGroupeTrait;
    use HasAnneeUniversitaireTrait;
    public function setGroupe(Groupe $groupe) : static
    {
        /** @var GroupeSelectPicker $input */
        $input = $this->get(self::GROUPE);
        $input->setGroupes([$groupe]);
        $input->setEmptyOption(null);
        $input->setAttribute('data-live-search', false);
        return $this;
    }

    public function setAnneeUniversitaire(AnneeUniversitaire $anneeUniversitaire) : static
    {
        /** @var GroupeSelectPicker $input */
        $input = $this->get(self::GROUPE);
        $groupes = $this->getObjectManager()->getRepository(Groupe::class)->findBy(['anneeUniversitaire' => $anneeUniversitaire]);
        $groupes = Groupe::sortGroupes($groupes);
        $input->setGroupes($groupes);
        return $this;
    }

    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     */

    const GROUPE = "groupe";

    private function initGroupeInput() : void
    {
        $this->add([
            'type' => GroupeSelectPicker::class,
            'name' => self::GROUPE,
            'options' => [
                'label' => "Groupe",
                'empty_option' => "Sélectionner un groupe",
            ],
            'attributes' => [
                'id' => self::GROUPE,
            ],
        ]);

        //Par défaut, on bloque les groupes si l'année universitaire est vérouillée
        /** @var GroupeSelectPicker $input */
        $input = $this->get(self::GROUPE);
        /** @var AnneeUniversitaire[] $annees */
        $annees = $this->getObjectManager()->getRepository(AnneeUniversitaire::class)->findBy(['anneeVerrouillee' => true]);
        foreach ($annees as $a){
            $input->removeAnneeUniversitaire($a);
        }

        $this->setInputfilterSpecification(self::GROUPE, [
            "name" => self::GROUPE,
            'required' => true,
            'validators' => [
                [
                    'name' => SessionStageValidator::class,
                    'options' => [
                        'callback' => SessionStageValidator::ASSERT_GROUPE,
                        'break_chain_on_failure' => false,
                    ],
                ],
            ],
        ]);

    }

    const DATE_CALCUL_ORDRES_AFFECTACTIONS = "dateCalculOrdresAffectations";
    const DATE_DEBUT_CHOIX = "dateDebutChoix";
    const DATE_FIN_CHOIX = "dateFinChoix";
    const DATE_COMMISSION = "dateCommission";
    const DATE_DEBUT_STAGE = "dateDebutStage";
    const DATE_FIN_STAGE = "dateFinStage";
    const DATE_DEBUT_VALIDATION = "dateDebutValidation";
    const DATE_FIN_VALIDATION = "dateFinValidation";
    const DATE_DEBUT_EVALUATION = "dateDebutEvaluation";
    const DATE_FIN_EVALUATION = "dateFinEvaluation";
    const INPUT_CALCUL_AUTOMATIQUE_DATE = "calculAutoDates";
    private function initDatesInputs() : void
    {
        $this->add([
            'name' => self::DATE_CALCUL_ORDRES_AFFECTACTIONS,
            'type' => Date::class,
            'options' => [
                'label' => "Calcul automatique des ordres d'affectations le",
            ],
            'attributes' => [
                'id' => self::DATE_CALCUL_ORDRES_AFFECTACTIONS,
            ],
        ]);

        $this->add([
            'name' => self::DATE_DEBUT_CHOIX,
            'type' => Date::class,
            'options' => [
                'label' => "Début des choix le",
            ],
            'attributes' => [
                'id' => self::DATE_DEBUT_CHOIX,
            ],
        ]);
        $this->add([
            'type' => Date::class,
            'name' => self::DATE_FIN_CHOIX,
            'options' => [
                'label' => "Fin le",
            ],
            'attributes' => [
                'id' => self::DATE_FIN_CHOIX,
            ],
        ]);

        $this->add([
            'type' => Date::class,
            'name' => self::DATE_COMMISSION,
            'options' => [
                'label' => "Date de la commission",
            ],
            'attributes' => [
                'id' => self::DATE_COMMISSION,
            ],
        ]);

        $this->add([
            'type' => Date::class,
            'name' => self::DATE_DEBUT_STAGE,
            'options' => [
                'label' => "Début des stages le",
            ],
            'attributes' => [
                'id' => self::DATE_DEBUT_STAGE,
            ],
        ]);
        $this->add([
            'type' => Date::class,
            'name' => self::DATE_FIN_STAGE,
            'options' => [
                'label' => "Fin le",
            ],
            'attributes' => [
                'id' => self::DATE_FIN_STAGE,
            ],
        ]);

        $this->add([
            'type' => Date::class,
            'name' => self::DATE_DEBUT_VALIDATION,
            'options' => [
                'label' => "Début des validations par le responsable du stage le",
            ],
            'attributes' => [
                'id' => self::DATE_DEBUT_VALIDATION,
            ],
        ]);
        $this->add([
            'type' => Date::class,
            'name' => self::DATE_FIN_VALIDATION,
            'options' => [
                'label' => "Fin le",
            ],
            'attributes' => [
                'id' => self::DATE_FIN_VALIDATION,
            ],
        ]);

        $this->add([
            'type' => Date::class,
            'name' => self::DATE_DEBUT_EVALUATION,
            'options' => [
                'label' => "Début des évaluations par les étudiants le",
            ],
            'attributes' => [
                'id' => self::DATE_DEBUT_EVALUATION,
            ],
        ]);
        $this->add([
            'type' => Date::class,
            'name' => self::DATE_FIN_EVALUATION,
            'options' => [
                'label' =>  "Fin le",
            ],
            'attributes' => [
                'id' => self::DATE_FIN_EVALUATION,
            ],
        ]);


        $this->add([
            'name' => self::INPUT_CALCUL_AUTOMATIQUE_DATE,
            'type' => Checkbox::class,
            'options' => [
                'label' => "Calcul automatique des dates",
                'use_hidden_element' => true,
                'checked_value' => "1",
                'unchecked_value' => "0",
            ],
            'attributes' => [
                'id' => self::INPUT_CALCUL_AUTOMATIQUE_DATE,
                'value' => 0,
                'class' => 'form-check-input'
            ],
        ]);

        $assertions = [
            self::DATE_CALCUL_ORDRES_AFFECTACTIONS => SessionStageValidator::ASSERT_DATE_CALCUL_ORDRES_AFFECTATIONS,
            self::DATE_DEBUT_CHOIX => SessionStageValidator::ASSERT_DATE_DEBUT_CHOIX,
            self::DATE_FIN_CHOIX => SessionStageValidator::ASSERT_DATE_FIN_CHOIX,
            self::DATE_COMMISSION => SessionStageValidator::ASSERT_DATE_COMMISSION,
            self::DATE_DEBUT_STAGE => SessionStageValidator::ASSERT_DATE_DEBUT_STAGE,
            self::DATE_FIN_STAGE => SessionStageValidator::ASSERT_DATE_FIN_STAGE,
            self::DATE_DEBUT_VALIDATION => SessionStageValidator::ASSERT_DATE_DEBUT_VALIDATION,
            self::DATE_FIN_VALIDATION => SessionStageValidator::ASSERT_DATE_FIN_VALIDATION,
            self::DATE_DEBUT_EVALUATION => SessionStageValidator::ASSERT_DATE_DEBUT_EVALUATION,
            self::DATE_FIN_EVALUATION => SessionStageValidator::ASSERT_DATE_FIN_EVALUTATION,
        ];

        foreach ($assertions as $inputId => $callback){
            $this->setInputfilterSpecification($inputId,[
                "name" => $inputId,
                'required' => true,
                'filters' => [
                    ['name' => DateTimeSelect::class],
                ],
                'validators' => [
                    [
                        'name' => SessionStageValidator::class,
                        'options' => [
                            'callback' => $callback,
                            'break_chain_on_failure' => false,
                        ],
                    ],
                ],
            ]);
        }
    }

    const INPUT_SESSION_RATTRAPAGE = "isSessionRattrapge";
    private function initPropertiesInputs(): void
    {
        $this->add([
            'name' => self::INPUT_SESSION_RATTRAPAGE,
            'type' => Checkbox::class,
            'options' => [
                'label' =>  "Session de rattrapage",
                'use_hidden_element' => true,
                'checked_value' => "1",
                'unchecked_value' => "0",
            ],
            'attributes' => [
                'id' => self::INPUT_SESSION_RATTRAPAGE,
                'value' => 0,
                'class' => 'form-check-input'
            ],
        ]);
    }


}