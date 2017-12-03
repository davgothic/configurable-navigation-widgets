jQuery(function($) {

    function select2Template(icon) {
        return $(
            '<span><i class="fa ' + icon.id + '"></i>&nbsp;&nbsp;&nbsp;' + icon.text + '</span>'
        );
    }

    function initIconPicker() {
        $('.cnw-icon-picker').select2({
            width: "100%",
            templateResult: select2Template,
            templateSelection: select2Template
        });
    }

    initIconPicker();

    jQuery(document).ajaxSuccess(function (e, xhr, settings) {
        var widget_id_base = 'current_page';

        if (settings.data.search('action=save-widget') != -1 && settings.data.search('id_base=' + widget_id_base) != -1) {
            initIconPicker();
        }
    });




});