<?php


namespace Application\Form\Groupe\Element;

use Application\Entity\Db\Groupe;
use Application\Form\Abstrait\Element\AbstractSelectPicker;

class GroupeSelectPicker extends AbstractSelectPicker
{
    public function setDefaultData() : static
    {
        $groupes = $this->getObjectManager()->getRepository(Groupe::class)->findAll();
//        Trie spécifique par année décroissante puis niveau d'étude croissant
        usort($groupes, function (Groupe $g1, Groupe $g2) {
            $a1 = $g1->getAnneeUniversitaire();
            $a2 = $g2->getAnneeUniversitaire();
            //On trie par date de fin des années en ordre décroissant puis par libellé si c'est la même
            if ($a1->getId() != $a2->getId()) {
                if ($a1->getDateDebut() < $a2->getDateDebut()) return 1;
                if ($a2->getDateDebut() < $a1->getDateDebut()) return -1;
                if ($a1->getDateFin() < $a2->getDateFin()) return 1;
                if ($a2->getDateFin() < $a1->getDateFin()) return -1;
                return strcmp($a1->getLibelle(), $a2->getLibelle());
            }
            //Si c'est la même année, on trie par niveau d'étude (ordre d'affichage puis libellé
            $n1 = $g1->getNiveauEtude();
            $n2 = $g2->getNiveauEtude();
            if ($n1->getId() != $n2->getId()) {
                if ($n1->getOrdre() < $n2->getOrdre()) return -1;
                if ($n2->getOrdre() < $n1->getOrdre()) return 1;
                return strcmp($n1->getLibelle(), $n2->getLibelle());
            }
            //Sinon, par libellé du groupe
            return strcmp($g1->getLibelle(), $g2->getLibelle());
        });
        $this->setGroupes($groupes);
        return $this;
    }

    public function setGroupes($groupes) : static
    {
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'] = [];
        $this->setOptions($inputOptions);
        foreach ($groupes as $g) {
            $this->addGroupe($g);
        }
        return $this;
    }

    //Permet nottament de modifier le label de la catégorie
    public function addAnneeUniversitaire($annee): static
    {
        if ($this->hasAnneeUniversitaire($annee)) return $this;
        $this->setAnneeUniversitaireOption($annee, 'label', $annee->getLibelle());
        $this->setAnneeUniversitaireOption($annee, 'options', []);
        return $this;
    }

    public function  addGroupe($groupe): static
    {
        if ($this->hasGroupe($groupe)) return $this;
        $annee = $groupe->getAnneeUniversitaire();
        if (!$this->hasAnneeUniversitaire($annee)) {
            $this->addAnneeUniversitaire($annee);
        }
        $this->setGroupeOption($groupe, 'label', $groupe->getLibelle());
        $this->setGroupeOption($groupe, 'value', $groupe->getId());
        return $this;
    }

    public function removeAnneeUniversitaire($annee): static
    {
        if (!$this->hasAnneeUniversitaire($annee)) return $this;
        $value_options = $this->getOption('value_options');
        unset($value_options[$annee->getId()]);
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'] = $value_options;
        $this->setOptions($inputOptions);
        return $this;
    }

    public function removeGroupe($groupe): static
    {
        if (!$this->hasGroupe($groupe)) return $this;
        $annee = $groupe->getAnneeUniversitaire();
        $value_options = $this->getOption('value_options');
        unset($value_options[$annee->getId()]['options'][$groupe->getId()]);
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'] = $value_options;
        $this->setOptions($inputOptions);
        return $this;
    }

    public function hasAnneeUniversitaire($annee) : bool
    {
        $value_options = $this->getOption('value_options');
        return ($value_options && key_exists($annee->getId(), $value_options));
    }

    public function hasGroupe($groupe) : bool
    {
        $annee = $groupe->getAnneeUniversitaire();
        if (!$this->hasAnneeUniversitaire($annee)) return false;
        $anneeOption = $this->getAnneeUniversitaireOptions($annee);
        return (key_exists('options', $anneeOption) && key_exists($groupe->getId(), $anneeOption['options']));
    }

    public function getAnneeUniversitaireOptions($annee) : array
    {
        if (!$this->hasAnneeUniversitaire($annee)) return [];
        $value_options = $this->getOption('value_options');
        return $value_options[$annee->getId()];
    }

    public function getAnneeUniversitaireAttributes($annee) : array
    {
        $anneeOptions = $this->getAnneeUniversitaireOptions($annee);
        if (!key_exists('attributes', $anneeOptions)) {
            return [];
        }
        return $anneeOptions['attributes'];
    }

    public function getGroupeOptions($groupe) : array
    {
        if (!$this->hasGroupe($groupe)) return [];
        $annee = $groupe->getAnneeUniversitaire();
        $anneeOptions = $this->getAnneeUniversitaireOptions($annee);
        return $anneeOptions['options'][$groupe->getId()];
    }

    public function getGroupeAttributes($groupe) : array
    {
        $groupeOptions = $this->getGroupeOptions($groupe);
        if (!key_exists('attributes', $groupeOptions)) {
            return [];
        }
        return $groupeOptions['attributes'];
    }

    public function setAnneeUniversitaireOption($annee, $key, $value): static
    {
        $options = $this->getAnneeUniversitaireOptions($annee);
        $options[$key] = $value;
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'][$annee->getId()] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    public function setGroupeOption($groupe, $key, $value): static
    {
        $options = $this->getGroupeOptions($groupe);
        $options[$key] = $value;
        $annee = $groupe->getAnneeUniversitaire();
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'][$annee->getId()]['options'][$groupe->getId()] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    //Permet nottament de rajouter des data-attributes
    public function setAnneeUniversitaireAttribute($annee, $key, $value): static
    {
        $attributes = $this->getAnneeUniversitaireAttributes($annee);
        $attributes[$key] = $value;
        $this->setAnneeUniversitaireOption($annee, 'attributes', $attributes);
        return $this;
    }

    public function setGroupeAttribute($groupe, $key, $value): static
    {
        $attributes = $this->getGroupeAttributes($groupe);
        $attributes[$key] = $value;
        $this->setGroupeOption($groupe, 'attributes', $attributes);
        return $this;
    }


    //permet d'ajouter l'option "Aucun groupe" - ie : pour les étudiants non inscrit
    public function setNoGroupeOption(string $label, int $value = -1) : static
    {
        $options = $this->getOptions();
        $valueOptions = ($options['value_options'])?? [];
        $noGroupeOption[] = [
            'label' => $label,
            'value' => $value,
        ];
        $valueOptions = array_merge($noGroupeOption, $valueOptions);
        $options['value_options']=$valueOptions;
        $this->setOptions($options);
        return $this;
    }
}