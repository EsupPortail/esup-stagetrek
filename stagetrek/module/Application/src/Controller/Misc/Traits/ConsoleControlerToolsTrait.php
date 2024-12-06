<?php


namespace Application\Controller\Misc\Traits;


use DateTime;
use Exception;
use Laminas\Router\RouteInterface;
use Laminas\Uri\Http;
use Unicaen\Console\ColorInterface;
use Unicaen\Console\Request;
use UnicaenApp\Exception\LogicException;

/**
 * Trait permettant de gérer certaines propriétés/actions requises pour les actions de console
 */
trait ConsoleControlerToolsTrait
{
    /**************************************************************
    * Vérification que l'action est bien appelé depuis la console
    ***************************************************************/
    protected function isConsoleAction(): bool
    {
        $request = $this->getRequest();
        if (!$request instanceof Request) {
            throw new \RuntimeException('Cette action doit être appelée depuis une console !');
        }
        return true;
    }

    protected function actionAllowed(String $action): bool
    {
        if(!isset($this->consoleConfig)){return true;}
        if(!isset($this->consoleConfig['actions'])){return true;}
        if(!isset($this->consoleConfig['actions'][$action])){return true;}
        if(!$this->consoleConfig['actions'][$action]){
            throw new \RuntimeException('Cette action a été désactivé dans la configuration');
        }

        return true;
    }

    /**************************************************************
     * URI host (pour la génération d'URL
     ***************************************************************/

    /** @var array */
    protected $consoleConfig;
    public function setConsoleConfig(array $consoleConfig){
        $this->consoleConfig = $consoleConfig;
        $this->reportTo = ($consoleConfig['report-to']) ?? null;
        if(is_string($this->reportTo)){
            $this->reportTo = str_replace('', '', $this->reportTo );
            $this->reportTo= explode(",",  $this->reportTo);
        }
    }

    /**
     * Requis pour la génération des liens pour une action de console
     * @var RouteInterface
     */
    private $router;
    /**
     * @param RouteInterface $router
     */
    public function setRouter(RouteInterface $router)
    {
        $this->router = $router;
    }

    protected function useConsoleUri(){
        $this->isConsoleAction();
        if(!$this->consoleConfig){
            throw new LogicException("La clé d'accès aux paramètres de configuration de la console 'console-cli' est introuvable.");
        }
        if(!isset($this->consoleConfig['uri-host'])){
            throw new LogicException("La clé d'accès aux paramètres de configuration 'console-cli[uri-host]' est introuvable.");
        }
        if(!isset($this->consoleConfig['uri-scheme'])){
            throw new LogicException("La clé d'accès aux paramètres de configuration 'console-cli[uri-scheme]' est introuvable.");
        }
        if(!$this->router){
            throw new LogicException("L'interface de routage de la console n'as pas été définie");
        }

        $httpUri = (new Http())
            ->setHost($this->consoleConfig['uri-host'])
            ->setScheme($this->consoleConfig['uri-scheme']);
        $this->router->setRequestUri($httpUri);
    }

    /************************************************
     * Gestions des traces à écrire dans la console
     ************************************************/
    protected function echoDebutTache($tacheName)
    {
        $today = new DateTime();
        $msg = sprintf("Début de la tache %s le %s à %s.",
            $tacheName,
            $today->format("d/m/Y"),
            $today->format("H\hi,s\s")
        );
        $this->getConsole()->writeLine("\n* " . $msg . " *", ColorInterface::LIGHT_GREEN);
        $this->getConsole()->writeLine("-------------------\n", ColorInterface::GREEN);
    }

    protected function echoFinTache()
    {
        $today = new DateTime();
        $msg = sprintf("-- Fin de la tache le %s à %s.",
            $today->format("d/m/Y"),
            $today->format("H\hi,s\s")
        );
        $this->getConsole()->writeLine("\n* " . $msg . " *", ColorInterface::LIGHT_GREEN);
        $this->getConsole()->writeLine("-------------------\n", ColorInterface::GREEN);
    }

    protected function echoInfo($msg, $color = null)
    {
        $this->getConsole()->writeLine("-- " . $msg, $color);
    }

    protected function echoException(Exception $exception, $infoComplementaire = null)
    {
        $today = new DateTime();
        $msg = sprintf("Une erreur est survenue le %s à %s.",
            $today->format("d/m/Y"),
            $today->format("H\hi,s\s")
        );
        $msg .= sprintf("\n-- Exception levée : %s", $exception->getMessage());
        if ($infoComplementaire) {
            $msg .= sprintf("\n-- Informations complémentaires : %s",
                $infoComplementaire
            );
        }
        $this->echoInfo($msg . "\n", ColorInterface::RED);
    }


    /************************************************
     * Gestions des rapports à envoyer par mail
     ************************************************/
    /** @var string|array $reportTo */
    protected $reportTo = null;

    protected function getRepportTo(): mixed
    {
        return $this->reportTo;
    }

    /** @var array $reportData */
    protected $reportData;

    protected function getReportData(): array
    {
        return ($this->reportData) ?? [];
    }

    protected function addToReport(string $msg, $lvl = 2)
    {
        if(!$this->reportTo) return;

        if ($lvl == -1) {//Pas de mise ne page
            $msg = sprintf("%s", $msg);
        } else if ($lvl == 0) {//Séparateur + gras
            $msg = sprintf("<br /><hr/><br /><strong>%s</strong>", $msg);
        } else if ($lvl == 1) {//Gras uniquement
            $msg = sprintf("<strong>%s</strong>", $msg);
        } else {//Décalage en fonction du niveau
            $sep = "";
            for ($i = 1; $i < $lvl; $i++) {
                $sep .= "--";
            }
            $msg = sprintf("%s %s", $sep, $msg);
        }
        $this->reportData[] = $msg;
    }

    protected $LIMIT_REPPORT_LOG = 50;
    protected function sendRepport(string $tacheName)
    {
        $to = $this->getRepportTo();
        if (!$to || $to == "") {
            return;
        }
        $data = $this->getReportData();
        if (!$data || empty($data)) {
            return;
        }
        if ($tacheName === null || $tacheName == "") {
            return;
        }
        $sujet = "Rapport - " . $tacheName;
        $today = new DateTime();
        $corps = "<b>" . $sujet . "</b><br/>";
        $corps .= sprintf("Rapport généré le %s à %s<br />", $today->format('d/m/Y'), $today->format('H:i:s'));

        if (sizeof($data) <= $this->LIMIT_REPPORT_LOG) {
            foreach ($data as $row) {
                $corps .= $row . '<br />';
            }
        } else {
            $limit = 10;
            $cpt = 0;
            foreach ($data as $row) {
                if ($cpt++ > $limit) {
                    break;
                }
                $corps .= $row . '<br />';
            }
            $corps .= "...<br/><hr /><br/>";
            $corps .= sprintf("Le nombre d'entrée(s) du rapport ne permet pas de fournir toutes les informations. <br/>");
            $corps .= sprintf("<i>Consulter les logs pour accéder aux restes des informations</i>");
        }
        $mail = $this->getMailService()->sendMail($to, $sujet, $corps);
        //Choix fait de supprimer les mails de rapport de la bdd pour encombrer inutilement la base de données
        $this->getObjectManager()->remove($mail);
        $this->getObjectManager()->flush($mail);
    }
}