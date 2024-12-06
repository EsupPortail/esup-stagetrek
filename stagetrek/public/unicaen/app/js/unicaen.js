/**
 * unicaen.js
 *
 * Javascript commun à toutes les applis.
 */
$(function ()
{
    /**
     * Détection de réponse "403 Unauthorized" aux requêtes AJAX pour rediriger vers
     * la page de connexion.
     */
    $(document).ajaxComplete(function (event, xhr, settings)
    {
        if (xhr.status === 403) {
            alert("Opération non autorisée ou session expirée.");
            xhr.abort();
        }
    });

    /**
     * Installation d'un lien permettant de remonter en haut de la page.
     * Ce lien apparaît lorsque c'est nécessaire.
     */
    if ($(window).scrollTop() > 100) {
        $('.scrollup').fadeIn();
    }
    $(window).on("scroll", function ()
    {
        if ($(this).scrollTop() > 100) {
            $('.scrollup').fadeIn();
        }
        else {
            $('.scrollup').fadeOut();
        }
    });
    $('.scrollup').on("click", function ()
    {
        $("html, body").animate({scrollTop: 0}, 300);
        return false;
    });

    ajaxPopoverInit();
    AjaxModalListener.install();

    /* Utilisation du WidgetInitializer et de l'intranavigator */
    WidgetInitializer.install();
    IntraNavigator.install();
});



/**
 * Système d'initialisation automatique de widgets
 *
 */
WidgetInitializer = {

    /**
     * Liste des widgets déclarés (format [className => widgetName])
     * className = Nom de la classe CSS qui déclenche l'association
     * widgetName = Nom du widget (sans le namespace)
     */
    widgets: {},

    use: function (className)
    {
        if (!this.widgets[className]) {
            console.log('ATTENTION : Widget ' + className + ' non déclaré!!');
            return;
        }

        var widgetName = this.widgets[className].widgetName;
        var onInitialize = this.widgets[className].onInitialize;
        var widgets = $('.' + className);

        if (widgets.length > 0) {
            if (undefined != onInitialize && !WidgetInitializer.widgets[className].initialized) {
                onInitialize();
                WidgetInitializer.widgets[className].initialized = true;
            }
            if (widgetName) {
                widgets.each(function ()
                {
                    $(this)[widgetName]($(this).data('widget'));
                });
            }
        }
    },

    /**
     * Ajoute un nouveau Widget à l'initializer
     *
     * @param string className
     * @param string widgetName
     */
    add: function (className, widgetName, onInitialize)
    {
        WidgetInitializer.widgets[className] = {
            widgetName: widgetName,
            onInitialize: onInitialize,
            initialized: false
        };
        this.use(className);
    },

    /**
     * Lance automatiquement l'association de tous les widgets déclarés avec les éléments HTMl de classe correspondante
     */
    run: function ()
    {
        for (className in this.widgets) {
            this.use(className);
        }
    },

    /**
     * Installe le WidgetInitializer pour qu'il se lance au chargement de la page ET après chaque requête AJAX
     */
    install: function ()
    {
        var that = this;

        this.run();
        $(document).ajaxSuccess(function ()
        {
            that.run();
        });
    },

    includeCss: function (fileName)
    {
        if (!$("link[href='" + fileName + "']").length) {
            var link = '<link rel="stylesheet" type="text/css" href="' + fileName + '">';
            $('head').append(link);
        }
    },

    includeJs: function (fileName)
    {
        if (!$("script[src='" + fileName + "']").length) {
            var script = '<script type="text/javascript" src="' + fileName + '">' + '</script>';
            $('body').append(script);
        }
    }
};



