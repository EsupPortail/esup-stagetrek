<?php

namespace Application\Entity\Db;

use Application\Entity\Interfaces\LibelleEntityInterface;
use Application\Entity\Traits\AnneeUniversitaire\HasAnneeUniversitaireTrait;
use Application\Entity\Traits\Etudiant\HasEtudiantsTrait;
use Application\Entity\Traits\InterfaceImplementation\IdEntityTrait;
use Application\Entity\Traits\InterfaceImplementation\LibelleEntityTrait;
use Application\Entity\Traits\Parametre\HasNiveauEtudeTrait;
use Application\Entity\Traits\Stage\HasSessionsStagesTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * Groupe
 */
class Groupe implements ResourceInterface,
    LibelleEntityInterface
{
    const RESOURCE_ID = 'Groupe';

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    use IdEntityTrait;
    use LibelleEntityTrait;
    use HasAnneeUniversitaireTrait;
    use HasNiveauEtudeTrait;
    use HasEtudiantsTrait;
    use HasSessionsStagesTrait;

    /**
     * @var \Application\Entity\Db\Groupe|null
     */
    protected ?Groupe $groupePrecedent = null;

    /**
     * @var \Application\Entity\Db\Groupe|null
     */
    protected ?Groupe $groupeSuivant = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initEtudiantsCollection();
        $this->initSessionsStagesCollection();
    }

    /** Fonction de trie d'un tableau de groupe */
    static function sortGroupes(array $groupes): array
    {
        usort($groupes, function (Groupe $g1, Groupe $g2) {
            $a1 = $g1->getAnneeUniversitaire();
            $a2 = $g2->getAnneeUniversitaire();
            //On trie par date de fin des années en ordre décroissant puis par libellé si c'est la même
            if ($a1->getId() != $a2->getId()) {
                if ($a1->getDateDebut() < $a2->getDateDebut()) return -1;
                if ($a2->getDateDebut() < $a1->getDateDebut()) return 1;
                if ($a1->getDateFin() < $a2->getDateFin()) return -1;
                if ($a2->getDateFin() < $a1->getDateFin()) return 1;
                return ($a1->getId() < $a2->getId()) ? -1 : 1;
            }
            //Si c'est la même année, on trie par niveau d'étude (ordre d'affichage puis libellé
            $n1 = $g1->getNiveauEtude();
            $n2 = $g2->getNiveauEtude();
            if ($n1->getId() != $n2->getId()) {
                if ($n1->getOrdre() < $n2->getOrdre()) return -1;
                if ($n2->getOrdre() < $n1->getOrdre()) return 1;
                return ($n1->getId() < $n2->getId()) ? -1 : 1;
            }
            //Sinon, par libellé du groupe
            return strcmp($g1->getLibelle(), $g2->getLibelle());
        });
        return $groupes;
    }


    /**
     * Surcharges des accesseurs d'étudiants
     * @param \Application\Entity\Db\Etudiant $etudiant
     * @return \Application\Entity\Db\Groupe
     */
    public function removeEtudiant(Etudiant $etudiant) : static
    {
        if ($this->etudiants->contains($etudiant)) {
            $this->etudiants->removeElement($etudiant);
            $etudiant->removeGroupe($this);
            /** @var SessionStage $s */
            foreach ($this->sessionsStages as $s) {
                $s->removeEtudiant($etudiant);
                $etudiant->removeSessionStage($s);
            }
            return $this;
        }
        return $this;
    }
    /**
     * @param \Application\Entity\Db\Etudiant $etudiant
     * @return Groupe
     */
    public function addEtudiant(Etudiant $etudiant): static
    {
        if (!$this->etudiants->contains($etudiant)) {
            $this->etudiants[] = $etudiant;
            $etudiant->addGroupe($this);
        }
        return $this;
    }


    /**
     * Get groupePrecedent.
     *
     * @return \Application\Entity\Db\Groupe|null
     */
    public function getGroupePrecedent(): ?Groupe
    {
        return $this->groupePrecedent;
    }

    /**
     * Set groupePrecedent.
     *
     * @param Groupe|null $groupePrecedent
     *
     * @return Groupe
     */
    public function setGroupePrecedent(Groupe $groupePrecedent = null): static
    {
        $this->groupePrecedent = $groupePrecedent;
        return $this;
    }

    /**
     * Get groupeSuivant.
     *
     * @return \Application\Entity\Db\Groupe|null
     */
    public function getGroupeSuivant(): ?Groupe
    {
        return $this->groupeSuivant;
    }

    /**
     * Set groupeSuivant.
     *
     * @param Groupe|null $groupeSuivant
     *
     * @return Groupe
     */
    public function setGroupeSuivant(Groupe $groupeSuivant = null): static
    {
        $this->groupeSuivant = $groupeSuivant;
        return $this;
    }
}
