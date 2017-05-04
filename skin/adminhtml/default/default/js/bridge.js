jQuery(document).ready(function ($) {
    $('#import_table.category input[type=checkbox]').on('click', function () {
        var base    = $(this).data('base'),
            current = $(this).val(),
            checked = $(this).is(':checked');
        if (current == 1) {
            $("#import_table.category input[type=checkbox]").each(function() {
                jQuery(this).prop('checked', checked);
            });
        } else {
            markChildrenForCurrent(base, checked);
        }
    });

    $('.form-check-button.manufacturer button').on('click', function(){
        var checked = $('#import_table.manufacturer input[type=checkbox]').is(':checked');
        $('#import_table.manufacturer input[type=checkbox]').prop('checked', !checked);
    });

    $('.form-check-button.category button').on('click', function(){
        var checked = $('#import_table.category input[type=checkbox]').is(':checked');
        $('#import_table.category input[type=checkbox]').prop('checked', !checked);
    });
});

function markChildrenForCurrent(base, checked) {
    jQuery("#import_table.category input[type=checkbox]").each(function() {
        if (jQuery(this).data('parent') == base) {
            jQuery(this).prop('checked', checked);
        }
    });
}
