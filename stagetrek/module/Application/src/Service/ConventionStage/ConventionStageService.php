<?php

namespace Application\Service\ConventionStage;

use Application\Entity\Db\ConventionStage;
use Application\Entity\Db\ModeleConventionStage;
use Application\Entity\Db\Stage;
use Application\Provider\Fichier\NatureFichierProvider;
use Application\Provider\Renderer\TemplateProvider;
use Application\Service\Misc\CommonEntityService;
use Application\Service\Renderer\MacroService;
use DateTime;
use Exception;
use Fichier\Entity\Db\Fichier;
use Fichier\Entity\Db\Nature;
use Fichier\Service\Fichier\FichierService;
use UnicaenPdf\Exporter\PdfExporter;
use UnicaenRenderer\Entity\Db\Rendu;
use UnicaenRenderer\Entity\Db\Template;
use UnicaenRenderer\Service\Macro\MacroServiceAwareTrait;

/**
 * Class ConventionStageService
 * @package Application\Service\ConventionStage
 */
class ConventionStageService extends CommonEntityService
{
    /** @return string */
    public function getEntityClass(): string
    {
        return ConventionStage::class;
    }

    /**
     * @param int $id
     * @return \Application\Entity\Db\ConventionStage|null
     */
    public function findByStageId(int $id) : ?ConventionStage
    {
        /** @var Stage $stage */
        $stage = $this->getObjectManager()->getRepository(Stage::class)->find($id);
        return ($stage !== null) ? $stage->getConventionStage() : null;
    }

    /**
     * Cherche toutes les instances de l'entité
     * !!! lourd car on passe par les stages pour bien avoir uniquement les conventions et pas d'autre UnicaenRendererRendu
     * @return array
     */
    public function findAll() : array
    {
        /** @var Stage[] $stages */
        $stages = $this->getObjectManager()->getRepository(Stage::class)->findAll();
        $result = [];
        foreach ($stages as $s) {
            $convention = $s->getConventionStage();
            if (isset($convention)) $result[] = $convention;
        }
        return $this->getList($result);
    }

    /**
     * @param mixed $entity
     * @param string|null $serviceEntityClass
     * @return \Application\Entity\Db\ConventionStage|null
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function add(mixed $entity, string $serviceEntityClass = null): ?ConventionStage
    {
        /** @var ConventionStage $convention */
        $convention = $entity;
        //Si le stage correspondait déjà à une convention de stage, on supprime l'ancienne au cas ou
        if ($convention->getStage() == null) {
            throw new Exception("La convention de stage de stage doit être associée à un stage");
        }
        $date = new DateTime();
        $convention->setDateUpdate($date);
        $this->getObjectManager()->persist($convention);

        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush($convention);
        }
        return $convention;
    }

    /**
     * Ajoute/Met à jour une entité
     *
     * @param mixed $entity
     * @param string|null $serviceEntityClass classe de l'entité
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(mixed $entity, string $serviceEntityClass = null): ConventionStage
    {
        /** @var ConventionStage $convention */
        $convention = $entity;
        $date = new DateTime();
        if($convention->getId() == null){
            $this->getObjectManager()->persist($convention);
        }
        $entity->setDateUpdate($date);

        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush($convention);
        }
        return $entity;
    }
    /**
     * @param mixed $entity
     * @param string|null $serviceEntityClass
     * @return $this
     */
    public function delete(mixed $entity, string $serviceEntityClass = null) : static
    { // Note : choix fait de ne pas supprimer tout de suite les conventions de stage historisé mais de les convserver quelques jours
//        A voir ce que veux dire ce quelques jours. TODO : faire lors de l'update un nettoyages des fichiers marqués comme archivées depuis trop longtemps
        /** @var ConventionStage $convention */
        $convention = $entity;
        $fichier = $convention->getFichier();
        if(isset($fichier)){ // Suppresssion du fichier
            $this->getFileService()->historise($fichier);
        }
        $this->getObjectManager()->remove($entity);
        if ($this->hasUnitOfWorksChange()) {
            $this->getObjectManager()->flush();
        }
        return $this;
    }