IntraNavigator = {

    getElementToRefresh: function (element)
    {
        return $($(element).parents('.intranavigator').get(0));
    },



    refreshElement: function (element, data, isSubmit)
    {
        element.html(data);
        element.trigger('intranavigator-refresh', {element: element, isSubmit: isSubmit});
    },



    hasErrors: function (element) {
        if (typeof element === 'string') {
            element = $('<div>' + element + '</div>');
        }

        var errs = element.find('.input-error, .has-error, .has-errors, .alert.alert-danger').length;

        return errs > 0;
    },



    extractTitle: function (element) {
        var res = {
            content: undefined,
            title: undefined
        };

        if (typeof element === 'string') {
            element = $('<div>' + element + '</div>');
        }

        var extractedTitle = element.find('.title,.modal-title,.popover-title,.page-header');

        if (extractedTitle.length > 0) {
            res.title = extractedTitle.html().trim();
            extractedTitle.remove();
        }
        res.content = element.html().trim();

        return res;
    },



    embeds: function (element)
    {
        return $(element).parents('.intranavigator').length > 0;
    },



    add: function (element)
    {
        if (!$(element).hasClass('intranavigator')) {
            $(element).addClass('intranavigator');
            //IntraNavigator.run();
        }
    },



    waiting: function (element, message)
    {
        if ($(element).find('.intramessage').length == 0) {
            var msg = message ? message : 'Chargement';
            msg += ' <span class="loading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
            msg = '<div class="alert alert-success intramessage" role="alert">' + msg + '</div>';
            $(element).append(msg);
        } else {
            $(element).find('.intramessage').show();
        }
    },



    endWaiting: function ()
    {
        $('.intramessage').hide();
    },



    formSubmitListener: function (e)
    {
        var form = $(e.target);
        var postData = form.serializeArray(); // paramètre "modal" indispensable
        var url = form.attr('action');
        var elementToRefresh = IntraNavigator.getElementToRefresh(form);

        if (elementToRefresh) {
            // requête AJAX de soumission du formulaire
            IntraNavigator.waiting(elementToRefresh, 'Veuillez patienter s\'il vous plaît...');
            $.post(url, postData, $.proxy(function (data)
            {
                IntraNavigator.refreshElement(elementToRefresh, data, true);
            }, this));
        }
        e.preventDefault();
    },



    innerAnchorClickListener: function (e)
    {
        var anchor = $(e.currentTarget);
        var url = anchor.attr('href');
        var elementToRefresh = IntraNavigator.getElementToRefresh(anchor);

        if (elementToRefresh && url && url !== "#") {
            // requête AJAX pour obtenir le nouveau contenu de la fenêtre modale
            IntraNavigator.waiting(elementToRefresh, 'Chargement');
            $.get(url, {}, $.proxy(function (data)
            {
                IntraNavigator.refreshElement(elementToRefresh, data, false);
            }, this));
        }

        e.preventDefault();
    },



    /*btnPrimaryClickListener: function (e)
     {
     var form = IntraNavigator.getElementToRefresh(e.target).find('form');
     if (form.length) {
     form.submit();
     e.preventDefault();
     }
     },*/

    /**
     * Lance automatiquement l'association de tous les widgets déclarés avec les éléments HTMl de classe correspondante
     */
    run: function ()
    {
        var submitSelector = '.intranavigator form:not(.no-intranavigation)';
        var clickSelector = '.intranavigator a:not(.pop-ajax):not(.ajax-modal):not(.no-intranavigation):not(.no-intranavigation a)';

        /* TODO: trouver une meilleure solution que d'utiliser la classe CSS "no-intranavigation" pour désactiver l'intra-navigation ?*/

        $('body').off("submit", submitSelector, IntraNavigator.formSubmitListener);
        $('body').off("click", clickSelector, IntraNavigator.innerAnchorClickListener);
        //$('body').off("click", ".intranavigator .btn-primary", IntraNavigator.btnPrimaryClickListener);

        $('body').one("submit", submitSelector, IntraNavigator.formSubmitListener);
        $('body').one("click", clickSelector, IntraNavigator.innerAnchorClickListener);

        //$('body').one("click", ".intranavigator .btn-primary", IntraNavigator.btnPrimaryClickListener);
        // Réglage du focus sur le champ de formulaire ayant l'attribut 'autofocus'
        $('.intranavigator [autofocus]').trigger("focus");
    },



    /**
     * Installe le WidgetInitializer pour qu'il se lance au chargement de la page ET après chaque requête AJAX
     */
    install: function ()
    {
        var that = this;

        this.run();
        $(document).ajaxSuccess(function ()
        {
            that.run();
            that.endWaiting();
        });
    }
};



/**
 * Autocomplete jQuery amélioré :
 * - format de données attendu pour chaque item { id: "", value: "", label: "", extra: "" }
 * - un item non sléctionnable s'affiche lorsqu'il n'y a aucun résultat
 *
 * @param Array options Options de l'autocomplete jQuery +
 *                      {
 *                          elementDomId: "Id DOM de l'élément caché contenant l'id de l'item sélectionné (obligatoire)",
 *                          noResultItemLabel: "Label de l'item affiché lorsque la recherche ne renvoit rien (optionnel)"
 *                      }
 * @returns description self
 */
