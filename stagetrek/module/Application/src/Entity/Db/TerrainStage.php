<?php

namespace Application\Entity\Db;

use Application\Entity\Db;
use Application\Entity\Interfaces\HasCodeInterface;
use Application\Entity\Interfaces\HasAdresseInterface;
use Application\Entity\Interfaces\HasLibelleInterface;
use Application\Entity\Traits\Adresse\HasAdresseTrait;
use Application\Entity\Traits\Contact\HasContactsTerrainsTrait;
use Application\Entity\Traits\Convention\HasModeleConventionStageTrait;
use Application\Entity\Traits\InterfaceImplementation\HasCodeTrait;
use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use Application\Entity\Traits\InterfaceImplementation\HasLibelleTrait;
use Application\Entity\Traits\Terrain\HasCategorieStageTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use UnicaenTag\Entity\Db\HasTagsInterface;
use UnicaenTag\Entity\Db\HasTagsTrait;

/**
 * TerrainStage
 */
class TerrainStage implements ResourceInterface,
    HasCodeInterface, HasLibelleInterface,
    HasAdresseInterface
    , HasTagsInterface
{
    const RESOURCE_ID = 'TerrainStage';

    const TYPE_TERRAIN_PRINCIPAL = 'Principal';
    const TYPE_TERRAIN_SECONDAIRE = 'Secondaire';


    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    public function generateDefaultCode(array $param = []) : string
    {
        $uid = ($param['uid']) ?? $this->getId();
        if($uid == null){$uid = uniqid();}
        if(isset($param['prefixe'])){$prefixe = $param['prefixe'];}
        elseif(!$this->hasCategorieStage() || $this->getCategorieStage() === null || $this->getCategorieStage()==""){$prefixe = 'T';}
        else{$prefixe = strtolower(substr(trim($this->getCategorieStage()->getAcronyme()), 0,10));
            $prefixe = str_replace(" ", "_", $prefixe);
        }
        $separateur = ($param['sep']) ?? '-';
        return substr(sprintf("%s%s%s", $prefixe, $separateur, $uid), 0, 25);
    }

    use HasIdTrait;
    use HasCodeTrait;
    use HasLibelleTrait;
    use HasCategorieStageTrait;
    use HasAdresseTrait;
    use HasContactsTerrainsTrait;
    use HasTagsTrait;

    protected Collection $terrainsPrincipaux;
    protected Collection $terrainsSecondaires;

    /**
     * @var string|null
     */
    protected ?string $service = null;

    /**
     * @var int
     */
    protected int $minPlace = 0;
    /**
     * @var int
     */
    protected int $idealPlace = 0;
    /**
     * @var int
     */
    protected int $maxPlace = 0;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected Collection $niveauxEtudesContraints;
    /**
     * @var bool|null
     */
    protected ?bool $actif = true;

    use HasModeleConventionStageTrait;

    /**
     * @var bool|null
     */
    protected ?bool $preferencesAutorisees = true;
    /**
     * @var string|null
     */
    protected ?string $infos = null;
    /**
     * @var string|null
     */
    protected ?string $lien = null;



    protected bool $horsSubdivision = false;
    /** @var bool $isTerrainPrincipal */
    protected bool $isTerrainPrincipal = true;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->adresse = new Adresse();
        $this->niveauxEtudesContraints = new ArrayCollection();
        $this->terrainsPrincipaux = new ArrayCollection();
        $this->terrainsSecondaires = new ArrayCollection();
        $this->initContactsTerrainsCollection();;
    }

    /** Fonction de trie d'un tableau de terrains de stages */
    static function sort(array|Collection $entities, string $order = 'asc'): array
    {
        $ordre = ($order != 'desc') ? 1 : -1;
        if($entities instanceof Collection){$entities = $entities->toArray();}
        usort($entities, function (TerrainStage $t1, TerrainStage $t2) use ($ordre) {
            $c1 = $t1->getCategorieStage();
            $c2 = $t2->getCategorieStage();
//            On trie par type (Principale/secondaire)
            if ($t1->isTerrainPrincipal() != $t2->isTerrainPrincipal()) {
                return ($t1->isTerrainPrincipal()) ? -$ordre : $ordre;
            }
            //On trie par ordre de la catégorie
            if ($c1->getId() != $c2->getId()) {
                if ($c1->getOrdre() < $c2->getOrdre()) return -$ordre;
                if ($c2->getOrdre() < $c1->getOrdre()) return $ordre;
                return $ordre*strcmp($c1->getLibelle(), $c2->getLibelle());
            }
            //Sinon par libelle du terrain
            return $ordre*strcmp($t1->getLibelle(), $t2->getLibelle());
        });
        return $entities;
    }

    /**
     * @return bool
     */
    public function isTerrainPrincipal(): bool
    {
        return $this->isTerrainPrincipal;
    }

    /**
     * @param bool $isTerrainPrincipal
     * @return \Application\Entity\Db\TerrainStage
     */
    public function setIsTerrainPrincipal(bool $isTerrainPrincipal): static
    {
        $this->isTerrainPrincipal = $isTerrainPrincipal;
        return $this;
    }


    /**
     * Get $service.
     *
     * @return string|null
     */
    public function getService(): ?string
    {
        return $this->service;
    }

    /**
     * Set libelle.
     *
     * @param string $service
     *
     * @return TerrainStage
     */
    public function setService(string $service): static
    {
        $this->service = $service;
        return $this;
    }


