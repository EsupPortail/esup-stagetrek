<?php


namespace Application\Form\Parametre\Fieldset;

use Application\Entity\Db\NiveauEtude;
use Application\Form\Misc\Abstracts\AbstractEntityFieldset;
use Application\Form\Misc\Traits\IdInputAwareTrait;
use Application\Form\Misc\Traits\LibelleInputAwareTrait;
use Application\Form\Misc\Traits\OrdreInputAwareTrait;
use Application\Form\Parametre\Element\NiveauEtudeSelectPicker;
use Laminas\Filter\ToInt;
use Laminas\Form\Element\Number;
use Laminas\Validator\Callback;

/**
 * Class NiveauEtudeFieldset
 */
class NiveauEtudeFieldset extends AbstractEntityFieldset
{
    use IdInputAwareTrait;
    use LibelleInputAwareTrait;
    use OrdreInputAwareTrait;


    const NIVEAU_ETUDE_PARENT = "niveauEtudeParent";
    const NB_STAGES = "nbStages";
    const LABEL_NIVEAU_ETUDE_PARENT = "Niveau d'étude précédent";
    const LABEL_NB_STAGES = "Nombre de stage(s)";


    const EMPTY_OPTION_TEXT_NIVEAU_ETUDE_PARENT = "";

    public function init(): void
    {
        $this->initIdInput();
        $this->initLibelleInput();
        $this->initOrdreInput();
        $this->initNiveauEtudePrecedentInput();
        $this->initNbStages();
    }

    protected function initNiveauEtudePrecedentInput() : static
    {
        $value_options = [];
        $value_options[] = [
            'value' => 0,
            'label' => 'Aucun',
        ];
        /** @var NiveauEtude $niveauEtude */
        foreach ($this->getObjectManager()->getRepository(NiveauEtude::class)->findBy([], ['ordre' => 'ASC', 'libelle' => 'ASC']) as $niveauEtude) {
            if ($niveauEtude->getNiveauEtudeSuivant() != null) {
                continue;
            } //On ne propose pas les niveau ayant déjà un fils
            $value_options[] = [
                'value' => $niveauEtude->getId(),
                'label' => $niveauEtude->getLibelle()
            ];
        }

        $this->add([
            'type' => NiveauEtudeSelectPicker::class,
            'name' => self::NIVEAU_ETUDE_PARENT,
            'options' => [
                'label' => self::LABEL_NIVEAU_ETUDE_PARENT,
                'value_options' => $value_options,
            ],
            'attributes' => [
                'id' => self::NIVEAU_ETUDE_PARENT,
            ],
        ]);

        $this->setInputfilterSpecification(self::NIVEAU_ETUDE_PARENT, [
            'name' => self::NIVEAU_ETUDE_PARENT,
            "required" => false,
            "filters" => [
                ["name" => ToInt::class],
            ],
            'validators' => [
                [//Note : transformer en un vrai validateur si plus complexe
                    'name' => Callback::class,
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => "Niveau d'étude précédent non valide.",
                        ],
                        'callback' => function ($value, $context = []) {
                            if (!key_exists(self::NIVEAU_ETUDE_PARENT, $context) ||
                                $context[self::NIVEAU_ETUDE_PARENT] == 0) {
                                return true;
                            }
                            /** @var NiveauEtude $precedent */
                            $precedent = $this->getObjectManager()->getRepository(NiveauEtude::class)->find($value);
                            if (!$precedent) {
                                return false;
                            } //Niveau demandé non trouvé
                            if (!key_exists(self::ID, $context)) {
                                return true;
                            } //Faux mais parce qu'il n'y a pas de champs ID, sécurité pour éviter une erreur lors du test d'après
                            $currentId = intval($context[self::ID]);
                            if ($precedent->getId() == $currentId) {
                                return false;
                            } //Auto-selection
                            if ($precedent->getNiveauEtudeSuivant() != null &&
                                $precedent->getNiveauEtudeSuivant()->getId() != $currentId
                            ) {
                                return false;
                            } //Le niveau en question est déjà le parent d'un niveau d'étude

                            //Pour interdire les cycles
                            $secu = 20;
                            $ancetre = $precedent;
                            while ($ancetre) {
                                if ($secu-- == 0) { //Bloque le nombre de niveau d'étude qui ce suivent à 20; a priori largement suffisament, permet surtout d'éviter une bloucle infini en cas de pb
                                    return false;
                                }
                                if ($ancetre->getId() == $currentId) {
                                    return false;
                                }
                                $ancetre = $ancetre->getNiveauEtudeParent();
                            }
                            return true;
                        },
                        'break_chain_on_failure' => true,
                    ],
                ],
            ],
        ]);
        return $this;
    }


    public function fixerNiveauEtude(NiveauEtude $niveauEtude): static
    {
        if(!$niveauEtude->getId()){return $this;}
        $value_options = [];
        $value_options[] = [
            'value' => 0,
            'label' => 'Aucun',
        ];
        /** @var NiveauEtude $n */
        foreach ($this->getObjectManager()->getRepository(NiveauEtude::class)->findBy([], ['ordre' => 'ASC', 'libelle'=> 'ASC']) as $n) {
            if($n->getId() == $niveauEtude->getId()){continue;}
            if($niveauEtude->getNiveauEtudeParent() && $niveauEtude->getNiveauEtudeParent()->getId() == $n->getId()){
                //On garde le niveau d'étude parent actuel dans la liste
                $value_options[] = [
                    'value' => $n->getId(),
                    'label' => $n->getLibelle()
                ];
                continue;
            }
            if($n->getNiveauEtudeSuivant() != null){continue;} //On ne propose pas les niveau ayant déjà un fils
            //Pour interdire les cycles, a priori on aura pas une formation sur 20 ans
            $secu=20;
            $ancetre = $n->getNiveauEtudeParent();
            $cycle = false;
            while($ancetre){
                if($secu--==0){ //Bloque le nombre de niveau d'étude qui ce suivent à 20; a priori largement suffisament, permet surtout d'éviter une bloucle infini en cas de pb
                    $cycle=true;
                    break;
                }
                if($ancetre->getId()==$niveauEtude->getId()){
                    $cycle=true;
                    break;
                }
                $ancetre = $ancetre->getNiveauEtudeParent();
            }
            if($cycle){continue;}
            $value_options[] = [
                'value' => $n->getId(),
                'label' => $n->getLibelle()
            ];
        }
        /** @var ?NiveauEtudeSelectPicker $input */
        $input =  $this->get(self::NIVEAU_ETUDE_PARENT);
        $input->setOption('value_options',$value_options);
        $input->setValueOptions($value_options);
        return $this;
    }

    protected function initNbStages() : static
    {
        $this->add([
            "name" => self::NB_STAGES,
            "type" => Number::class,
            "options" => [
                "label" => "Nombre de stage(s)",
            ],
            "attributes" => [
                "id" => self::NB_STAGES,
                "min" => 0,
            ],
        ]);

        $this->setInputfilterSpecification(self::NB_STAGES, [
                "name" => self::NB_STAGES,
                "required" => true,
                "filters" => [
                    ["name" => ToInt::class],
                ],
        ]);
        return $this;
    }

}