$.fn.autocompleteUnicaen = function (options)
{
    var defaults = {
        elementDomId: null,
        noResultItemLabel: "Aucun résultat trouvé.",
    };
    var opts = $.extend(defaults, options);
    if (!opts.elementDomId) {
        alert("Id DOM de l'élément invisible non spécifié.");
    }
    var select = function (event, ui)
    {
        // un item sans attribut "id" ne peut pas être sélectionné (c'est le cas de l'item "Aucun résultat")
        if (ui.item.id) {
            $(event.target).val(ui.item.label);
            $('#' + opts.elementDomId).val(ui.item.id);
            $('#' + opts.elementDomId).trigger("change", [ui.item]);
        }
        return false;
    };
    var response = function (event, ui)
    {
        if (!ui.content.length) {
            ui.content.push({label: opts.noResultItemLabel});
        }
    };
    var element = this;
    element.autocomplete($.extend({select: select, response: response}, opts))
    // on doit vider le champ caché lorsque l'utilisateur tape le moindre caractère (touches spéciales du clavier exclues)
        .keypress(function (event)
        {
            if (event.which === 8 || event.which >= 32) { // 8=backspace, 32=space
                var lastVal = $('#' + opts.elementDomId).val();
                $('#' + opts.elementDomId).val(null);
                if (null === lastVal) $('#' + opts.elementDomId).trigger("change");
            }
        })
        // on doit vider le champ caché lorsque l'utilisateur vide l'autocomplete (aucune sélection)
        // (nécessaire pour Chromium par exemple)
        .keyup(function ()
        {
            if (!$(this).val().trim().length) {
                var lastVal = $('#' + opts.elementDomId).val();
                $('#' + opts.elementDomId).val(null);
                $('#' + opts.elementDomId).trigger("change");
                if (null === lastVal) $('#' + opts.elementDomId).trigger("change");
            }
        })
        // ajoute de quoi faire afficher plus d'infos dans la liste de résultat de la recherche
        .data("ui-autocomplete")._renderItem = function (ul, item)
    {
        var template = item.template ? item.template : '<span id=\"{id}\">{label} <span class=\"extra\">{extra}</span></span>';
        var markup = template
            .replace('{id}', item.id ? item.id : '')
            .replace('{label}', item.label ? item.label : '')
            .replace('{extra}', item.extra ? item.extra : '');
        markup = '<a id="autocomplete-item-' + item.id + '">' + markup + "</a>";
        var li = $("<li></li>").data("item.autocomplete", item).append(markup).appendTo(ul);
        // mise en évidence du motif dans chaque résultat de recherche
        element.val().split(' ').filter(v => v).forEach(v => highlight(v, li, 'sas-highlight'));
        // si l'item ne possède pas d'id, on fait en sorte qu'il ne soit pas sélectionnable
        if (!item.id) {
            li.on("click", function () { return false; });
        }
        return li;
    };
    return this;
};



/**
 * Installation d'un mécanisme d'ouverture de fenêtre modale Bootstrap 3 lorsqu'un lien
 * ayant la classe CSS 'modal-action' est cliqué.
 * Et de gestion de la soumission du formulaire éventuel se trouvant dans la fenêtre modale.
 *
 * @param dialogDivId Id DOM éventuel de la div correspondant à la fenêtre modale
 */