//    TODO : utiliser le fileService
    /**
     * @throws \Exception
     */
    public function exportToPdf(ConventionStage $convention) : void
    {
        if(!$convention->hasFile()){
            throw new Exception("Le fichier de la convention de stage n'est pas définie");
        }
        try{
            $fichier = $convention->getFichier();
            $content = $this->getFileService()->getStorageFileContent($fichier);
            header("Content-type: application/pdf");
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=".$fichier->getNomOriginal());
            header("Pragma: no-cache");
            header("Expires: 0");
            echo $content;
        }
        catch (Exception $e){
            throw new Exception("Le fichier de la convention de stage est actuellement indisponible");
        }
    }

    //Gestion des fichier
    protected ?FichierService $fileService = null;
    public function getFileService(): ?FichierService
    {
        return $this->fileService;
    }
    public function setFileService(?FichierService $fileService): void
    {
        $this->fileService = $fileService;
    }

    /**
     * TODO : faire la fonction qui permet de récupérer les conventions
     * Créer si besoin l'objet convention et surtout créer / Mais a jour le fichier dans le storage
     * @throws \Exception
     */
    public function createFromFile(ConventionStage $convention, array $fileData) : ConventionStage
    {
        //1) si la convention a déjà un fichier, on l'archive (permet de le récupérer en cas de problème
        $oldFile = $convention->getFichier();
        if(isset($oldFile)){
            $this->getFileService()->historise($oldFile);
        }
        try{
            $nature = $this->getObjectManager()->getRepository(Nature::class)->findOneBy(['code' => NatureFichierProvider::CONVENTION]);
            /** @var ConventionStageFileNameFormatter $fileNameFormatter */
            $fileNameFormatter = $this->getFileService()->getFileNameFormatter();
            $fileNameFormatter->setConventionStage($convention);
            $fileData['name'] = $fileNameFormatter->getOriginalFilename();
            $fichier = $this->getFileService()->createFichierFromUpload($fileData, $nature);

            $convention->setFichier($fichier);
            if($convention->getId() == null){
                $this->add($convention);
            }
            else{
                $this->update($convention);
            }
            $this->getObjectManager()->flush($fichier);
        }
        catch (Exception $e){
            try{//On essaye de restaurer l'ancien fichier si posible
                if(isset($oldFile)) {
                    $this->getFileService()->restore($oldFile);
                    $convention->setFichier($oldFile);
                    $this->getObjectManager()->flush($oldFile);
                    $this->getObjectManager()->flush($convention);
                }
            }catch (Exception $e2){}
            throw $e;
        }
        return $convention;
    }

    public function createFromTemplate(ConventionStage $convention) : ConventionStage
    {
        //1) si la convention a déjà un fichier, on l'archive (permet de le récupérer en cas de problème
        $oldFile = $convention->getFichier();
        if(isset($oldFile)){
            $this->getFileService()->historise($oldFile);
        }
        try{
            $nature = $this->getObjectManager()->getRepository(Nature::class)->findOneBy(['code' => NatureFichierProvider::CONVENTION]);
            /** @var ConventionStageFileNameFormatter $fileNameFormatter */
            $fileNameFormatter = $this->getFileService()->getFileNameFormatter();
            $fileNameFormatter->setConventionStage($convention);
            $originalName = $fileNameFormatter->getOriginalFilename();
            $tmpName = $this->generateConvention($convention);
            $fichier = $this->getFileService()->createFichierFromFile($tmpName, $nature);
            $fichier->setNomOriginal($originalName); //Pour ne pas conserver un nom tmp fictif
            $convention->setFichier($fichier);
            if($convention->getId() == null){
                $this->add($convention);
            }
            else{
                $this->update($convention);
            }
            $this->getObjectManager()->flush($fichier);
        }
        catch (Exception $e){
            try{//On essaye de restaurer l'ancien fichier si posible
                if(isset($oldFile)) {
                    $this->getFileService()->restore($oldFile);
                    $convention->setFichier($oldFile);
                    $this->getObjectManager()->flush($oldFile);
                    $this->getObjectManager()->flush($convention);
                }
            }catch (Exception $e2){}
            throw $e;
        }
        return $convention;
    }


    /**
     * Génération des conventions a partir d'un template et d'un rendu
     */

    /**
     * @param \Application\Entity\Db\ConventionStage $convention
     * @param ModeleConventionStage|null $modele
     * @return ConventionStage
     * @desc Contstruit une nouvelle convention de stage à partir des informations issue du modéle pour le stage et des informations disponible
     */
    public function pregenererConventionRendu(ConventionStage $convention, ?ModeleConventionStage $modele): ConventionStage
    {
        $corps = "";
        $rendu = $convention->getRendu();
        if(isset($modele)){
            /** @var MacroService $macrosService */
            $macrosService = $this->getMacroService();
            $variables = $this->getMacroVariablesForConvention($convention);
            $convention->setModeleConventionStage($modele);
            $corps = $macrosService->replaceMacros($modele->getCorps(), $variables);
        }
        if($rendu ===null){
            $rendu = new Rendu();
            $convention->setRendu($rendu);
            $rendu->setDate(new DateTime());
            $rendu->setSujet(sprintf("Convention du stage %s de %s",
                $convention->getStage()->getNumero(),
                $convention->getStage()->getEtudiant()->getDisplayName(),
            ));
        }
        $rendu->setCorps($corps);
        $convention->setCorps($corps);
        return $convention;
    }


    /** @var PdfExporter */
    protected PdfExporter $pdfExporter;
