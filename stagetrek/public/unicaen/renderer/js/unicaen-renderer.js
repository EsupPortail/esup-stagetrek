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
        const macroSelectTemplate = function (macro) {
            if (!macro) return "Aucune macro sélectionnée.";
            return '<strong class="macro-id">' + macro.text + '</strong> : ' + '<code class="macro-value">' + macro.value + '</code><br>' +
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