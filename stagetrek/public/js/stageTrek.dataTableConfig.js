/**
 * @author Thibaut Vallée <thibaut.vallee at unicaen.fr>
 * Fichier jS qui gére les paramètres par défaut des Datatable
 * @see https://datatables.net/reference/option/ pour la liste (probablement compléte) des différentes otpions
 */


$(function ()
{
    var l = window.location;
    var base_url = l.protocol + "//" + l.host + "/" + l.pathname.split('/')[0];

    WidgetInitializer.add('table-data', 'tableData');

});

//Class a utiliser dans les entêtes de colonne (l'usage est assez explicite
// no-sort
// no-seach
// hidden
// no-export

// Dataattribute dont l'usage est relative claire
// data-order
// data-filter
// data-type="num"

function generateDataTableSetings(newOptions){
    options = {
        autoWidth: false,
        stateSave: true,
        retrieve: true,
        responsive: true,
        order: [[0, 'asc']],
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]],
        columnDefs: [
            {targets: ['no-sort'], orderable: false},
            {targets: ['no-search'], searchable: false},
            {targets: ['hidden'], visible: false},
        ],
        "language": {
            'lengthMenu': "Afficher _MENU_ &eacute;l&eacute;ments",
            "search": "Filtre de recherche : _INPUT_",
            "loadingRecords": "Chargement en cours...",
            'info': "<small class=\"text-highlight\">Affichage : <strong><i class=\"far fa-list-alt\"></i> _START_ - _END_ sur _TOTAL_</strong></small>",
            'infoEmpty': "",
            'infoFiltered': "<small class=\"text-highlight\">(_MAX_ &eacute;l&eacute;ments au total)</small>",
            'emptyTable': "Aucune donnée disponible.",
            'zeroRecords': "Aucun enregistrement trouvé.",
            "paginate": {
                "previous": "<i class=\"fas fa-chevron-left\"></i>",
                "next": "<i class=\"fas fa-chevron-right\"></i>"
            },
            "select": {
                "rows": {
                    0: "",
                    1: "<small class=\"text-highlight\">1 selectionné</small>",
                    _: "<small class=\"text-highlight\">%d selectionnés</small>",
                }
            }
            // "url": "https://gest.unicaen.fr/public/DataTables-1.10.12/plug-ins/i18n/french.json",
        },
        "createdRow": function (row, data, index) {
            $('.pop-ajax', row).popAjax();
        },
        // maj éventuel des popover qui serait contenue dans le table
        "drawCallback": function (settings) {
            $(this).find('[data-bs-toggle="popover"]').popover({html: true, sanitize: false});
        },
    };
    if(newOptions) {
        Object.entries(newOptions).forEach(([key, value]) => {
            options[key] = value;
        });
    }
    return options;
}

//Retourne les options JS pour l'export a rajouter ensuite dans les options
// ie : let btnOptions = getExportButtonSetings()
// options = {
//      buttons: btnOptions
// }
function getExportButtonSetings(filteTitle, title="Export"){
    btnOptions = [
        {
            extend: 'csvHtml5',
            text: '<i class="fas fa-file-csv"></i> Csv',
            className: 'export-btn btn btn-success bg-primary',
            titleAttr: "Export CSV",
            fieldSeparator: ';',
            filename : filteTitle,
            title : title,
            exportOptions: {
                orthogonal: 'export',
                columns: ':not(.no-export)',
                format: {
                    body: function ( data, row, column, node ) {
                        return $(node).data('export') ? $(node).data('export') : data;
                    },
                }
            }
        },
        {
            extend: 'excelHtml5',
            text: '<i class="fas fa-file-excel"></i> Excel',
            className: 'export-btn btn btn-success bg-success',
            titleAttr: "Export Excel",
            filename : filteTitle,
            title : title,
            exportOptions: {
                orthogonal: 'export',
                columns: ':not(.no-export)',
                format: {
                    body: function ( data, row, column, node ) {
                        return $(node).data('export') ? $(node).data('export') : data;
                    },
                }
            }
        },
        {
            extend: 'pdfHtml5',
            text: '<i class="fas fa-file-pdf"></i> Pdf',
            className: 'export-btn btn btn-danger bg-danger',
            titleAttr: "Export PDF",
            orientation: 'landscape',
            filename : filteTitle,
            title : title,
            exportOptions: {
                orthogonal: 'export',
                columns: ':not(.no-export)',
                format: {
                    body: function ( data, row, column, node ) {
                        return $(node).data('export') ? $(node).data('export') : data;
                    },
                }
            }

        }
    ];
    return btnOptions
}

$.widget("stagetrek.tableData", {

    _create: function () {
        var moment = 'DD/MM/YYYY - HH:mm:ss';

        var data_moment = this.element.data('table-date-format');
        $.fn.dataTable.moment(typeof data_moment !== 'undefined' ? data_moment : moment);


        var options = generateDataTableSetings();
        //Rajout d'options fournis en data-attributes du tableaux

        var data_state_save = this.element.data('table-state-save');
        if(typeof data_state_save !== 'undefined') {
            options.stateSave = data_state_save;
        }

        var data_order = this.element.data('table-order');
        if (typeof data_order !== 'undefined') {
            var orderby = [];
            if(data_order !== '') {
                var result = (data_order).split(';');
                $.each(result, function (i, v) {
                    var ob = (v + ',').split(',');
                    orderby.push([
                        ob[0] !== "" ? ob[0] : order,
                        ob[1] !== "" ? ob[1].toLowerCase() : by
                    ]);
                });
            }

            options.order = orderby;
        }
        this.element.DataTable(options);
    },
});