//    TODO : modifier pour les intégrer en config / ou encore mieux en template depuis renderer ?
    protected string $scriptDirectory = "/application/convention/convention-stage/scripts";

    public function setPdfExporter(PdfExporter $pdfExporter): void
    {
        $this->pdfExporter = $pdfExporter;
    }

    /**
     * Gestions des macros
     */
    use MacroServiceAwareTrait;
    /**
     * Retournes la listes des variables nécessaires pour la générations des conventions de stages
     */
    public function getMacroVariablesForConvention(ConventionStage $conventionStage): array
    {
        /** @var MacroService $macroService */
        $macroService = $this->getMacroService();

        $variables = [];
        $stage = $conventionStage->getStage();
        $etudiant = $stage->getEtudiant();
        $affectation = $stage->getAffectationStage();
        $variables['stage'] = $stage;
        $variables['affectationStage'] = $affectation;
        $variables['terrainStage'] = ($affectation) ? $affectation->getTerrainStage() : null;
        $variables['terrainStageSecondaire'] = ($affectation) ? $affectation->getTerrainStageSecondaire() : null;
        $variables['etudiant'] = $stage->getEtudiant();
        $variables['anneeUniversitaire'] = $stage->getAnneeUniversitaire();
        $macroService->getDateRendererService()->setVariables(['stage' => $stage, 'etudiant' => $etudiant]);
        $macroService->getAdresseRendererService()->setVariables(['etudiant' => $etudiant]);
        $macroService->getContactRendererService()->setVariables(['stage' => $stage]);
        $variables['parametreRendererService'] = $macroService->getParametreRendererService();
        $variables['dateRendererService'] = $macroService->getDateRendererService();
        $variables['adresseRendererService'] = $macroService->getAdresseRendererService();
        $variables['contactRendererService'] = $macroService->getContactRendererService();
        $variables['pdfRendererService'] = $macroService->getPdfRendererService();
        return $variables;
    }

    public function getConventionRenduContainsMacro(ConventionStage $convention): bool
    {
        /** @var MacroService $macrosService */
        $macrosService = $this->getMacroService();
        return $macrosService->textContainsMacro($convention->getCorps());
    }

    /**
     * @param ConventionStage $convention
     * @return ConventionStage.
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws \Mpdf\MpdfException
     * @throws \Exception
     */
    protected function generateConvention(ConventionStage $convention): string
    {
//        if(!isset($this->conventionRepository) || !is_dir($this->conventionRepository)){
//            throw new Exception( "Le repertoire de dépôt des conventions de stage n'est pas correctement configuré.");
//        }

//        On doit faire le update ici car l'accés au conventions pose des problème de non persistance en bdd
        /** @var ConventionStageFileNameFormatter $fileNameFormatter */
        $fileNameFormatter = $this->getFileService()->getFileNameFormatter();
        $fileNameFormatter->setConventionStage($convention);
        $fileName = $fileNameFormatter->getOriginalFilename();
        $modele = ($convention->hasModeleConventionStage()) ? $convention->getModeleConventionStage() : null;
        if(!isset($modele)){
            throw new Exception("Le modéle de référence n'est pas défini");
        }

        $corps = $convention->getCorps();
        $corps = $this->formatText($convention, $corps);

        /** @var Template $headerTemplate */
        $headerTemplate = $this->getObjectManager()->getRepository(Template::class)->findOneBy(['code' => TemplateProvider::HEADER_CONVENTION]);
        $header = ($headerTemplate) ? $headerTemplate->getCorps() : null;
        $header = $this->formatText($convention, $header);

        /** @var Template $footerTemplate */
        $footerTemplate = $this->getObjectManager()->getRepository(Template::class)->findOneBy(['code' => TemplateProvider::FOOTER_CONVENTION]);
        $footer = ($footerTemplate) ? $footerTemplate->getCorps() : null;
        $footer = $this->formatText($convention, $footer);

        $css = $modele->getCss().PHP_EOL;
        $css .= (isset($headerTemplate)) ? $headerTemplate->getCss().PHP_EOL : "";
        $css .= (isset($footerTemplate)) ? $footerTemplate->getCss().PHP_EOL : "";
        if($css==""){$css = null;}

        //Script de génération
        $cssScript = sprintf("%s/%s", $this->scriptDirectory, 'css.phtml');
        $headerScript = sprintf("%s/%s", $this->scriptDirectory, 'header.phtml');
        $titleScript =  sprintf("%s/%s", $this->scriptDirectory, 'title.phtml');
        $corpsScript =  sprintf("%s/%s", $this->scriptDirectory, 'corps.phtml');
        $signaturesScript = sprintf("%s/%s", $this->scriptDirectory, 'signatures.phtml');
        $footerScript = sprintf("%s/%s", $this->scriptDirectory, 'footer.phtml');

        $this->pdfExporter->setHeaderScript($headerScript, null,  ['content' => $header]);
        //Choix fait : de mettre le header en tant que titleScript pour ne le faire que sur la premiére page
        $this->pdfExporter->addBodyScript($cssScript, false, ['css' => $css], false);
        $this->pdfExporter->addBodyScript($titleScript, false,  [], false);
        $this->pdfExporter->addBodyScript($corpsScript, false, ['content' => $corps], false);
        $this->pdfExporter->addBodyScript($signaturesScript, false, ['convention' => $convention], false);
        $this->pdfExporter->setFooterScript($footerScript, null, ['content' => $footer]);
        $this->pdfExporter->setExportDirectoryPath("/tmp");
        $this->pdfExporter->export($fileName, PdfExporter::DESTINATION_FILE);//
        if(!file_exists("/tmp/".$fileName)){
            throw new Exception("Une erreur c'est produite, le fichier de la convention n'as pas pu être remplacé.");
        }
        return "/tmp/".$fileName;
    }

    /**
     * @throws \Exception
     */
    protected function formatText(ConventionStage $convention, ?string $content = null) : ?string
    {
        if(!isset($content)){return null;}
        /** @var MacroService $macrosService */
        $macrosService = $this->getMacroService();
        $variables = $this->getMacroVariablesForConvention($convention);
        //Remplacement des variables si possibles
        $content = $macrosService->replaceMacros($content, $variables);

        if($macrosService->textContainsMacro($content)){
            throw new Exception("Toutes les macros n'ont pas été remplacées.");
        }
//       Permet au besoins de s'assurer que des div n'ont pas été encodé en bdd
        return html_entity_decode($content);
    }
}