<?php


namespace Application\Provider\Parametre;


class ParametreProvider
{
    //Logs
    const CONSERVATION_LOG = 'conservation_log';
    const CONSERVATION_MAIL = 'conservation_mail';
    const CONSERVATION_EVENEMENT = 'conservation_evenement';

    //Footer

    const FOOTER_UNIV_NAME = 'footer_univ_nom';
    const FOOTER_UNIV_URL = 'footer_univ_url';
    const FOOTER_UNIV_LOGO = 'footer_univ_logo';
    const FOOTER_UNIV_CONTACT = 'footer_univ_contact';
    const FOOTER_UNIV_VIE_PRIVEE = 'footer_univ_vie_privee';
    const FOOTER_UNIV_MENTIONS_LEGALS = 'footer_univ_mentions_legals';

    // Dates des stages
    const DATE_CALCUL_ORDRES_AFFECTATIONS = 'date_calcul_ordres_affectations';
    const DATE_PHASE_CHOIX = 'date_phase_choix';
    const DUREE_PHASE_CHOIX = 'duree_phase_choix';
    const DATE_PHASE_AFFECTATION = 'date_phase_affectation';
    const DUREE_STAGE = 'duree_stage';
    const DATE_PHASE_VALIDATION = 'date_phase_validation';
    const DUREE_PHASE_VALIDATION = 'duree_phase_validation';
    const DATES_PHASE_EVALUATION = 'date_phase_evaluation';
    const DUREE_PHASE_EVALUATION = 'duree_phase_evaluation';

    // Préférences
    // TODO : faires des paramètres pour la formule de calcul d'estimations des demandes
    const NB_PREFERENCES = 'nb_preferences';

    //Validations des stages
    const DUREE_TOKEN_VALDATION_STAGE = 'duree_token_validation';

    // Mails
    const DATE_PLANIFICATIONS_MAILS = 'date_planifictions_mails';
    const DELAI_RAPPELS = 'delai_rappels';

    // Convention de stage
    const NOM_UFR_SANTE = 'nom_ufr';
    const ADRESSE_UFR_SANTE = 'adresse_ufr_sante';
    const TELEPHONE_UFR_SANTE = 'tel_ufr_sante';
    const MAIL_UFR_SANTE = 'mail_ufr_sante';
    const FAX_UFR_SANTE = 'fax_ufr_sante';
    const DOYEN_UFR_SANTE = 'doyen_ufr_sante';
    const NOM_CHU = 'nom_chu';
    const DIRECTEUR_CHU = 'directeur_chu';
    const DUREE_CONSERVCATION = 'duree_conservation';

    // Procédures d'affectations
    const PROCEDURE_AFFECTATION = 'procedure_affectation';
    const COEF_INADEQUATION = 'coef_inadequation';
    const PRECISION_COUT_AFFECTATION = 'precision_cout_affectation';
    const FACTEUR_CORRECTEUR_COUT_TERRAIN = 'facteur_correcteur_cout_terrain';
    const FACTEUR_CORRECTEUR_BONUS_MALUS = 'facteur_correcteur_bonus_malus';
    const AFFECTATION_COUT_TERRAIN_MAX = 'cout_terrain_max';
    const AFFECTATION_COUT_TOTAL_MAX = 'cout_total_max';

}