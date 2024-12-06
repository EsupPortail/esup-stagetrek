<?php


namespace Application\View\Helper\Stages;

use Application\Entity\Db\AnneeUniversitaire;
use Application\Entity\Db\Groupe;
use Application\Entity\Db\SessionStage;
use Application\Entity\Timeline\TimeFrame;
use Application\Entity\Timeline\Timeline;
use Application\View\Helper\Groupe\GroupeViewHelper;
use Application\View\Helper\SessionsStages\SessionStageViewHelper;
use Application\View\Helper\Timeline\TimelineHelper;
use Laminas\View\Helper\AbstractHelper;

/**
 * Class CalendrierStageViewHelper
 * @package Application\View\Helper\Stages
 *
 * @method SessionStageViewHelper sessionStage()
 */
class CalendrierStageViewHelper extends AbstractHelper
{
    /** @var AnneeUniversitaire $annee */
    protected $annee;

    /**
     * @return AnneeUniversitaire
     */
    public function getAnnee()
    {
        return $this->annee;
    }

    /**
     * @param AnneeUniversitaire $annee
     */
    public function setAnnee(AnneeUniversitaire $annee)
    {
        $this->annee = $annee;
    }

    /**
     * @param AnneeUniversitaire $annee
     * @return self
     */
    public function __invoke($annee = null)
    {
        $this->annee = $annee;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * @return string
     */
    public function render()
    {
        if (!$this->annee) return "";
        return $this->annee->getLibelle();
    }

    /**
     * @param AnneeUniversitaire $annee
     * @param Groupe[] $groupes //Précisez les groupes que l'on souhaites afficher
     * @return string
     */
    public function renderCalendrierTimeline($annee, $groupes=[])
    {
        if (!$annee) {
            return "";
        }
        if($groupes == []){
            $groupes = $annee->getGroupes()->toArray();
            $groupes = Groupe::sortGroupes($groupes);
        }

        /** @var SessionStage[] $sessions */
        $sessions = [];
        foreach ($groupes as $groupe) {
            $sessions[$groupe->getId()] = [];
            foreach ($groupe->getSessionsStages() as $session) {
                $sessions[$groupe->getId()][$session->getId()] = $session;
            }
        }

        //Création des calendrier
        $calendrier = [];
        //Création d'une timeline par groupes
        foreach ($groupes as $groupe) {
            /** @var GroupeViewHelper $groupeVh */
            $groupeVh = $this->view->groupe($groupe);
            $groupeLibelle = $groupeVh->lienAfficher();
            $timeline = new Timeline($groupe->getId(), $groupeLibelle, $annee->getDateDebut(), $annee->getDateFin());
            foreach ($sessions[$groupe->getId()] as $session) {
                $timeFrame = new TimeFrame($session->getId(), $session->getLibelle());
                $timeFrame->setStart($session->getDateDebutStage());
                $timeFrame->setEnd($session->getDateFinStage());
                $timeline->addTimeFrame($timeFrame);
            }
            $calendrier[$groupe->getId()] = $timeline;
        }

        //Timeline par défaut : une année vide sans rien dedans ...
        if ($calendrier == []) {
            $calendrier[] = new Timeline(0, "", $annee->getDateDebut(), $annee->getDateFin());
        }

        $timeline = new TimelineHelper();
        return $timeline->multipleTimeline($calendrier);

    }
}