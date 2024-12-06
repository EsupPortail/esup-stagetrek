
//Pour le bootrap datetimepicker qui semble poser quelques problème
//TODO : a revoir car non fonctionnel
// $(function () {
//
//     var l = window.location;
//     var base_url = l.protocol + "//" + l.host + "/" + l.pathname.split('/')[0];
//
//     WidgetInitializer.add('bootstrap-datetimepicker', 'bootstrapDatetimepicker', function(){
//         WidgetInitializer.includeJs(base_url + '/unicaen/app/js/bootstrap-datetimepicker.min.js');
//         WidgetInitializer.includeCss(base_url + '/unicaen//app/css/bootstrap-datetimepicker.min.css');
//     });
// })

/*
    Code pour le chargement lorsqu'un formulaire est soumis
 */
function addLoadingProgress(event){
    if(!event.target.classList.contains("loadingForm")){return;}

    var element = $(event.target);
    loadContent = "<span class='loadingInProgress fas fa-spinner fa-pulse fas fa-4x'></span>";
    element.append(loadContent);
}
$(function() {
    let body = $("body");
    body.on('submit', addLoadingProgress);
})

/*
 * Code JS qui ferme automatiquement les boites modales et les pop ajax après un certains timeout
 *
 * Ie de modal :
 *  <a  id="[ID]"
 *      class="ajax-modal"
 *      data-event="event-add/edit"
 *      href="[URL]"
 *  >
 *      [Contenue du liens]
 *  </a>
 *
 * Ie de pop ajax avec la demande de confirmation
 * <a   id="[id]"
 *      class="pop-ajax"
 *      href="[URL]"
 *      data-content="[Message de confirmation]"
 *      data-confirm="true"
 *      data-submit-close="true"
 *      data-submit-event="event-delete"
 * >
 *      [Contenue du liens]
 *  </a>
 */

// TODo delete ?
// const timeout=20000;
const timeout=0;

function closeModal(event){
    var target = $(event.target);
    target.find('#flash-message').refresh();
    setTimeout(function(){
        event.div.modal('hide');
        // window.location.reload();
    },timeout);
}
//
// $(function() {
//     let body = $("body");
//     body.on("event-add",closeModal);
//     body.on("event-import",closeModal);
//     body.on("event-delete",function(){window.location.reload();});
// });

/*
 * Code JS liée à des liste d'item un peu trop longue que l'on veut pouvoir déplié
 */
$.widget("stagetrek.longItemList", {
    longListShortSize: 3,
    longListExtendStep: 10,

    extendsLongListItem : function (event){
        liNode = this.parentNode;
        ulNode = liNode.parentNode;
        let cpt =0;
        for (i = 0; i < ulNode.childNodes.length; i++) {
            if(ulNode.childNodes[i].hidden) {
                ulNode.childNodes[i].hidden = false;
                cpt++;
                if (cpt >= this.longListExtendStep) return;
            }
        }
        //Si on est arrivé au max on supprime le bouton pour étendre
        ulNode.removeChild(liNode);
    },

    addLongItemListListener: function(longItemList)
    {
        if (longItemList.childNodes.length > this.longListShortSize) {
            for (i = this.longListShortSize; i < longItemList.childNodes.length; i++) {
                longItemList.childNodes[i].hidden = true;
            }
            textnode = document.createTextNode("...");
            linkExtendsNode = document.createElement("span");
            linkExtendsNode.classList.add('extendsLongListItem');
            linkExtendsNode.setAttribute('title', 'Voir la suite');
            linkExtendsNode.setAttribute('style', 'cursor: pointer');
            linkExtendsNode.addEventListener("click", this.extendsLongListItem);
            liNode = document.createElement("li");
            linkExtendsNode.appendChild(textnode);
            liNode.appendChild(linkExtendsNode);
            longItemList.appendChild(liNode);
        }
    },

    _create: function ()
    {
        var that = this;
        that.addLongItemListListener(that.element[0]);
    },
});

$(function ()
{
    WidgetInitializer.add('longItemList', 'longItemList');
});


