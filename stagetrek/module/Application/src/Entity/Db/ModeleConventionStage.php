<?php

namespace Application\Entity\Db;

use Application\Entity\Interfaces\HasCodeInterface;
use Application\Entity\Interfaces\HasLibelleInterface;
use Application\Entity\Traits\InterfaceImplementation\HasCodeTrait;
use Application\Entity\Traits\InterfaceImplementation\HasDescriptionTrait;
use Application\Entity\Traits\InterfaceImplementation\HasIdTrait;
use Application\Entity\Traits\InterfaceImplementation\HasLibelleTrait;
use Application\Entity\Traits\Terrain\HasTerrainsStagesTrait;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use UnicaenRenderer\Entity\Db\Template;
use UnicaenUtilisateur\Entity\Db\HistoriqueAwareInterface;
use UnicaenUtilisateur\Entity\Db\HistoriqueAwareTrait;

/**
 * ModeleConventionStage
 */
class ModeleConventionStage implements ResourceInterface,
    HasCodeInterface, HasLibelleInterface,
    HistoriqueAwareInterface
{
    const RENDER_CONVENTION_NAMESPACE = 'Convention';
    const RENDER_CONVENTION_CORPS_TYPE = 'pdf';
    const RESOURCE_ID = 'ModeleConventionStage';

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return self::RESOURCE_ID;
    }

    public function generateDefaultCode(array $param = []) : string
    {
        $uid = ($param['uid']) ?? uniqid();
        $prefixe = ($param['prefixe']) ?? 'convention';;
        $separateur = ($param['sep']) ?? '-';
        return substr(sprintf("%s%s%s", $prefixe, $separateur, $uid), 0, 100);
    }

    use HasIdTrait;
    use HasCodeTrait;
    use HasLibelleTrait;
    use HasDescriptionTrait;
    use HasTerrainsStagesTrait;
    use HistoriqueAwareTrait;

    /**
     * @param TerrainStage $terrainStage
     * @return \Application\Entity\Db\ModeleConventionStage
     */
    public function addTerrainStage(TerrainStage $terrainStage) : static
    {
        if(!$this->terrainsStages->contains($terrainStage)){
            $this->terrainsStages->add($terrainStage);
            //requis pour la persistance des données
            $terrainStage->setModeleConventionStage($this);
        }
        return $this;
    }
    /**
     * Remove groupe.
     *
     * @param TerrainStage $terrainStage
     */
    public function removeTerrainStage(TerrainStage $terrainStage) : static
    {
        if($this->terrainsStages->contains($terrainStage)){
            $this->terrainsStages->removeElement($terrainStage);
            //requis pour la persistance des données
            $terrainStage->setModeleConventionStage($this);
        }
        return $this;
    }


    public function setLibelle(?string $libelle): static
    {
        $this->libelle = $libelle;
        if($this->hasCorpsTemplate()){
            $this->corpsTemplate->setSujet($libelle);
        }
        return $this;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;
        if($this->hasCorpsTemplate()){
            $this->corpsTemplate->setCode($code);
        }
        return $this;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initTerrainsStagesCollection();
    }
    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return ModeleConventionStage
     */
    public function setDescription(?string $description) : static
    {
        $this->description = $description;
        if($this->hasCorpsTemplate()){
            $this->corpsTemplate->setDescription($description);
        }
        return $this;
    }


    protected ?Template $corpsTemplate = null;

    public function getCorpsTemplate(): ?Template
    {
        return $this->corpsTemplate;
    }

    public function setCorpsTemplate(?Template $corpsTemplate): void
    {
        $this->corpsTemplate = $corpsTemplate;
    }

    public function hasCorpsTemplate() : bool
    {
        return $this->corpsTemplate !== null;
    }

    /**
     * Set corps.
     *
     * @param string|null $corps
     *
     * @return ModeleConventionStage
     */
    public function setCorps(?string $corps) : static
    {
        if(!$this->hasCorpsTemplate()){return $this;}
        if(!isset($corps)){$corps = "";} //sécurité car UnicaenRenderer n'autorise pas null
        $this->corpsTemplate->setCorps($corps);
        return $this;
    }
    /**
     * Get corps.
     *
     * @return string|null
     */
    public function getCorps(): ?string
    {
        if(!$this->hasCorpsTemplate()){return null;}
        return $this->corpsTemplate->getCorps();
    }
    /**
     * Set css.
     *
     * @param string|null $css
     *
     * @return ModeleConventionStage
     */
    public function setCss(?string $css) :static
    {
        if(!$this->hasCorpsTemplate()){return $this;}
        $this->corpsTemplate->setCss($css);
        return $this;
    }
    /**
     * Get css.
     *
     * @return ?string
     */
    public function getCss(): ?string
    {
        if(!$this->hasCorpsTemplate()){return null;}
        return $this->corpsTemplate->getCss();
    }

}