function AjaxModalListener(dialogDivId)
{
    this.eventListener = $("body");
    this.modalContainerId = dialogDivId ? dialogDivId : "modal-div-gjksdgfkdjsgffsd";
    this.modalEventName = undefined;

    this.getModalDialog = function ()
    {
        var modal = $("#" + this.modalContainerId);
        if (!modal.length) {
            var modal =
                $('<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" />').append(
                    $('<div class="modal-dialog" />').append(
                        $('<div class="modal-content" />').append(
                            $('<div class="modal-body">Patientez, svp...<div>')
                        )
                    )
                );
            modal.attr('id', this.modalContainerId).appendTo("body").modal({show: false});
        }
        return modal;
    };
    this.extractNewModalContent = function (data)
    {
        var selector = '.modal-header, .modal-body, .modal-footer';
        // seuls les header, body et footer nous intéressent
        var newModalContent = $(data).filter(selector);
        if (!newModalContent.length) {
            newModalContent = $('<div class="modal-body" />');
        }
        // les var_dump, notice, warning, error PHP s'affichent n'importe où, on remet tout ça dans le body
        $(data).filter(':not(' + selector + ')').prependTo(newModalContent.filter(".modal-body"));
        // suppression de l'éventuel titre identique présent dans le body
        if (title = $(".modal-title", newModalContent).html()) {
            $(":header", newModalContent.filter(".modal-body")).filter(function () { return $(this).html() === title; }).remove();
        }
        return newModalContent;
    }
    this.getDialogBody = function ()
    {
        return $("div.modal-body", this.getModalDialog());
    };
    this.getDialogFooter = function ()
    {
        return $("div.modal-footer", this.getModalDialog());
    };
    this.getForm = function ()
    {
        return $("form", this.getDialogBody());
    };
    this.getSubmitButton = function ()
    {
        return $("#" + this.modalContainerId + " .btn-primary");
    };

    /**
     * Fonction lancée à l'ouverture de la fenêtre modale
     */
    this.modalShownListener = function (e)
    {
        // déplacement du bouton submit dans le footer
//        this.getSubmitButton().prependTo(this.getDialogFooter());
        // Réglage du focus sur le champ de formulaire ayant l'attribut 'autofocus'
        $('[autofocus]', e.target).focus();
    };

    /**
     * Interception des clics sur les liens adéquats pour affichage de la fenêtre modale
     */
    this.anchorClickListener = function (e)
    {
        var anchor = $(e.currentTarget);
        var url = anchor.attr('href');
        var modalDialog = this.getModalDialog();

        if (url && url !== "#") {
            $("body").addClass('loading');

            // transmet à la DIV le lien cliqué (car fournit l'événement à déclencher à la soumission du formulaire)
            modalDialog.data('a', anchor);
            this.modalEventName = anchor.data('event');

            // déclenchement d'un événement dont le nom est fourni par le lien cliqué (via un attribut "data-xxxx")
            var eventName = anchor.data('event-modal-loading');
            if (eventName) {
                var event = jQuery.Event(eventName, {div: modalDialog, a: modalDialog.data('a')});
                // console.log("Triggering '" + event.type + "' event...");
                // console.log("Event object : ", event);
                this.eventListener.trigger(event);
            }

            // requête AJAX pour obtenir le nouveau contenu de la fenêtre modale
            $.get(url, {modal: 1}, $.proxy(function (data)
            {
                // remplacement du contenu de la fenêtre modale
                $(".modal-content", modalDialog.modal('show')).html(this.extractNewModalContent(data));

                // déclenchement d'un événement dont le nom est fourni par le lien cliqué (via un attribut "data-xxxx")
                var eventName = anchor.data('event-modal-opened');
                if (eventName) {
                    var event = jQuery.Event(eventName, {div: modalDialog, a: modalDialog.data('a')});
                    console.log("Triggering '" + event.type + "' event...");
                    console.log("Event object : ", event);
                    this.eventListener.trigger(event);
                }

                $("body").removeClass('loading');
            }, this));
        }

        e.preventDefault();
    };

    /**
     * Interception des clics sur les liens inclus dans les modales pour rafraichir la modale au lieu de la page
     */
    this.innerAnchorClickListener = function (e)
    {
        if (IntraNavigator.embeds(e.currentTarget)) {
            return; // L'IntraNavigator se charge de tout, il n'y a rien à faire
        }

        var anchor = $(e.currentTarget);
        var url = anchor.attr('href');
        var modalDialog = this.getModalDialog();

        if (anchor.attr('target') === '_blank') {
            return;
        }

        if (url && url !== "#") {
            this.modalEventName = anchor.data('event');

            // requête AJAX pour obtenir le nouveau contenu de la fenêtre modale
            $.get(url, {modal: 1}, $.proxy(function (data)
            {
                // remplacement du contenu de la fenêtre modale
                $(".modal-content", modalDialog.modal('show')).html(this.extractNewModalContent(data));

            }, this));
        }

        e.preventDefault();
    };

    this.btnPrimaryClickListener = function (e)
    {
        var form = this.getForm();

        if (IntraNavigator.embeds(form)) {
            return; // L'IntraNavigator se charge de tout, il n'y a rien à faire
        }

        if (form.length) {
            form.submit();
            e.preventDefault();
        }
    };

    this.formSubmitListener = function (e)
    {
        if (IntraNavigator.embeds(e.target)) {
            return; // L'IntraNavigator se charge de tout, il n'y a rien à faire
        }

        var that = this;
        var modalDialog = this.getModalDialog();
        var dialogBody = this.getDialogBody().css('opacity', '0.5');
        var form = $(e.target);
        var postData = new FormData(form[0]);
        postData.append("modal", "1");

        var url = form.attr('action');
        if (!url) {
            throw new Error("Le formulaire dans la modale doit posséder un attribut 'action'");
        }
        var isRedirect = url.indexOf("redirect=") > -1 || $("input[name=redirect]").val();

        // requête AJAX de soumission du formulaire
        $.ajax({
            url : url,
            type : 'POST',
            data : postData,
            processData: false,  // tell jQuery not to process the data
            contentType: false,  // tell jQuery not to set contentType
            success : function(data) {
                // mise à jour du "content" de la fenêtre modale seulement
                $(".modal-content", modalDialog).html(that.extractNewModalContent(data));

                // tente de déterminer si le formulaire éventuel contient des erreurs de validation
                var terminated = !isRedirect && ($(".input-error, .has-error, .has-errors, .alert.alert-danger", modalDialog).length ? false : true);
                if (terminated) {
                    // recherche de l'id de l'événement à déclencher parmi les data du lien cliqué
                    //var modalEventName = modalDialog.data('a').data('event');
                    if (that.modalEventName) {
                        var args = that.getForm().serializeArray();
                        var event = jQuery.Event(that.modalEventName, {div: modalDialog, a: modalDialog.data('a')});
//                        console.log("Triggering '" + event.type + "' event...");
//                        console.log("Event object : ", event);
//                        console.log("Trigger args : ", args);
                        that.eventListener.trigger(event, [args]);
                    }
                }
                dialogBody.css('opacity', '1.0');
            }
        });

        e.preventDefault();
    };
}
/**
 * Instance unique.
 */