/*Fonction basé sur UnicaenJs : AjaxModalListener permettant d'ouvrir  POST lors de l'ouverture de la modal
// Usage :
$([JQuerySelector]').on('click', function (e) {
     data['post1']=1;
     data['post2']=2
     //...
     openAjaxModalWithPostData(e, data);
     return false; //annule l'ouverture du lien
}
    exemple d'utilisation : fournir a une action la liste des éléments selectionnée dans un tableaux
 */
function openAjaxModalWithPostData(event, post){
    var anchor = $(event.currentTarget);
    if(anchor.is('form')){
        var url = anchor.attr('action');
    }
    else if(anchor.is('a')){
        var url = anchor.attr('href');
        //ToDO ; voir comment fournir les variables post malgré le fait que l'on est dans un target
        if (anchor.attr('target') === '_blank') {
            return;
        }
    }
    else if(anchor.is('button')){
        var url = anchor.attr('href');
    }
    else{
        return;
    }

    var modalDialog = AjaxModalListener.singleton.getModalDialog();

    if (url && url !== "#") {
        // transmet à la DIV le lien cliqué (car fournit l'événement à déclencher à la soumission du formulaire)
        modalDialog.data('a', anchor);
        AjaxModalListener.singleton.modalEventName = anchor.data('event');
        post['modal'] = 1; //Pour respecter le paramètre définit dans UnicaenJs
        $.post(url, post, $.proxy(function (data) {
            // remplacement du contenu de la fenêtre modale
            $(".modal-content", modalDialog.modal('show')).html(AjaxModalListener.singleton.extractNewModalContent(data));

        }, AjaxModalListener.singleton));
    }
    event.preventDefault();
}

/**
 * Fonction de formatage de l'instant t sous la forme dd-mm-YYYY_hh-mm-ss
 */

function getFormatedDateTime() {
    dt = new Date()
    dateFormat = `${
        dt.getDate().toString().padStart(2, '0')}-${
        (dt.getMonth() + 1).toString().padStart(2, '0')}-${
        dt.getFullYear().toString().padStart(4, '0')}_${
        dt.getHours().toString().padStart(2, '0')}-${
        dt.getMinutes().toString().padStart(2, '0')}-${
        dt.getSeconds().toString().padStart(2, '0')}`
    ;
    return dateFormat
}

/** Fonction qui convertie un tableau JSON DATA en fichier csv à télécharger
 *
 * Exemple d'usage : modéle de template d'importation des données
 *
 * //Liens pour le téléchargement
 * <a class="import-template" id="import-template"><span class="fa fa-file-csv"></span>Template</a>
 *
 * $table[]=['Entête Col 1' => "Valeur row 1", 'Entête Col 2' => "Valeur row 1"];
 * $table[]=['Entête Col 1' => "Valeur row 2", 'Entête Col 2' => "Valeur row 2"];
 * $table[]=['Entête Col 1' => "Valeur row 2", 'Entête Col 2' => "Valeur row 2"];
 *
 * <script>
 *     $('#import-template').on('click', function() {
 *       let tableau = <?php echo json_encode($table); ?>;
 *       downloadCSVFromJson('template_importation_'+ Date.now().toString() +'.csv', tableau);
 *   });
 * </script>
 *
 * **/
function downloadCSVFromJson(filename, arrayOfJson){
    if(arrayOfJson.length===0){
        arrayOfJson = [{"Aucun résultat":""}];
    }
    // convert JSON to CSV
    const replacer = (key, value) => value === null ? '-' : value
    const header = Object.keys(arrayOfJson[0])

    let csv = arrayOfJson.map(row => header.map(fieldName =>
        JSON.stringify(row[fieldName], replacer)).join(';'))
    csv.unshift(header.join(';'))
    csv = csv.join('\r\n')
    // Create link and download
    var link = document.createElement('a');
    link.setAttribute('href', 'data:text/csv;charset=utf-8,%EF%BB%BF' + encodeURIComponent(csv));
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}


/**
 * Permet d'aller d'appeler une action en fournissant des Post Data
 * Exemple d'usage : l'export du datatbale des terrains de stage
 * qui fournis en Post['terrains'] les identifiants des terrains à exporter
 function exporter(event){
        event.preventDefault();
        var rows = tableInstance.rows({'search': 'applied'}).nodes();
        var terrains = {};
        rows.each(function (r) {
            id = $(r).data('terrainId');
            terrains['terrains['+id+']']=id;
        });
        var anchor = $(event.currentTarget);
        var url = anchor.attr('href');
        redirectWithPostData(url,terrains);
        return false;
    }
 $('#exporter').on('click', exporter);
 * @param actionUrl
 * @param params
 */
