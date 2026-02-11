<?php

namespace BddAdmin\Data;

use Application\Misc\Util;
use Application\Provider\Mailing\CodesMailsProvider;
use BddAdmin\Data\Interfaces\DataProviderInterface;
use Laminas\Stdlib\ArrayUtils;
use Unicaen\BddAdmin\Data\DataManager;

class UnicaenRendererDataProvider implements DataProviderInterface {


    static public function getConfig(string $table, array $config = []): array
    {
        $defaultConfig=[];
        switch ($table) {
            case 'unicaen_renderer_macro' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'     => 'code',
                    'options' => ['update' => true, 'soft-delete' => false, 'delete' => false],
                ];
                break;
            case 'unicaen_renderer_template' :
                $defaultConfig = [
                    'actions' => [DataManager::ACTION_INSTALL, DataManager::ACTION_UPDATE],
                    'key'     => 'code',
                    'options' => ['update' => false, 'soft-delete' => false, 'delete' => false],
                ];
                break;
        }
        return ArrayUtils::merge($defaultConfig, $config);
    }

    public function unicaen_renderer_macro(): array
    {
        return [
            [
                "code" => "AnneeUniversitaire#libelle",
                "description" => "<p>Libelle de l'année universitaire",
                "variable_name" => "anneeUniversitaire",
                "methode_name" => "getLibelle",
            ],
            [
                "code" => "Application#url",
                "description" => "<p>Url de l'application</p>",
                "variable_name" => "urlService",
                "methode_name" => "getUrlApp",
            ],
            [
                "code" => "CHU#directeurGeneral",
                "description" => "<p>Nom Prénom du directeur général du CHU</p>",
                "variable_name" => "parametreRendererService",
                "methode_name" => "getDirecteurCHU",
            ],
            [
                "code" => "CHU#doyenUFRSante",
                "description" => "<p>Nom Prénom du doyen de l'UFR de santé</p>",
                "variable_name" => "parametreRendererService",
                "methode_name" => "getDoyenUFR",
            ],
            [
                "code" => "CHU#nomCHU",
                "description" => "<p>Nom du CHU</p>",
                "variable_name" => "parametreRendererService",
                "methode_name" => "getNomCHU",
            ],
            [
                "code" => "Contact#listeEtudiantsEncadres",
                "description" => "<p>Liste des liens des étudiants encadrés par un responsable sur une session</p>",
                "variable_name" => "contactRendererService",
                "methode_name" => "getlisteEtudiantsEncadres",
            ],
            [
                "code" => "Contact#listeUrlValidations",
                "description" => "<p>Liste des liens de validations d'un responsable de stage</p>",
                "variable_name" => "contactRendererService",
                "methode_name" => "getListeUrlValidations",
            ],
            [
                "code" => "Date#dateCoutrante",
                "description" => "<p>Date d'envoie du mail</p>",
                "variable_name" => "dateRendererService",
                "methode_name" => "getDateCourante",
            ],
            [
                "code" => "Date#heureCoutrante",
                "description" => "<p>Heure d'envoie du mail</p>",
                "variable_name" => "dateRendererService",
                "methode_name" => "getHeureCourante",
            ],
            [
                "code" => "Etudiant#adresse",
                "description" => "<p>Adresse personnel de l'étudiant".Util::POINT_MEDIANT."e</p>",
                "variable_name" => "adresseRendererService",
                "methode_name" => "getAdresseEtudiant",
            ],
            [
                "code" => "Etudiant#dateNaissance",
                "description" => "<p>Date de naissance de l'étudiant".Util::POINT_MEDIANT."e</p>",
                "variable_name" => "etudiant",
                "methode_name" => "getDateNaissance",
            ],
            [
                "code" => "Etudiant#mail",
                "description" => "<p>Adresse mail de l'étudiant".Util::POINT_MEDIANT."e</p>",
                "variable_name" => "etudiant",
                "methode_name" => "getEmail",
            ],
            [
                "code" => "Etudiant#nom",
                "description" => "<p>Nom de l'étudiant".Util::POINT_MEDIANT."e</p>",
                "variable_name" => "etudiant",
                "methode_name" => "getNom",
            ],
            [
                "code" => "Etudiant#nomPrenom",
                "description" => "<p>Nom Prénom de l'étudiant".Util::POINT_MEDIANT."e</p>",
                "variable_name" => "etudiant",
                "methode_name" => "getDisplayName",
            ],
            [
                "code" => "Etudiant#numero",
                "description" => "<p>Numéro l'étudiant".Util::POINT_MEDIANT."e</p>",
                "variable_name" => "etudiant",
                "methode_name" => "getNumEtu",
            ],
            [
                "code" => "Etudiant#prenom",
                "description" => "<p>Prénom de l'étudiant".Util::POINT_MEDIANT."e</p>",
                "variable_name" => "etudiant",
                "methode_name" => "getPrenom",
            ],
            [
                "code" => "PDF#nombreDePages",
                "description" => "<p>Nombre total de page du pdf généré</p>",
                "variable_name" => "pdfRendererService",
                "methode_name" => "getNbPages",
            ],
            [
                "code" => "PDF#numeroPage",
                "description" => "<p>Numéro de la page courante dans le pdf généré</p>",
                "variable_name" => "pdfRendererService",
                "methode_name" => "getNumeroPage",
            ],
            [
                "code" => "Preferences#nbChoix",
                "description" => "<p>Nombre de choix possible(s) pour définir les préférences</p>",
                "variable_name" => "parametreRendererService",
                "methode_name" => "getNbChoixPossible",
            ],
            [
                "code" => "Preferences#url",
                "description" => "<p>Url vers la page de définition des préférences de l'étudiant vers sont stage</p>",
                "variable_name" => "urlService",
                "methode_name" => "getUrlPreferences",
            ],
            [
                "code" => "Session#dateDebutStage",
                "description" => "<p>Date de début d'une session de stage</p>",
                "variable_name" => "dateRendererService",
                "methode_name" => "getDateDebutSession",
            ],
            [
                "code" => "Session#dateFinStage",
                "description" => "<p>Date de fin d'une session de stage</p>",
                "variable_name" => "dateRendererService",
                "methode_name" => "getDateFinSession",
            ],
            [
                "code" => "Session#dateFinValidation",
                "description" => "<p>Date de fin de la phase de validation de la session de stage</p>",
                "variable_name" => "dateRendererService",
                "methode_name" => "getDateFinValidationSession",
            ],
            [
                "code" => "Session#heureFinValidation",
                "description" => "<p>Heure de fin de la phase de validation de la session de stage</p>",
                "variable_name" => "dateRendererService",
                "methode_name" => "getHeureFinValidationSession",
            ],
            [
                "code" => "Stage#dateDebut",
                "description" => "<p>Date de début du stage</p>",
                "variable_name" => "dateRendererService",
                "methode_name" => "getDateDebutStage",
            ],
            [
                "code" => "Stage#dateDebutChoix",
                "description" => "<p>Date de début de la phase de choix du stage</p>",
                "variable_name" => "dateRendererService",
                "methode_name" => "getDateDebutChoixStage",
            ],
            [
                "code" => "Stage#dateFin",
                "description" => "<p>Date de fin du stage</p>",
                "variable_name" => "dateRendererService",
                "methode_name" => "getDateFinStage",
            ],
            [
                "code" => "Stage#dateFinChoix",
                "description" => "<p>Date de fin de la phase de choix du stage</p>",
                "variable_name" => "dateRendererService",
                "methode_name" => "getDateFinChoixStage",
            ],
            [
                "code" => "Stage#dateFinValidation",
                "description" => "<p>Date de fin de la phase de validation du stage</p>",
                "variable_name" => "dateRendererService",
                "methode_name" => "getDateFinValidationStage",
            ],
            [
                "code" => "Stage#heureDebutChoix",
                "description" => "<p>Heure de début de la phase de choix du stage</p>",
                "variable_name" => "dateRendererService",
                "methode_name" => "getHeureDebutChoixStage",
            ],
            [
                "code" => "Stage#heureFinChoix",
                "description" => "<p>Heure de fin de la phase de choix du stage</p>",
                "variable_name" => "dateRendererService",
                "methode_name" => "getHeureFinChoixStage",
            ],
            [
                "code" => "Stage#libelle",
                "description" => "<p>Libelle du stage</p>",
                "variable_name" => "stage",
                "methode_name" => "getLibelle",
            ],
            [
                "code" => "Stage#mailResponsable",
                "description" => "<p>Adresse mail du responsable de stage</p>",
                "variable_name" => "contactRendererService",
                "methode_name" => "getMailResponsables",
            ],
            [
                "code" => "Stage#responsable",
                "description" => "<p>Nom Prénom du responsable de stage</p>",
                "variable_name" => "contactRendererService",
                "methode_name" => "getResponsablesNames",
            ],
            [
                "code" => "Stage#urlValidation",
                "description" => "<p>Url de validation du stage</p>",
                "variable_name" => "urlService",
                "methode_name" => "getUrlValidationStage",
            ],
            [
                "code" => "Terrain#libelle",
                "description" => "<p>Libelle du entity de stage</p>",
                "variable_name" => "terrainStage",
                "methode_name" => "getLibelle",
            ],
            [
                "code" => "Terrain#service",
                "description" => "<p>Nom du service du stage</p>",
                "variable_name" => "terrainStage",
                "methode_name" => "getService",
            ],
            [
                "code" => "UFR#adresse",
                "description" => "<p>Adresse de l'UFR de santé</p>",
                "variable_name" => "parametreRendererService",
                "methode_name" => "getAdresseUFR",
            ],
            [
                "code" => "UFR#fax",
                "description" => "<p>Fax à l'UFR de santé</p>",
                "variable_name" => "parametreRendererService",
                "methode_name" => "getFaxUFR",
            ],
            [
                "code" => "UFR#mail",
                "description" => "<p>Adresse mail de contact à l'UFR de santé</p>",
                "variable_name" => "parametreRendererService",
                "methode_name" => "getMailUFR",
            ],
            [
                "code" => "UFR#nom",
                "description" => "<p>Nom de l'UFR de santé</p>",
                "variable_name" => "parametreRendererService",
                "methode_name" => "getNomUFR",
            ],
            [
                "code" => "UFR#tel",
                "description" => "<p>Téléphone de contact à l'UFR de santé</p>",
                "variable_name" => "parametreRendererService",
                "methode_name" => "getTelUFR",
            ],
        ];
    }

    public function unicaen_renderer_template() : array
    {
        return [
            [
                "code" => "Convention-header",
                "description" => "<p>Entête des conventions de stages</p>",
                'engine' => 'default',
                "namespace" => "Convention",
                "document_type" => "pdf",
                "document_sujet" => "Entête des conventions de stages",
                "document_css" => ".header-convention{
                font-size: 110%; 
                border-bottom: 1px solid black; 
                vertical-align: bottom;
                }",
                "document_corps" => "<table class='header-convention'>
                <tbody>
                <tr style='height: 15px;'>
                <td style='text-align: left; vertical-align: top; height: 15px;' width='50%'>VAR[CHU#nomCHU]</td>
                <td style='height: 15px;' width='20%'</td>
                <td style='height: 15px;'</td>
                <td style='text-align: right; vertical-align: top; height: 15px;' width='30%'>VAR[UFR#nom]</td>
                </tr>
                </tbody>
                </table>
            ",
            ],
            [
                "code" => "Convention-footer",
                "description" => "<p>Pied de pages des conventions de stages</p>",
                'engine' => 'default',
                "namespace" => "Convention",
                "document_type" => "pdf",
                "document_sujet" => "Pied de page conventions de stages",
                "document_css" => ".footer-convention{
                font-size: 70%; 
                border-top: 1px solid black; 
                vertical-align: bottom;
                }",
                "document_corps" => "<table class='footer-convention' style='width: 100%;'>
                <tbody>
                <tr>
                <td style='width: 55%;'>UFRS - VAR[UFR#adresse]</td>
                <td style='width: 20%;'>Tel : VAR[UFR#tel]</td>
                <td style='width: 20%;'>Mail : VAR[UFR#mail]</td>
                <td style='width: 5%;'>VAR[PDF#numeroPage] / VAR[PDF#nombreDePages]</td>
                </tr>
                </tbody>
                </table>
            ",
            ],
//        Modéle des mails automatique
            [
                "code" => CodesMailsProvider::STAGE_DEBUT_CHOIX_RAPPEL,
                "description" => "Mail de rappel envoyé avant la date de fin de la procédure aux étudiants qui n'ont pas définie de préférence pour une session de stage",
                'engine' => 'default',
                "namespace" => "Mail",
                "document_type" => "mail",
                "document_sujet" => "Rappel - VAR[Stage#libelle] - Procédure de choix",
                "document_css" => "",
                "document_corps" => "<p>Cher(e), VAR[Etudiant#prenom]<br /><br />La procédure de choix pour stage clinique VAR[Stage#libelle] arrive a son terme.<br /><br />Vous pouvez définir ou modifier vos préférences jusqu'au VAR[Stage#dateFinChoix] - VAR[Stage#heureFinChoix] en vous connectant sur la plateforme VAR[Application#url].<br /><br /><strong>En l'absence de préférences la commission stage et garde procédera à votre affectation en fonction des places disponibles restantes. Aucune réclamation ne sera acceptée.</strong><br /><br />Bien cordialement,<br />La commission Stages et Gardes du DFASM</p>",
            ],
            [
                "code" => CodesMailsProvider::STAGE_DEBUT_CHOIX,
                "description" => "Mail envoyé aux étudiants pour les prévenir du début de la phase de définition des préférences d'une session de stage",
                'engine' => 'default',
                "namespace" => "Mail",
                "document_type" => "mail",
                "document_sujet" => "VAR[Stage#libelle] - Début de la procédure de choix",
                "document_css" => "",
                "document_corps" => "<p>Cher(e), VAR[Etudiant#prenom]<br /><br />Ce courriel vous est adressé car vous êtes inscrit pour le stage clinique VAR[Stage#libelle] qui aura lieux du VAR[Stage#dateDebut] au VAR[Stage#dateFin].<br /><br />La procédure de choix est ouverte jusqu'au <strong>VAR[Stage#dateFinChoix] - VAR[Stage#heureFinChoix]</strong>.<br />Vous pouvez dès à présent définir vos préférences pour ce stage en vous connectant sur la plateforme VAR[Application#url].<br /><br /><br />Bien cordialement,<br />La commission Stages et Gardes du DFASM</p>",
            ],
            [
                "code" => CodesMailsProvider::VALIDATION_STAGE_EFFECTUEE,
                "description" => "Mail envoyé automatiquement la première fois que le responsable pédagogique a procédé à la validation (ou l'invalidation) du stage",
                'engine' => 'default',
                "namespace" => "Mail",
                "document_type" => "mail",
                "document_sujet" => "VAR[Stage#libelle] - Évaluation du stage effectuée",
                "document_css" => "",
                "document_corps" => "<p>Cher(e), VAR[Etudiant#prenom]<br /><br />Votre encadrant pédagogique s'est prononcé sur la validation de votre stage VAR[Stage#libelle].<br />Cette évaluation sera disponible sur la plateforme VAR[Application#url] à partir du VAR[Stage#dateFinValidation].<br /><br />Bien cordialement,<br />La commission Stages et Gardes du DFASM</p>",
            ],
            [
                "code" => CodesMailsProvider::AFFECTATION_STAGE_VALIDEE,
                "description" => "Mail envoyé aux étudiants pour les informer d'une modification de leurs affectations.",
                'engine' => 'default',
                "namespace" => "Mail",
                "document_type" => "mail",
                "document_sujet" => "VAR[Stage#libelle] - De nouvelles informations sont disponibles",
                "document_css" => "",
                "document_corps" => "<p>Cher(e), VAR[Etudiant#prenom]<br /><br /> Ce courriel vous est adressé car vous êtes inscrit pour le stage clinique VAR[Stage#libelle] qui aura lieux du VAR[Stage#dateDebut] au VAR[Stage#dateFin].<br /><br /> De nouvelles informations concernant votre affectation de stage sont disponibles.<br />Vous pouvez dès à présent les consulter en vous connectant sur la plateforme VAR[Application#url].<br /> <br />Bien cordialement,<br />La commission Stages et Gardes du DFASM</p>",
            ],
            [
                "code" => CodesMailsProvider::VAlIDATION_STAGE_RAPPEL,
                "description" => "Ré-envoie du token de validation au responsable d'un stage si celui ci n'as pas effectuer la validation",
                'engine' => 'default',
                "namespace" => "Mail",
                "document_type" => "mail",
                "document_sujet" => "Rappel - Validation du stage de VAR[Etudiant#nomPrenom]",
                "document_css" => "",
                "document_corps" => "<p>Bonjour,<br /><br />Vous recevez ce mail en tant qu'encadrant pédagogique du stage de VAR[Etudiant#nomPrenom] sur la période du VAR[Stage#dateDebut] au VAR[Stage#dateFin].<br /><br />A ce jour, nous n'avons pas reçu de validation de son stage.<br />La validation est rapide et s'effectue en allant sur le lien suivant VAR[Stage#urlValidation].<br /><br />Nous vous remercions par avance.<br /><br /> Bien cordialement,<br />La commission Stages et Gardes du DFASM<br /><br />Si vous recevez ce mail par erreur, merci de le signaler en envoyant un mail à assistance-stagetrek@unicaen.fr</p>",
            ],
            [ //Mails manuels
                "code" => CodesMailsProvider::VALIDATION_STAGE,
                "description" => "Mail (manuelle) envoyé à un responsable de stage contenant le lien de validation (pour un stage spécifique)",
                'engine' => 'default',
                "namespace" => "Mail",
                "document_type" => "mail",
                "document_sujet" => "Validation du stage de VAR[Etudiant#nomPrenom]",
                "document_css" => "",
                "document_corps" => "<p>Bonjour,<br><br>Vous recevez ce mail en tant qu'encadrant pédagogique du stage de VAR[Etudiant#nomPrenom] sur la période du VAR[Stage#dateDebut] au VAR[Stage#dateFin].<br><br>Merci de procéder à l'évaluation du stage en allant sur le lien suivant VAR[Stage#urlValidation] avant le VAR[Stage#dateFinValidation].<br><br>Bien cordialement,<br>La commission Stages et Gardes du DFASM<br><br><em>Si vous recevez ce mail par erreur, merci de le signaler en envoyant un mail à assistance-stagetrek@unicaen.fr</em></p>",
            ],
            [ //Mails automatique
                "code" => CodesMailsProvider::MAIL_AUTO_VALIDATIONS_STAGES,
                "description" => "Mail (automatique) envoyé aux responsables des stages contenant les liens de validations des stages qu'ils doivent valider",
                'engine' => 'default',
                "namespace" => "Mail",
                "document_type" => "mail",
                "document_sujet" => "Validations des stages",
                "document_css" => "",
                "document_corps" => "<p>Bonjour,<br><br>Vous recevez ce mail en tant qu'encadrant pédagogique des stages sur la période du VAR[Session#dateDebutStage] au VAR[Session#dateFinStage].</p>
<p>Vous trouverez ci dessous les liens de validation pour les stages que vous avez encadrés.</p>
<p>Merci de procéder à l'évaluation de ces derniers avant le VAR[Session#dateFinValidation] - VAR[Session#heureFinValidation].</p>
<p>VAR[Contact#listeUrlValidations]</p>
<p>Bien cordialement,<br>La commission Stages et Gardes du DFASM<br><br><em>Si vous recevez ce mail par erreur, merci de le signaler en envoyant un mail à assistance-stagetrek@unicaen.fr</em></p>",
            ],
            [ //Mails automatique
                "code" => CodesMailsProvider::MAIL_AUTO_LISTE_ETUDIANTS_STAGES,
                "description" => "Mail (automatique) envoyé aux responsables des stages contenant la liste des étudiants affectés dans leurs services",
                'engine' => 'default',
                "namespace" => "Mail",
                "document_type" => "mail",
                "document_sujet" => "Liste des étudiants en stages",
                "document_css" => "",
                "document_corps" => "<<p>Bonjour,<br><br>Vous recevez ce mail en tant qu'encadrant pédagogique des stages sur la période du VAR[Session#dateDebutStage] au VAR[Session#dateFinStage].</p>
<p>La liste des étudiants devant effectuer leur stage dans votre service sur cette période a été mise à jour.</p>
<p><em>(mise à jour le VAR[Date#dateCoutrante] à VAR[Date#heureCoutrante])</em></p>
<p>VAR[Contact#listeEtudiantsEncadres]</p>
<p>Bien cordialement,<br>La commission Stages et Gardes du DFASM<br><br><em>Si vous recevez ce mail par erreur, merci de le signaler en envoyant un mail à assistance-stagetrek@unicaen.fr</em></p>",
            ]
    ];

    }
}