AjaxModalListener.singleton = null;
/**
 * Installation du mécanisme d'ouverture de fenêtre modale.
 */
AjaxModalListener.install = function (dialogDivId)
{
    if (null === AjaxModalListener.singleton) {
        AjaxModalListener.singleton = new AjaxModalListener(dialogDivId);
        AjaxModalListener.singleton.start();
    }

    return AjaxModalListener.singleton;
};
/**
 * Désinstallation du mécanisme d'ouverture de fenêtre modale.
 */
AjaxModalListener.uninstall = function ()
{
    if (null !== AjaxModalListener.singleton) {
        AjaxModalListener.singleton.stop();
    }

    return AjaxModalListener.singleton;
};
/**
 * Démarrage du mécanisme d'ouverture de fenêtre modale.
 */
AjaxModalListener.prototype.start = function ()
{
    // interception des clics sur les liens adéquats pour affichage de la fenêtre modale
    this.eventListener.on("click", "a.ajax-modal", $.proxy(this.anchorClickListener, this));

    // interception des clics sur les liens adéquats pour affichage de la fenêtre modale
    this.eventListener.on("click", "#" + this.modalContainerId + " a:not([download])", $.proxy(this.innerAnchorClickListener, this));

    // le formulaire éventuel est soumis lorsque le bouton principal de la fenêtre modale est cliqué
    if (this.getSubmitButton().length) {
        this.eventListener.on("click", this.getSubmitButton().selector, $.proxy(this.btnPrimaryClickListener, this));
    }

    // interception la soumission classique du formulaire pour le faire à la sauce AJAX
    this.eventListener.on("submit", "#" + this.modalContainerId + " form", $.proxy(this.formSubmitListener, this));

    // force le contenu de la fenêtre modale à être "recalculé" à chaque ouverture
    this.eventListener.on('hidden.bs.modal', "#" + this.modalContainerId, function (e)
    {
        $(e.target).removeData('bs.modal');
    });

    this.eventListener.on('shown.bs.modal', "#" + this.modalContainerId, $.proxy(this.modalShownListener, this));

    return this;
};
/**
 * Arrêt du mécanisme d'ouverture de fenêtre modale.
 */
AjaxModalListener.prototype.stop = function ()
{
    this.eventListener
        .off("click", "a.ajax-modal", $.proxy(this.anchorClickListener, this))
        .off("click", this.getSubmitButton().selector, $.proxy(this.btnPrimaryClickListener, this))
        .off("submit", "#" + this.modalContainerId + " form", $.proxy(this.formSubmitListener, this))
        .off('hidden.bs.modal', "#" + this.modalContainerId);

    return this;
};





/***************************************************************************************************************************************************
 Popover
 /***************************************************************************************************************************************************/

