<?php

namespace Application;

return [
    //Inclusions des fichiers js et css
    //Mettre les librairies dans publics
    'public_files' => [
        'head_scripts' => [
            //Jquery
            '015_jquery' => 'unistrap-1.0.0/js/jquery-3.6.1.min.js',
            '020_jqueryui' => 'unistrap-1.0.0/lib/jquery-ui-1.13.2/jquery-ui.min.js',
            '050_select2' => "/lib/select2-4.0.13/dist/js/select2.min.js",
            '050_select2_fr' => "/lib/select2-4.0.13/dist/js/i18n/fr.js",
            //Bootstrap
            '040_bootstrap' => '',
        ],
        'inline_scripts' => [
            '020_app' => '/js/app.js',
//            '030_util' => '/unicaen/app/js/util.js',
//            '040_unicaen' => '/unicaen/app/js/unicaen.js',
            //Jquery
            '050_jquery_form' => '',
            '070_bootstrap' => 'unistrap-1.0.0/lib/bootstrap-5.2.2/dist/js/bootstrap.bundle.min.js',
            '080_unistrap' => 'unistrap-1.0.0/js/unistrap.js',

            /** Selectipicker */
            '120_bootstrap-select' => '/lib/bootstrap-select-1.14.0-beta3/js/bootstrap-select.min.js',
            '120_bootstrap-select-fr' => '/lib/bootstrap-select-1.14.0-beta3/js/i18n/defaults-fr_FR.js',

            /** Datatable */
            '100_moment' => '/lib/moment-2.30.1/moment-with-locales.js',
            '130_datatables' => '/lib/DataTables/datatables.js',
            '131_datatables' => '/lib/DataTables/datetime-moment.js',
            '133_datatables' => '/lib/DataTables/Buttons-2.2.3/js/dataTables.buttons.min.js',
            '134_datatables' => '/lib/DataTables/ColReorder-1.5.6/js/dataTables.colReorder.min.js',
            '135_datatables' => '/lib/DataTables/DateTime-1.1.2/js/dataTables.dateTime.min.js',
            '139_datatables' => '/lib/DataTables/JSZip-2.5.0/jszip.min.js',
            '140_datatables' => '/lib/DataTables/pdfmake-0.1.36/pdfmake.min.js',
            '141_datatables' => '/lib/DataTables/pdfmake-0.1.36/vfs_fonts.js',
            '142_datatables' => '/lib/DataTables/Responsive-2.3.0/js/dataTables.responsive.min.js',
            '143_datatables' => '/lib/DataTables/RowGroup-1.2.0/js/dataTables.rowGroup.min.js',
            '144_datatables' => '/lib/DataTables/RowReorder-1.2.8/js/dataTables.rowReorder.min.js',
            '149_datatables' => '/lib/DataTables/StateRestore-1.1.1/js/dataTables.stateRestore.min.js',
            '150_dataTableConfig' => 'js/stageTrek.dataTableConfig.js',


            '151_tinymce' => 'lib/tinymce_7.6.0/js/tinymce/tinymce.min.js',
            '152_tinymce2' => 'lib/form_fiche.js',
        ],
        'stylesheets' => [
            '010_jquery-ui' => '/lib/jquery-ui-1.12.1/jquery-ui.min.css',
            '020_jquery-ui-structure' => '/lib/jquery-ui-1.12.1/jquery-ui.structure.min.css',
            '030_jquery-ui-theme' => '/lib/jquery-ui-1.12.1/jquery-ui.theme.min.css',

             /** From UniStrap */
            '040_bootstrap' => 'unistrap-1.0.0/lib/bootstrap-5.2.2/dist/css/bootstrap.min.css',
            '041_ubuntu' => 'unistrap-1.0.0/css/font-ubuntu.css',
            '042_unistrap' => 'unistrap-1.0.0/css/unistrap.css',
            /** Selectipicker */
            '120_' => '/lib/bootstrap-select-1.14.0-beta3/css/bootstrap-select.min.css',

            '060_unicaen'             => '',
            '061_form' => '/css/form.css',
            '062_misc' => '/css/misc.css', //Divers a retravailler
            '070_app' => '/css/app.css',

            '112_' => 'lib/fontawesome-free-6.3.0-web/css/all.min.css',
            '065_unicaen-icon' => 'unicaen/app/css/unicaen-icon.css',
            '075_logos'        => 'css/logos.css',

            '101_' => 'css/timeline.css',

            '500_select2' => "lib/select2-4.0.13/dist/css/select2.min.css",
            '10000_etat' => 'unicaen/etat/css/unicaen-etat.css',
            '10000_evenement' => 'unicaen/evenement/css/unicaen-evenement.css',
            '11000_mail' => 'unicaen/mail/css/unicaen-mail.css',

            '0129_datatables' => '/css/stagetrek.dataTables.css',
            '0130_datatables' => '/lib/DataTables/datatables.min.css',
            '0132_datatables' => '/lib/DataTables/Buttons-2.2.3/css/buttons.dataTables.min.css',
            '0133_datatables' => '/lib/DataTables/ColReorder-1.5.6/css/colReorder.dataTables.min.css',
            '0134_datatables' => '/lib/DataTables/DateTime-1.1.2/css/dataTables.dateTime.min.css',
            '0140_datatables' => '/lib/DataTables/Responsive-2.3.0/css/responsive.dataTables.min.css',
            '0141_datatables' => '/lib/DataTables/RowGroup-1.2.0/css/rowGroup.dataTables.min.css',
            '0142_datatables' => '/lib/DataTables/RowReorder-1.2.8/css/rowReorder.dataTables.min.css',
            '0147_datatables' => '/lib/DataTables/StateRestore-1.1.1/css/stateRestore.dataTables.min.css',

//
        ],
        'printable_stylesheets' => [
        ],
        'cache_enabled'         => false,
    ],
];