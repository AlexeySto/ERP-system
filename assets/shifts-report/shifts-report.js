jQuery(document).ready(function () {
    shiftsEditAssignActions('#shifts');
});

function shiftsEditAssignActions(selector) {
    jQuery(selector + ' .numeric0').numeric({ decimal: false, negative: true, decimalPlaces : 0 });
    jQuery(selector + ' .numeric0').keyup(shiftsReportDoneChanged).change(shiftsReportDoneChanged);
    jQuery(selector + ' .row_done').change(shiftsReportDoneChanged);
}

function shiftsReportDoneChanged() {
    var row = jQuery(this).closest('tr');
    var tarif = getFloat(row.find('.row_tarif').html());
    var q = getFloat(row.find('.row_done').val());
    var salary = tarif * q;
    row.find('span.row_salary').html(Math.round(salary));
    row.find('input.row_salary').val(Math.round(salary));
    shiftsReportDoneTotal();
}

function shiftsReportDoneTotal() {
    var total = 0;
    jQuery('#shifts tr').each(function (i, o) {
        var salary = getInt(jQuery(o).find('input.row_salary').val());
        total += salary;
    });
    jQuery('.total_salary').html(total);
}