function ajaxPopoverInit()
{
    jQuery.fn.popover.Constructor.prototype.replace = function ()
    {
        var $tip = this.tip()

        var placement = typeof this.options.placement == 'function' ?
            this.options.placement.call(this, $tip[0], this.$element[0]) :
            this.options.placement

        var autoToken = /\s?auto?\s?/i
        placement = placement.replace(autoToken, '') || 'top'

        this.options.container ? $tip.appendTo(this.options.container) : $tip.insertAfter(this.$element)

        var pos = this.getPosition()
        var actualWidth = $tip[0].offsetWidth
        var actualHeight = $tip[0].offsetHeight

        var $parent = this.$element.parent()

        var orgPlacement = placement
        var docScroll = document.documentElement.scrollTop || document.body.scrollTop
        var parentWidth = this.options.container == 'body' ? window.innerWidth : $parent.outerWidth()
        var parentHeight = this.options.container == 'body' ? window.innerHeight : $parent.outerHeight()
        var parentLeft = this.options.container == 'body' ? 0 : $parent.offset().left

        placement = placement == 'bottom' && pos.top + pos.height + actualHeight - docScroll > parentHeight ? 'top' :
            placement == 'top' && pos.top - docScroll - actualHeight < 0 ? 'bottom' :
                placement == 'right' && pos.right + actualWidth > parentWidth ? 'left' :
                    placement == 'left' && pos.left - actualWidth < parentLeft ? 'right' :
                        placement

        $tip
            .removeClass(orgPlacement)
            .addClass(placement)

        var calculatedOffset = this.getCalculatedOffset(placement, pos, actualWidth, actualHeight)

        this.applyPlacement(calculatedOffset, placement)
    }

    $("body").popover({
        selector: 'a.ajax-popover',
        html: true,
        trigger: 'click',
        content: 'Chargement...',
    }).on('shown.bs.popover', ".ajax-popover", function (e)
    {
        var target = $(e.target);

        var content = $.ajax({
            url: target.attr('href'),
            async: false
        }).responseText;

        div = $("div.popover").last(); // Recherche la dernière division créée, qui est le conteneur du popover
        div.data('a', target); // On lui assigne le lien d'origine
        div.html(content);
        target.popover('replace'); // repositionne le popover en fonction de son redimentionnement
        div.find("form:not(.filter) :input:first").focus(); // donne le focus automatiquement au premier élément de formulaire trouvé qui n'est pas un filtre
    });

    $("body").on("click", "a.ajax-popover", function ()
    { // Désactive le changement de page lors du click
        return false;
    });

    $("body").on("click", "div.popover .fermer", function (e)
    { // Tout élément cliqué qui contient la classe .fermer ferme le popover
        div = $(e.target).parents('div.popover');
        if (div.hasClass('pop-ajax-div')) return;
        div.data('a').popover('hide');
    });

    $("body").on("submit", "div.popover div.popover-content form:not('.disable-ajax-submit')", function (e)
    {
        var form = $(e.target);
        var div = $(e.target).parents('div.popover');
        if (div.hasClass('pop-ajax-div')) return;
        $.post(
            form.attr('action'),
            form.serialize(),
            function (data)
            {
                div.html(data);

                var terminated = $(".input-error, .has-error, .has-errors, .alert", $(data)).length ? false : true;
                if (terminated) {
                    // recherche de l'id de l'événement à déclencher parmi les data de la DIV
                    var modalEventName = div.data('a').data('event');
                    var args = form.serializeArray();
                    var event = jQuery.Event(modalEventName, {a: div.data('a'), div: div});
                    $("body").trigger(event, [args]);
                }
            }
        );
        e.preventDefault();
    });
}




$.widget("unicaen.formAdvancedMultiCheckbox", {

    height: function (height)
    {
        if (height === undefined) {
            return this.getItemsDiv().css('max-height');
        } else {
            this.getItemsDiv().css('max-height', height);
        }
    },

    overflow: function (overflow)
    {
        if (overflow === undefined) {
            return this.getItemsDiv().css('overflow');
        } else {
            this.getItemsDiv().css('overflow', overflow);
        }
    },

    selectAll: function ()
    {
        this.getItems().prop("checked", true);
    },

    selectNone: function ()
    {
        this.getItems().prop("checked", false);
    },

    _create: function ()
    {
        var that = this;
        this.getSelectAllBtn().on('click', function () { that.selectAll(); });
        this.getSelectNoneBtn().on('click', function () { that.selectNone(); });
    },

    //@formatter:off
    getItemsDiv     : function() { return this.element.find('div#items');           },
    getItems        : function() { return this.element.find("input[type=checkbox]");},
    getSelectAllBtn : function() { return this.element.find("a.btn.select-all");    },
    getSelectNoneBtn: function() { return this.element.find("a.btn.select-none");   }
    //@formatter:on

});

$(function ()
{
    WidgetInitializer.add('form-advanced-multi-checkbox', 'formAdvancedMultiCheckbox');
});




/**
 * TabAjax
 */
