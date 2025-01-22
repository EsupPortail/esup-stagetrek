//Note : application de tinymce sur les class type1 et type2 (utiliser dans certaines libs tels que mail)
$(function () {
    tinymce.init({
        selector: '.type1',
        toolbar: 'newdocument undo redo | bold italic | bullist | bullist | highlight',
        language: 'fr_FR',
        entity_encoding : "raw",
        resize: true,
        statusbar: true,
        browser_spellcheck : true,
        branding: false,
        menu: {},
        setup: function (editor) {
            editor.addButton('highlight', {
                text: '',
                icon: 'codesample',
                title: 'Définition',
                onclick: function () {
                    let content =  editor.selection.getContent();
                    if (content !== '') {
                        let new_content = "";
                        let regex = /<abbr title="">.*<\/abbr>/g;
                        let found = content.match(regex);
                        if (found) {
                            new_content = content.replace('</abbr>', '');
                            new_content = new_content.replace('<abbr title="">', '');
                        } else {
                            new_content = '<abbr title="">' + content + '</abbr>';
                        }
                        editor.insertContent(new_content);
                    }
                }
            });
        }
    });
    tinymce.init({
        selector: '.type2',
        plugins: 'lists',
        toolbar: 'newdocument undo redo | bold italic | bullist',
        browser_spellcheck : true,
        language: 'fr_FR',
        entity_encoding : "raw",
        statusbar: false,
        resize: false,
        branding: false,
        menu: {},
        setup: function (editor) {}
    });

    $('.info-icon').popover({
        html: true
    })

});