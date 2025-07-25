// Widget permettant d'utiliser les macros dans des inputs tinymce
// forte dépendance avec select2
// généralement à coupler avec tinymce mais pas obligatoire
// usage :
// - donner à l'input la class .macro-compatible
// - à la fin du formulaire rajouter :
// <script>
// const macros = <?php echo $macrosJsonValue ?>;
// $(function () {
//         installMacrosWidgets(macros);
//     });
//</script>

/**
 * Parcours des textareas "macro compatibles" pour y installer le widget de recherche/insertion de macro.
 * @param macros array
 */
function installMacrosWidgets(macros)
{
    $(".macro-compatible").each(function (index, element) {
        const $textarea = $(element);
        // Pour éviter un chevauchement éventuelle d'input ayant la croix permettant de les vider
        $textarea.parent().find(".form-control-clear").detach();
        $textarea.parent().removeClass('has-clear');

        const id = 'macro-insert-widget-' + index;
        const $widget = $($("#macro-insert-widget-content").html()).attr('id', id);

        // insertion du widget
        $(this).before($widget);

        // installation du popover sur le bouton
        const $popoverContent = $widget.find(".macro-popover-content").attr('id', 'macro-popover-content-' + index);
        const $popoverBtn = $widget.find(".macro-btn").popover({
            html: true,
            sanitize: false,
            content: $popoverContent,
        });

        // installation du select2 dans le popover
        let $macroSelect2;
        const escapeHTML = function(html) {
            return document.createElement('div').appendChild(document.createTextNode(html)).parentNode.innerHTML;
        };
        const macroSelectTemplate = function (macro) {
            if (!macro) return "Aucune macro sélectionnée.";
            return '<strong class="macro-id">' + macro.text + '</strong> : ' + '<code class="macro-value">' + escapeHTML(macro.value) + '</code><br>' +
                '<div class="macro-description text-secondary">' + macro.description + '</div>';
        }
        try {
            $macroSelect2 = $widget.find(".macro-select").select2({
                data: macros,
                dropdownParent: $popoverContent,
                placeholder: "Sélectionnez une macro...",
                search: true,
                templateResult: macro => $(macroSelectTemplate(macro))
            });
        } catch (e) {
            alert("Impossible d'activer Select2 ! : " + e);
            console.error(e);
        }

        // bouton déclenchant l'insertion de la macro sélectionnée dans le textarea
        const $macroInsertBtn = $popoverContent.find(".macro-insert-btn").on("click", function () {
            // debugger;
            const macro = $macroSelect2.select2('data')[0];
            if (!macro) {
                return;
            }
            if (tinyMCE && ($ed = tinyMCE.get($textarea.prop('id')))) {
                $ed.selection.setContent(macro.value);
            } else {
                const caretPos = $textarea.prop('selectionStart');
                if (caretPos === undefined) {
                    $textarea.val($textarea.val() + ' ' + macro.value);
                } else {
                    $textarea.val($textarea.val().substring(0, caretPos) + macro.value + $textarea.val().substring(caretPos));
                }
            }
        });

        // bouton de fermeture du popover
        const $macroCancelBtn = $popoverContent.find(".macro-close-btn").on("click", function () {
            $popoverBtn.popover('hide');
        });

        // lorsqu'une macro est sélectionnée dans le select, affichage de ses détails
        $macroSelect2.on("select2:select", function (e) {
            const macro = e.params.data;
            $popoverContent.find(".macro-selection-placeholder").html(macroSelectTemplate(macro)).show();
            $macroInsertBtn.removeAttr("disabled");
        });
    });
}


/**
 * Parcours des textareas "template compatibles" pour y installer le widget de recherche/insertion de template.
 * @param templates array
 */
function installTemplatesWidgets(templates)
{
    $(".template-compatible").each(function (index, element) {
        const $textarea = $(element);
        // Pour éviter un chevauchement éventuelle d'input ayant la croix permettant de les vider
        $textarea.parent().find(".form-control-clear").detach();
        $textarea.parent().removeClass('has-clear');

        const id = 'template-insert-widget-' + index;
        const $widget = $($("#template-insert-widget-content").html()).attr('id', id);

        // insertion du widget
        $(this).before($widget);

        // installation du popover sur le bouton
        const $popoverContent = $widget.find(".template-popover-content").attr('id', 'template-popover-content-' + index);
        const $popoverBtn = $widget.find(".template-btn").popover({
            html: true,
            sanitize: false,
            content: $popoverContent,
        });

        // installation du select2 dans le popover
        let $templateSelect2;
        const escapeHTML = function(html) {
            return document.createElement('div').appendChild(document.createTextNode(html)).parentNode.innerHTML;
        };
        const templateSelectTemplate = function (template) {
            if (!template) return "Aucun template sélectionné.";
            return '<strong class="template-id">' + template.text + '</strong> : ' + '<code class="template-value">' + escapeHTML(template.value) + '</code><br>' +
                '<div class="template-description text-secondary">' + template.description + '</div>';
        }
        try {
            $templateSelect2 = $widget.find(".template-select").select2({
                data: templates,
                dropdownParent: $popoverContent,
                placeholder: "Sélectionnez un template...",
                search: true,
                templateResult: template => $(templateSelectTemplate(template))
            });
        } catch (e) {
            alert("Impossible d'activer Select2 ! : " + e);
            console.error(e);
        }

        // bouton déclenchant l'insertion de template sélectionné dans le textarea
        const $templateInsertBtn = $popoverContent.find(".template-insert-btn").on("click", function () {
            // debugger;
            const template = $templateSelect2.select2('data')[0];
            if (!template) {
                return;
            }
            if (tinyMCE && ($ed = tinyMCE.get($textarea.prop('id')))) {
                $ed.selection.setContent(template.value);
            } else {
                const caretPos = $textarea.prop('selectionStart');
                if (caretPos === undefined) {
                    $textarea.val($textarea.val() + ' ' + template.value);
                } else {
                    $textarea.val($textarea.val().substring(0, caretPos) + template.value + $textarea.val().substring(caretPos));
                }
            }
        });

        // bouton de fermeture du popover
        const $templateCancelBtn = $popoverContent.find(".template-close-btn").on("click", function () {
            $popoverBtn.popover('hide');
        });

        // lorsqu'une template est sélectionnée dans le select, affichage de ses détails
        $templateSelect2.on("select2:select", function (e) {
            const template = e.params.data;
            $popoverContent.find(".template-selection-placeholder").html(templateSelectTemplate(template)).show();
            $templateInsertBtn.removeAttr("disabled");
        });
    });
}