$.widget("unicaen.tabAjax", {

    /**
     * Permet de retourner un onglet, y compris à partir de son ID
     *
     * @param string|a tab
     * @returns {*}
     */
    getTab: function (tab)
    {
        if (typeof tab === 'string') {
            return this.element.find('.nav-tabs a[aria-controls="' + tab + '"]');
        } else {
            return tab; // par défaut on présuppose que le lien "a" a été transmis!!
        }
    },

    getIsLoaded: function (tab)
    {
        tab = this.getTab(tab);
        return tab.data('is-loaded') == '1';
    },

    setIsLoaded: function (tab, isLoaded)
    {
        tab = this.getTab(tab);
        tab.data('is-loaded', isLoaded ? '1' : '0');

        this._trigger('loaded', null, tab);

        return this;
    },

    getForceRefresh: function (tab)
    {
        return this.getTab(tab).data('force-refresh') ? true : false;
    },

    setForceRefresh: function (tab, forceRefresh)
    {
        this.getTab(tab).data('force-refresh', forceRefresh);
        return this;
    },

    select: function (tab)
    {
        var that = this;

        tab = this.getTab(tab);
        if (tab.attr('href')[0] !== '#' && (!this.getIsLoaded(tab) || this.getForceRefresh(tab))) {
            var loadurl = tab.attr('href'),
                tid = tab.data('bs-target');

            that.element.find(".tab-pane" + tid).html("<div class=\"loading\">&nbsp;</div>");
            IntraNavigator.add(that.element.find(".tab-pane" + tid));
            $.get(loadurl, function (data)
            {
                that.element.find(".tab-pane" + tid).html(data);
                that.setIsLoaded(tab, true);
            });
        }
        tab.tab('show');
        this._trigger("change");
        return this;
    },

    getContent: function(tab)
    {
        var that = this;

        tab = this.getTab(tab);
        tid = tab.data('bs-target');
        return that.element.find(".tab-pane" + tid).html();
    },

    getSelected: function()
    {
        var sel = this.element.find('.tab-pane.active').attr('id');

        return sel;
    },

    _create: function ()
    {
        var that = this;
        this.element.find('.nav-tabs a').on('click', function (e)
        {
            e.preventDefault();
            that.select($(this));
            return false;
        });
        if (!that.getContent(that.getSelected())){
            that.select(that.getSelected());
        }
    },

});

$(function ()
{
    WidgetInitializer.add('tab-ajax', 'tabAjax');
});