function redirectWithPostData(actionUrl, params)
{
    if(!actionUrl){
        return false;
    }
    if(params) {
        var mapForm = document.createElement("form");
        mapForm.method = "POST";
        mapForm.action = actionUrl;
        for (const [key, value] of Object.entries(params)) {
            var mapInput = document.createElement("input");
            mapInput.type = "text";
            mapInput.name = key;
            mapInput.value =value;
            mapForm.appendChild(mapInput);
        }
        document.body.appendChild(mapForm);
        mapForm.submit();
        //utile nottament si l'on demande a ouvrir dans un nouvelle onglet, dans une modal ...
        document.body.removeChild(mapForm);
    }
}

//TODO : voir comment corriger le bug d'affichage lors d'un refresh liée a l'utilisation du container:'body'. sans limité la zone du tooltip a la div parente
//     $(function () {
//         let body = $('body');
//         body.tooltip({
//             selector: '[data-toggle="tooltip"]',
//             html: true
//         });
//         body.on('shown.bs.modal', function (e) {
//             body.tooltip({
//                 selector: '[data-toggle="tooltip"]',
//                 html: true
//             });
//         })
//     });

// back button pour permettre de revenir sur la page précédente
$('.btn-back').on('click', function(event){
    // $(this).attr("disabled", 'disabled');
    // var loading = "fas fa-spinner fa-pulse fas";
    // $(this).children('i').removeClass().addClass(loading);

    var href = $(this).attr("href");
    if(href  === undefined || href===""){
        event.preventDefault();
        parent.history.back();
        return false;
    }
});

//gestions des fragements des tab panels
function addFragementOnUrl(event){
    if($(this).attr("disabled")){return  false;}
    $(this).attr("disabled", 'disabled');
    var loading = "fas fa-spinner fa-pulse fas";
    $(this).children('i').removeClass().addClass(loading);

    var hash = window.location.hash;
    if(hash===""){
        return true;
    }
    var href = $(this).attr("href");
    $(this).attr("href", href+""+hash);
}
$(function () {
    // // on load of the page: switch to the currently selected tab
    var hash = window.location.hash;
    $('.nav-tabs  a[href="' + hash + '"]').tab('show');

    // Rajou du hash dans l'url ?
    // $("ul.nav-tabs > li > a").on("shown.bs.tab", function (e) {
    //     if((e.target).hasAttribute("href")) {
    //         var fragment = $(e.target).attr("href").substr(1);
    //         if (fragment === "") return;
    //         window.location.hash = fragment;
    //     }
    // });
});

//Pour simuler le refresh des selectpicker qui est bugguer
$(function () {
    $('body').on('refreshed.bs.select', function (e, clickedIndex, newValue, oldValue) {
        $(e.target).selectpicker('destroy').selectpicker('render').addClass('selectpicker');
    });
    //
});


// loading button sur les formulaire (nottament de recherche)
$(function () {
    $('form').on('submit', function () {
        var btn = $(this).find('button[type="submit"]:first');
        btn.attr("disabled", "disabled")
            .find('span').attr('class', 'fas fa-pulse fa-spinner');
        // FontAwesome SVG
        // .find('[data-fa-i2svg]')
        // .attr('data-icon', 'spinner')
    }).on('submit', function () {
        // Cas particulier des input[type=submit] :
        // - l'attribut "disabled" ne doit pas être utilisé, sous peine de retirer des données POSTées l'input cliqué ;
        // - impossible d'ajouter un "spinner".
        $(this).find('input[type="submit"]')
            .addClass("disabled");
    });
    $("form.rechercherForm button.reset").click(function () {
        var form = $(this).parents('form:first');
        $(':input', form)
            .not(':button, :submit, :reset, :checkbox, :radio')
            .val('');
        $(':input', form)
            .prop('checked', false)
            .prop('selected', false)
            .trigger('propertychange');
        $('.selectpicker').val('').selectpicker('refresh');
    });
});