// Exemple d'utilisation en inline
// var tableInstance = $('#tableId').DataTable({
//     "autoWidth": true,
//     //Informations liée au datatable.
//     "dom": "<'row' <'toolbar col-sm-12 col-md-12'>>" +
//         "<'row' <'filter col-sm-12 col-md-12'> >" +
//         "<'row' <'col-sm-12 col-md-3 ' l> <'col-sm-12 col-md-9 text-right' f> >" +
//         "<'row' <'col-sm-12'tr> >" +
//         "<'row' <'col-sm-12 col-md-5'i> <'col-sm-12 col-md-7'p> >",
//     "paging": true,
//     "pagingType": "simple_numbers",
//     "lengthMenu": [[50, 100, 250, -1], [50, 100, 250, "Tout"]],
//     "order": [[ 0, "asc"], [1, "desc"]],
//     "select": {"style": 'multiple',"selector": 'tr'},
//     // "select": {"style": 'os',"selector": 'td:first-child'},
//     // "select": {"style": 'multiple', "selector": '.select_row'},
//     "info": true,
//         "columnDefs": [
//         {"targets": ['nosearch'], "searchable": false},
//         {"targets": ['nosort'], "orderable": false},
//         {"targets": ['hidden'], "visible": false},
//     ],
//     "language": {
//         'lengthMenu': "Afficher _MENU_ &eacute;l&eacute;ments",
//             "search": "Filtre de recherche : _INPUT_",
//             "loadingRecords": "Chargement en cours...",
//             'info': "<small class=\"text-highlight\">Affichage : <strong><i class=\"far fa-list-alt\"></i> _START_ - _END_ sur _TOTAL_</strong></small>",
//             'infoEmpty': "",
//             'infoFiltered': "<small class=\"text-highlight\">(_MAX_ &eacute;l&eacute;ments au total)</small>",
//             'emptyTable': "aucune donnée disponible",
//             'zeroRecords': "aucun enregistrement trouvé",
//             "paginate": {
//             "previous": "<i class=\"fas fa-chevron-left\"></i>",
//                 "next": "<i class=\"fas fa-chevron-right\"></i>"
//         },
//     },
// });

// $.fn.dataTable.moment('DD/MM/YYYY');
// $.extend($.fn.dataTable.defaults, {
//     autoWidth: false,
//     //Pagination
//     paging: true,
//     pagingType: "simple_numbers",
//     pageLength : 50,
//     lengthMenu: [[50, 100, 250, -1], [50, 100, 250, "Tout"]],
//     //Ordre
//     // order: [], //Pas d'ordre par défaut
//     //Classes amenant à des propriété
//     columnDefs: [
//         {targets: ['nosort'], orderable: false},
//         {targets: ['nosearch'], searchable: false},
//         {targets: ['hidden'], visible: false},
//     ],
//
//     // fixedHeader: true,
//     fixedHeader: {
//         headerOffset: $('#navbar').outerHeight(false) //Permet de fixer en dessous de la bar de navigation
//         //outerHeigt([includeMargin])
//     },
//     // "scrollY": '50vh', "scrollX": false, "scrollCollapse": true,
//     // responsive: true,
//     retrieve: true, //Permet de garder la même instance JS
//     stateSave: true, //On garde les recherches
//     // stateDuration : 2*60, //durée de concersvartion des donnéesdu state save (en seconde)
//     // select :{
//     //     style: 'multiple',
//     //     selector: 'td:first-child',
//     // },
//     //Placement des informations !!! doit nécessairement être définit avant la section "Informations" pour être pris en compte
//     //toolbar
//     dom: "<'row' <'col-sm-12 col-md-3 ' l> <'col-sm-12 col-md-9 text-right' f> >" + //l : lengthMenu | f : filtre global
//         "<'row' <'col-sm-12 col-md-12' t> >" + //Tableau
//         "<'row' <'col-sm-12 col-md-5'i> <'col-sm-12 col-md-7'p> >", //i : info | p : pagination
//     //Mémorise l'état lors d'un refresh
//     // @see https://datatables.net/reference/option/stateLoadCallback pour gerer comment sont sauvegarder/recharger les données
//
//     //Informations
//     info: true,
//     language: {
//         lengthMenu: "Afficher _MENU_ &eacute;l&eacute;ments",
//         search: "Rechercher : _INPUT_",
//         loadingRecords: "Chargement en cours...",
//         info: "<small class=\"text-highlight\">Affichage : <strong><i class=\"far fa-list-alt\"></i> _START_ - _END_ sur _TOTAL_</strong></small>",
//         infoEmpty: "",
//         infoFiltered: "<small class=\"text-highlight\">(_MAX_ &eacute;l&eacute;ments au total)</small>",
//         emptyTable: "Aucune donnée disponible",
//         zeroRecords: "Aucun enregistrement trouvé",
//         paginate: {
//             previous: "<i class=\"fas fa-chevron-left\"></i>",
//             next: "<i class=\"fas fa-chevron-right\"></i>"
//         },
//     },
//
// });