$.widget("unicaen.popAjax", {

    popInstance: undefined,
    loading: true,
    ajaxLoaded: false,

    options: {
        url: undefined,
        title: undefined,
        content: undefined,
        confirm: false,
        confirmButton: '<i class="fas fa-check"></i> OK',
        cancelButton: '<i class="fas fa-xmark"></i> Annuler',
        submitEvent: undefined,
        submitClose: false,
        submitReload: false,
        forced: false,
        loadingTitle: 'Chargement...',
        loadingContent: '<div class="loading"></div>',
    },



    _create: function () {
        var that = this;

        that.loadOptions();

        /* Traitement des événements */
        if ('A' === this.element.prop("tagName")) {
            // On retire le comportement normal du click sur les ancres
            that.element.on("click", () => {
                return false;
            });
        }

        $('html').on("click", (e) => {
            // On détecte si on fait un clic ailleurs afin de fermer la pop-ajax
            that.htmlClick(e);
        });

        /* Préparation du popover bootstrap */
        popoptions = {
            html: true,
            sanitize: false,
            title: that.options.title ? that.options.title : that.options.loadingTitle,
            content: that.options.content ? that.options.content : that.options.loadingContent,
        };
        that.popInstance = new bootstrap.Popover(that.element, popoptions);

        that.element[0].addEventListener('show.bs.popover', () => {
            that.show(true);
        });

        that.element[0].addEventListener('inserted.bs.popover', () => {
            var pob = that.getPopoverElement().find('.popover-body');
            pob.addClass('intranavigator');
            pob.on('DOMSubtreeModified', () => {
                if (pob.find('.popover-title,.page-header').length > 0) {
                    that.setContent(pob.html());
                }
            });
            pob.on('intranavigator-refresh', (event, args) => {
                if (args.isSubmit) {
                    that.contentSubmit(pob);
                }
            });
        });

        that.element[0].addEventListener('hidden.bs.popover', () => {
            that.hide(true);
        });
    },



    loadOptions: function () {
        /* Traitement des options de configuration pour chargement */
        var optionsKeys = {
            url: 'url',
            content: 'content',
            title: 'title',
            confirm: 'confirm',
            confirmButton: 'confirm-button',
            cancelButton: 'cancel-button',
            submitEvent: 'submit-event',
            submitClose: 'submit-close',
            submitReload: 'submit-reload',
            forced: 'forced',
            loadingTitle: 'loading-title',
            loadingContent: 'loading-content',
        };

        for (var k in optionsKeys) {
            if (typeof this.element.data(optionsKeys[k]) !== 'undefined') {
                this.options[k] = this.element.data(optionsKeys[k]);
            }
        }
        if (this.options.title === undefined) {
            this.options.title = this.element.attr('title');
        }

        if ('A' === this.element.prop("tagName")) {
            this.options.url = this.element.attr('href');
        }
    },



    ajaxLoad: function () {
        var that = this;

        this.ajaxLoaded = true;
        this.setTitle(this.options.loadingTitle);
        this.setContent(this.options.loadingContent, true);
        $.ajax({
            url: this.options.url,
            success: (response) => {
                that.setContent(response);
                //that.contentSubmit(that.getPopoverElement().find('.popover-body'));
            }
        });
    },



    setContent: function (content, loading) {
        var ct = IntraNavigator.extractTitle(content);

        this.popInstance._config.content = ct.content;
        this.popInstance.setContent();

        if (ct.title) {
            this.setTitle(ct.title);
        }

        if (loading !== true) {
            this._trigger('change', null, this);
        }
    },



    getContent: function () {
        return this.popInstance._config.content;
    },



    setTitle: function (title) {
        this.options.title = title;
        this.popInstance._config.title = this.options.title;

        var poe = this.getPopoverElement();
        if (poe && poe.length == 1) {
            var titleElement = poe.find('.popover-header');
            if (titleElement && titleElement.length == 1) {
                titleElement.html(this.options.title);
            }
        }
    },



    getTitle: function () {
        return this.options.title;
    },



    show: function (shown) {
        var that = this;

        if ((this.options.forced || !this.ajaxLoaded) && this.options.url) {
            if (this.options.confirm) {
                this.setContent(this.makeConfirmBox());
            } else {
                this.loading = true;
                this.ajaxLoad();
            }
        }
        if (shown !== true) {
            this.popInstance.show();
        }
        this._trigger('show', null, this);
        setTimeout(() => {
            that.loading = false;
        }, 100);
    },



    hide: function (hidden) {
        if (hidden !== true) {
            this.popInstance.hide();
        }
        this.loading = true;
        this._trigger('hide', null, this);
    },



    shown: function () {
        return this.getPopoverElement() !== undefined;
    },



    hasErrors: function () {
        return IntraNavigator.hasErrors(this.getContent());
    },



    contentSubmit: function (element) {
        /* Gestion des événements lors de la submition d'un formulaire */
        if (IntraNavigator.hasErrors(element)) {
            this._trigger('error', null, this);
        } else {
            if (this.options.submitEvent) {
                $("body").trigger(this.options.submitEvent, this);
            }
            if (this.options.submitClose) {
                this.hide();
            }
            if (this.options.submitReload) {
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            }

            this._trigger('submit', null, this);
        }
    },



    makeConfirmBox: function () {
        var c = '<form action="' + this.options.url + '" method="post">' + this.options.content + '<div class="btn-goup" style="text-align:right;padding-top: 10px" role="group">';
        if (this.options.cancelButton) {
            c += '<button type="button" class="btn btn-secondary pop-ajax-hide">' + this.options.cancelButton + '</button>';
        }
        if (this.options.confirmButton && this.options.cancelButton) {
            c += '&nbsp;';
        }
        if (this.options.confirmButton) {
            c += '<button type="submit" class="btn btn-primary">' + this.options.confirmButton + '</button>';
        }
        c += '</div>' + '</form>';

        return c;
    },



    htmlClick: function (e) {
        var popEl = this.getPopoverElement();

        if (this.loading) return;
        if (!popEl || !popEl[0] || e.target == this.element[0]) return true;

        var p = popEl[0].getBoundingClientRect();
        var horsZonePop = e.clientX < p.left || e.clientX > p.left + p.width || e.clientY < p.top || e.clientY > p.top + p.height;
        var horsElementFils = $(e.target).parents('.popover-content,.ui-autocomplete').length == 0;

        if ($(e.target).hasClass('pop-ajax-hide')) {
            this.hide();
        }

        if (horsZonePop) {
            if (horsElementFils) { // il ne faut pas que l'élément soit dans le popover
                this.hide();
            }
        }
    },



    getPopoverElement: function () {
        var id = $(this.element).attr('aria-describedby');

        if (!id) {
            return undefined;
        }
        return $('#' + id);
    },
});



$(function () {
    WidgetInitializer.add('pop-ajax', 'popAjax');
});