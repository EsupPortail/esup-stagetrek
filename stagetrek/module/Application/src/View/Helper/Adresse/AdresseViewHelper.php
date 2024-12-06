<?php


namespace Application\View\Helper\Adresse;

use Application\Entity\Db\Adresse;
use Application\Entity\Traits\Adresse\HasAdresseTrait;
use Laminas\View\Helper\AbstractHelper;

/**
 * Class AnneeUniversitaireViewHelper
 * @package Application\View\Helper\Annees
 */
class AdresseViewHelper extends AbstractHelper
{
    use HasAdresseTrait;

    /**
     * @param Adresse|null $adresse
     * @return self
     */
    public function __invoke(?Adresse $adresse = null) : static
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function __toString() : string
    {
        return $this->renderer();
    }

    public function renderer() : string
    {
        if(!$this->hasAdresse()){ return "Adresse non renseignée";}

        $addr = $this->adresse->getAdresse();
        $comp = $this->adresse->getComplement();
        $cp = $this->adresse->getCodePostal();
        $ville = $this->adresse->getVille();
        $cedex = $this->adresse->getCedex();
        $part1 = trim(sprintf("%s %s %s",
            trim(($addr) ?? ""),
            trim((($addr) && ($comp)) ? ", " : ""),
            trim(($comp) ?? ""),
        ));

        $part2 = trim(sprintf("%s %s %s",
            trim(($cp) ?? ""),
            trim(($ville) ?? ""),
            trim(($cedex) ?? "")
        ));
        $res = trim(sprintf("%s %s %s",
            (($part1!="") ? $part1 : ""),
            (($part1!="" && $part2!="") ? "<br/>" : ""),
            (($part2!="") ? $part2 : ""),
        ));
        return ($res !="") ? $res : "Information non renseignée" ;
    }

}