<?php


namespace Application\Form\Stages\Element;

use Application\Entity\Db\Groupe;
use Application\Entity\Db\SessionStage;
use Application\Form\Abstrait\Element\AbstractSelectPicker;

class SessionStageSelectPicker extends AbstractSelectPicker
{

    public function setDefaultData() : static
    {
        $sessions = $this->getObjectManager()->getRepository(SessionStage::class)->findAll();
//        trie spécigique par année en ordre décroissant puis groupe en ordre croissant puis par dates des sessions
        usort($sessions, function (SessionStage $s1, SessionStage $s2) {
            $a1 = $s1->getAnneeUniversitaire();
            $a2 = $s2->getAnneeUniversitaire();
            if($a1->getId() != $a2->getId()) {
                if($a1->getDateFin() < $a2->getDateFin()){return -1;}
                if($a2->getDateFin() < $a1->getDateFin()){return 1;}
                if($a1->getDateDebut() < $a2->getDateDebut()){return -1;}
                if($a2->getDateDebut() < $a1->getDateDebut()){return 1;}
            }

            //Trie par groupe en priorité
            $g1 = $s1->getGroupe();
            $g2 = $s2->getGroupe();
            $sortGroupe = Groupe::sortGroupes([$g1, $g2]);
            if($sortGroupe[0] != $g1){return 1;}
            //Sinon, par ordre croissant sur les date ou par libellé
            if ($s1->getDateDebutStage() < $s2->getDateDebutStage()) return -1;
            if ($s2->getDateDebutStage() < $s1->getDateDebutStage()) return 1;
            if ($s1->getDateFinStage() < $s2->getDateFinStage()) return -1;
            if ($s2->getDateFinStage() < $s1->getDateFinStage()) return 1;
            return strcmp($s1->getLibelle(), $s2->getLibelle());
        });
        $this->setSessions($sessions);
        return $this;
    }

    public function setSessions($sessions): static
    {
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'] = [];
        $this->setOptions($inputOptions);
        foreach ($sessions as $s) {
            $this->addSession($s);
        }
        return $this;
    }

    public function addGroupe(Groupe $groupe): static
    {
        if ($this->hasGroupe($groupe)) return $this;
        $annee = $groupe->getAnneeUniversitaire();
        $this->setGroupeOption($groupe, 'label', $groupe->getLibelle()." (".$annee->getLibelle().")");
        $this->setGroupeOption($groupe, 'options', []);
        return $this;
    }

    public function addSession(SessionStage $session): static
    {
        if ($this->hasSession($session)) return $this;
        $groupe = $session->getGroupe();
        if (!$this->hasGroupe($groupe)) {
            $this->addGroupe($groupe);
        }
        $this->setSessionOption($session, 'label', $session->getLibelle());
        $this->setSessionOption($session, 'value', $session->getId());
        return $this;
    }

    public function removeGroupe($groupe): static
    {
        if (!$this->hasGroupe($groupe)) return $this;
        $value_options = $this->getOption('value_options');
        unset($value_options[$groupe->getId()]);
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'] = $value_options;
        $this->setOptions($inputOptions);
        return $this;
    }

    public function removeSession($session): static
    {
        if (!$this->hasSession($session)) return $this;
        $groupe = $session->getAnneeUniversitaire();
        $value_options = $this->getOption('value_options');
        unset($value_options[$groupe->getId()]['options'][$session->getId()]);
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'] = $value_options;
        $this->setOptions($inputOptions);
        return $this;
    }

    public function hasGroupe(Groupe $groupe): bool
    {
        $value_options = $this->getOption('value_options');
        return ($value_options && key_exists($groupe->getId(), $value_options));
    }

    public function hasSession(SessionStage $session): bool
    {
        $groupe = $session->getGroupe();
        if (!$this->hasGroupe($groupe)) return false;
        $groupeOption = $this->getGroupeOptions($groupe);
        return (key_exists('options', $groupeOption) && key_exists($session->getId(), $groupeOption['options']));
    }

    public function getGroupeOptions(Groupe $groupe)
    {
        if (!$this->hasGroupe($groupe)) return [];
        $value_options = $this->getOption('value_options');
        return $value_options[$groupe->getId()];
    }

    public function getGroupeAttributes(Groupe $groupe)
    {
        $groupeOptions = $this->getGroupeOptions($groupe);
        if (!key_exists('attributes', $groupeOptions)) {
            return [];
        }
        return $groupeOptions['attributes'];
    }

    public function getSessionOptions(SessionStage $session) : array
    {
        if (!$this->hasSession($session)) return [];
        $groupe = $session->getGroupe();
        $groupeOptions = $this->getGroupeOptions($groupe);
        return $groupeOptions['options'][$session->getId()];
    }

    public function getSessionAttributes(SessionStage $session) : array
    {
        $sessionOptions = $this->getSessionOptions($session);
        if (!key_exists('attributes', $sessionOptions)) {
            return [];
        }
        return $sessionOptions['attributes'];
    }

    public function setGroupeOption(Groupe $groupe, $key, $value): static
    {
        $options = $this->getGroupeOptions($groupe);
        $options[$key] = $value;
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'][$groupe->getId()] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    public function setSessionOption(SessionStage $session, $key, $value): static
    {
        $options = $this->getSessionOptions($session);
        $options[$key] = $value;
        $groupe = $session->getGroupe();
        $inputOptions = $this->getOptions();
        $inputOptions['value_options'][$groupe->getId()]['options'][$session->getId()] = $options;
        $this->setOptions($inputOptions);
        return $this;
    }

    //Permet nottament de rajouter des data-attributes
    public function setGroupeAttributeAttribute(Groupe $groupe, $key, $value): static
    {
        $attributes = $this->getGroupeAttributes($groupe);
        $attributes[$key] = $value;
        $this->setGroupeOption($groupe, 'attributes', $attributes);
        return $this;
    }

    public function setSessionAttribute(SessionStage $session, $key, $value): static
    {
        $attributes = $this->getSessionAttributes($session);
        $attributes[$key] = $value;
        $this->setSessionOption($session, 'attributes', $attributes);
        return $this;
    }
}