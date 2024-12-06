<?php


namespace Application\Form\Annees\Element;

use Application\Entity\Db\AnneeUniversitaire;
use Application\Form\Abstrait\Element\AbstractSelectPicker;

class AnneeUniversitaireSelectPicker extends AbstractSelectPicker
{

    public function setDefaultData() : static
    {
        $annees = $this->getObjectManager()->getRepository(AnneeUniversitaire::class)->findAll();
        usort($annees, function (AnneeUniversitaire $a1, AnneeUniversitaire $a2) {
            //On trie par date de fin des années puis date de début en ordre décroissant puis par libellé si c'est la même
            if ($a1->getId() != $a2->getId()) {
                if ($a1->getDateFin() < $a2->getDateFin()) return 1;
                if ($a2->getDateFin() < $a1->getDateFin()) return -1;
            }
            return strcmp($a1->getLibelle(), $a2->getLibelle());
        });
        $this->setAnneesUniversitaires($annees);
        return $this;
    }

    public function setAnneesUniversitaires($annees) : static
    {
        $inputOptions = $this->getOptions();
        $inputOptions['options'] = [];
        $this->setOptions($inputOptions);
        foreach ($annees as $a) {
            $this->addAnneeUniversitaire($a);
        }
        return $this;
    }

    public function addAnneeUniversitaire($annee) : static
    {
        if ($this->hasAnneeUniversitaire($annee)) return $this;
        $this->setAnneeUniversitaireOption($annee, 'label', $annee->getLibelle());
        $this->setAnneeUniversitaireOption($annee, 'value', $annee->getId());
        return $this;
    }

    public function removeAnneeUniversitaire($annee): static
    {
        if (!$this->hasAnneeUniversitaire($annee)) return $this;
        $options = $this->getOption('options');
        unset($options[$annee->getId()]);
        $inputOptions = $this->getOptions();
        $inputOptions['options'] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    /** Ensembles de fonctions qui permette de modifier les options de l'input */
    public function hasAnneeUniversitaire($annee) : bool
    {
        $options = $this->getOption('options');
        return ($options && key_exists($annee->getId(), $options));
    }

    public function getAnneeUniversitaireOptions($annee)
    {
        if (!$this->hasAnneeUniversitaire($annee)) return [];
        $options = $this->getOption('options');
        return $options[$annee->getId()];
    }

    public function getAnneeUniversitaireAttributes($annee)
    {
        $options = $this->getAnneeUniversitaireOptions($annee);
        if (!key_exists('attributes', $options)) {
            return [];
        }
        return $options['attributes'];
    }

    public function setAnneeUniversitaireOption($annee, $key, $value) : static
    {
        $options = $this->getAnneeUniversitaireOptions($annee);
        $options[$key] = $value;
        $inputOptions = $this->getOptions();
        $inputOptions['options'][$annee->getId()] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    public function setAnneeUniversitaireAttribute($annee, $key, $value) : static
    {
        $attributes = $this->getAnneeUniversitaireAttributes($annee);
        $attributes[$key] = $value;
        $this->setAnneeUniversitaireOption($annee, 'attributes', $attributes);
        return $this;
    }

//permet d'ajouter l'option "Aucun groupe" - ie : pour les étudiants non inscrit
    public function setNoAnneeOption(string $label, int $value = -1) : static
    {
        $options = $this->getOptions();
        $valueOptions = ($options['options'])?? [];
        $noGroupeOption[] = [
            'label' => $label,
            'value' => $value,
        ];
        $valueOptions = array_merge($noGroupeOption, $valueOptions);
        $options['options']=$valueOptions;
        $this->setOptions($options);
        return $this;
    }
}