//    Liens vers les terrains de stages associées
//TODO : a supprimer
    /**
     * Get minPlace.
     *
     * @return int
     */
    public function getMinPlace(): int
    {
        return $this->minPlace;
    }

    /**
     * Set minPlace.
     *
     * @param int $minPlace
     *
     * @return TerrainStage
     */
    public function setMinPlace(int $minPlace): static
    {
        $this->minPlace = $minPlace;
        return $this;
    }

    /**
     * Get idealPlace.
     *
     * @return int
     */
    public function getIdealPlace(): int
    {
        return $this->idealPlace;
    }

    /**
     * Set idealPlace.
     *
     * @param int $idealPlace
     *
     * @return TerrainStage
     */
    public function setIdealPlace(int $idealPlace): static
    {
        $this->idealPlace = $idealPlace;
        return $this;
    }

    /**
     * Get maxPlace.
     *
     * @return int
     */
    public function getMaxPlace(): int
    {
        return $this->maxPlace;
    }

    /**
     * Set maxPlace.
     *
     * @param int $maxPlace
     *
     * @return TerrainStage
     */
    public function setMaxPlace(int $maxPlace): static
    {
        $this->maxPlace = $maxPlace;
        return $this;
    }

    public function getTerrainsPrincipaux(): Collection
    {
        return $this->terrainsPrincipaux;
    }

    public function setTerrainsPrincipaux(Collection $terrainsPrincipaux): static
    {
        $this->terrainsPrincipaux = $terrainsPrincipaux;
        return $this;
    }

    public function addTerrainPrincipal(TerrainStage $terrain): static
    {
        if (!$this->terrainsPrincipaux->contains($terrain)) $this->terrainsPrincipaux[] = $terrain;
        return $this;
    }

    public function removeTerrainPrincipal(TerrainStage $terrain): static
    {
         $this->terrainsPrincipaux->removeElement($terrain);
        return $this;
    }

    public function getTerrainsSecondaires(): Collection
    {
        return $this->terrainsSecondaires;
    }

    public function setTerrainsSecondaires(Collection $terrainsSecondaires): static
    {
        $this->terrainsSecondaires = $terrainsSecondaires;
        return $this;
    }

    public function addTerrainSecondaire(TerrainStage $terrain): static
    {
        if (!$this->terrainsPrincipaux->contains($terrain)) $this->terrainsSecondaires[] = $terrain;
        return $this;
    }

    public function removeTerrainSecondaire(TerrainStage $terrain): static
    {
        $this->terrainsSecondaires->removeElement($terrain);
        return $this;
    }

    /**
     * Add niveauxEtudesContraint.
     *
     * @param NiveauEtude $niveauEtude
     *
     * @return TerrainStage
     */
    public function addNiveauEtudeContraint(NiveauEtude $niveauEtude): static
    {
        if (!$this->niveauxEtudesContraints->contains($niveauEtude)) {
            $this->niveauxEtudesContraints[] = $niveauEtude;
        }
        return $this;
    }

    /**
     * Remove niveauxEtudesContraint.
     *
     * @param NiveauEtude $niveauEtude
     * @return \Application\Entity\Db\TerrainStage
     */
    public function removeNiveauEtudeContraint(NiveauEtude $niveauEtude): static
    {
        $this->niveauxEtudesContraints->removeElement($niveauEtude);
        return $this;
    }

    /**
     * Get niveauxEtudesContraints.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function getNiveauxEtudesContraints(): ArrayCollection|Collection
    {
        return $this->niveauxEtudesContraints;
    }

    /**
     * @param NiveauEtude $niveauEtude
     * @return boolean
     */
    public function isContraintForNiveauEtude(NiveauEtude $niveauEtude): bool
    {
        return $this->niveauxEtudesContraints->contains($niveauEtude);
    }

    /**
     * Get actif.
     *
     * @return bool|null
     */
    public function getActif(): ?bool
    {
        return $this->actif;
    }

    /**
     * Set actif.
     *
     * @param bool|null $actif
     *
     * @return TerrainStage
     */
    public function setActif(bool $actif = null): static
    {
        $this->actif = $actif;
        return $this;
    }

    /**
     * Get actif.
     *
     * @return bool|null
     */
    public function isActif(): ?bool
    {
        return $this->actif;
    }

    /**
     * Get modelConventionStage.
     *
     * @return \Application\Entity\Db\ModeleConventionStage|null
     */
    public function getModeleConventionStage(): ?ModeleConventionStage
    {
        return $this->modeleConventionStage;
    }

    /**
     * Add niveauxEtudesContraint.
     *
     * @param NiveauEtude $niveauxEtudesContraint
     *
     * @return TerrainStage
     */
    public function addNiveauxEtudesContraint(NiveauEtude $niveauxEtudesContraint): static
    {
        $this->niveauxEtudesContraints[] = $niveauxEtudesContraint;
        return $this;
    }

    /**
     * Remove niveauxEtudesContraint.
     *
     * @param NiveauEtude $niveauxEtudesContraint
     * @return \Application\Entity\Db\TerrainStage
     */
    public function removeNiveauxEtudesContraint(NiveauEtude $niveauxEtudesContraint): static
    {
        $this->niveauxEtudesContraints->removeElement($niveauxEtudesContraint);
        return $this;
    }

    /**
     * Get preferencesAutorisees.
     *
     * @return bool|null
     */
    public function getPreferencesAutorisees(): ?bool
    {
        return $this->preferencesAutorisees;
    }

    /**
     * Set preferencesAutorisees.
     *
     * @param bool|null $preferencesAutorisees
     *
     * @return TerrainStage
     */
    public function setPreferencesAutorisees(bool $preferencesAutorisees = null): static
    {
        $this->preferencesAutorisees = $preferencesAutorisees;
        return $this;
    }

    /**
     * Get infos.
     *
     * @return string|null
     */
    public function getInfos(): ?string
    {
        return $this->infos;
    }

    /**
     * Set infos.
     *
     * @param string|null $infos
     *
     * @return TerrainStage
     */
    public function setInfos(string $infos = null): static
    {
        $this->infos = $infos;
        return $this;
    }

    /**
     * Get lien.
     *
     * @return string|null
     */
    public function getLien(): ?string
    {
        return $this->lien;
    }

    /**
     * Set lien.
     *
     * @param string|null $lien
     *
     * @return TerrainStage
     */
    public function setLien(string $lien = null): static
    {
        $this->lien = $lien;
        return $this;
    }


    public function isHorsSubdivision(): bool
    {
        return $this->horsSubdivision;
    }

    /**
     * Set horsSubdivision.
     *
     * @param bool|null $horsSubdivision
     *
     * @return TerrainStage
     */
    public function setHorsSubdivision(bool $horsSubdivision = null): static
    {
        $this->horsSubdivision = $horsSubdivision;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTerrainSecondaire(): bool
    {
        return !$this->isTerrainPrincipal;
    }

    /**
     * @param bool $isTerrainPrincipal
     * @return \Application\Entity\Db\TerrainStage
     */
    public function setIsTerrainSecondaire(bool $isTerrainPrincipal): static
    {
        $this->setIsTerrainSecondaire($isTerrainPrincipal);
        return $this;
    }

}
