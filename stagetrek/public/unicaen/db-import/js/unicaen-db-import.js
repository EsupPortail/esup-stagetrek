/**
 * Implémentation du filtrage des <tr> selon le nom renseigné dans un champ texte "#filter".
 *
 * @param trSelector Sélecteur de <tr>, ex: "tr.import"
 */
function installFiltering(trSelector)
{
    let $filter = $("#filter");
    $filter.on('input', function () {
        filterTable();
    });
    filterTable();

    function filterTable() {
        let text = $filter.val();
        $("td.name").each(function () {
            let td = $(this);
            let tr = td.parent(trSelector);
            if (td.text().match(new RegExp(text, 'ig'))) {
                tr.show();
            } else {
                tr.hide();
            }
        });
    }
}

/**
 * Implémentation du cochage multiple et de la màj du "href" du lien de lancement.
 */
function installLancementMultiple()
{
    let btn = $("#a-lancer-multiple"),
        checkall = $(".checkall"),
        checkboxes = $(".checkbox"),
        urlPattern = btn.prop("href");
    checkboxes.on('change', function () {
        updateBtn();
    })
    checkall.on('change', function () {
        $(".checkbox:visible").prop("checked", $(this).is(":checked"));
        updateBtn();
    });
    updateBtn();

    function generateNames($checkboxes) {
        let names = [];
        $checkboxes.each(function () {
            names.push($(this).data('name'));
        });
        return encodeURI(names.join(","));
    }

    function updateBtn() {
        let $checkboxes = $(".checkbox:visible:checked");
        if ($checkboxes.length > 0) {
            btn.removeClass('disabled');
        } else {
            btn.addClass('disabled');
        }
        if(urlPattern  === undefined){return;}
        btn.prop("href", urlPattern.replace('__names__', generateNames($checkboxes)));
    }
}