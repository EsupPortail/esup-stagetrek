

$(function () {
    $('[data-bs-toggle="popover"]').popover({html: true, sanitize: false});
    $('[data-bs-toggle="tooltip"]').tooltip({});
});


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


function closeModal(event){
    var target = $(event.target);
    target.find('#flash-message').refresh();
    event.div.modal('hide');
}

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


