jQuery(document).ready(function () {
    jQuery('#tarifs-form').submit(shiftsEditSubmit);
    jQuery('.btn-add-row').click(shiftsEditAddRow);
    shiftsEditAssignActions('#shifts');
});

function shiftsEditValidate(o) {
    var obj = jQuery(o);
    var value = getFloat(obj.val());
    if (value === 0) obj.addClass('has-error');
    else obj.removeClass('has-error');
}
function shiftsEditValidateAll() {
    jQuery('#shifts select, #shifts input[type=number]').each(function (i, o) {
        shiftsEditValidate(o);
    });
}
function shiftsEditSubmit(e) {
    shiftsEditValidateAll();
    if (jQuery('#shifts .required.has-error').length === 0) {
        return true;
    }
    if (e) {
        e.cancelBubble = true;
        e.stopPropagation();
        e.preventDefault();
    }
    return false;
}

function shiftsEditAddRow() {
    var id = getInt(jQuery('.btn-add-row').attr('data-id'));
    var tid = 't' + id;
    var html = '<tr id="shift-row-' + tid + '">' + jQuery('#template tr').html() + '</tr>';
    html = html.replace(/\[xxx\]/g, '[' + tid + ']');
    id++;
    jQuery('.btn-add-row').attr('data-id', id);

    jQuery('#shifts tbody').append(html);
    shiftsEditAssignActions('#shift-row-' + tid);
}

function shiftsEditAssignActions(selector) {
    jQuery(selector + ' .btn-delete-row').click(shiftsEditRemove);
    jQuery(selector + ' .numeric0').numeric({ decimal: false, negative: true, decimalPlaces : 0 });
    jQuery(selector + ' .numeric0').keyup(shiftsEditPlanChanged).change(shiftsEditPlanChanged);
    jQuery(selector + ' .row_order_work').change(shiftsEditWorktypeChanged);
    jQuery(selector + ' .row_plan').change(shiftsEditPlanChanged);
    jQuery(selector + ' .row_order').change(shiftsEditOrderChanged);
    jQuery(selector + ' select, ' + selector + ' input[type=number]').blur(function () { shiftsEditValidate(this); });
}

function shiftsEditRemove() {
    if(!confirm('Удалить строку?')) return false;
    jQuery(this).closest('tr').remove();
    shiftsEditPlanChanged();
}

function shiftsEditPlanChanged() {
    var row = jQuery(this).closest('tr');
    var tarif = getFloat(row.find('input.row_tarif').val());
    var q = getFloat(row.find('.row_plan').val());
    var salary = tarif * q;
    row.find('span.row_salary_plan').html(Math.round(salary));
    row.find('input.row_salary_plan').val(Math.round(salary));
    shiftsEditPlanTotal();
}

function shiftsEditPlanTotal() {
    var total = 0;
    jQuery('#shifts tr').each(function (i, o) {
        var salary = getInt(jQuery(o).find('input.row_salary_plan').val());
        total += salary;
    });
    jQuery('.total_salary_plan').html(total);
}

function shiftsEditWorktypeChanged() {
    var row = jQuery(this).closest('tr');
    var option = jQuery(this).find('option:selected');
    var tarif = option.data('tarif');
    var step = option.data('step');
    var left = option.data('left');
    row.find('span.row_tarif').html(tarif);
    row.find('span.row_left').html(left);
    row.find('input.row_tarif').val(tarif);
    row.find('.row_plan').attr('step', step);
    // row.find('.row_plan').attr('max', left);
    row.find('.row_plan').change();
    shiftsEditValidateAll();
}

function shiftsEditOrderChanged() {
    var row = jQuery(this).closest('tr');
    row.find('.row_order_work option').remove();
    var select = row.find('.row_order_work');
    select.append('<option value="0">---</option>');

    var order = getInt(jQuery(this).val());
    $.ajax({
        url: URL_SHIFT_ORDER_WORKS,
        data: {order: order},
        dataType: 'json',
        method: 'get',
        success: function (json) {
            if (!Array.isArray(json)) return;
            for (i in json) {
                var item = json[i];
                select.append('<option value="' + item.id + '" data-tarif="' + item.tarif + '" data-step="' + item.step + '" data-left="' + item.left + '">' + item.name + '</option>');
            }
            select.change();
        },
        error: function (a) {
            console.log('shiftsEditOrderChanged: ' + a.responseText);
        }
    });

    shiftsEditOrderComments();
}

function shiftsEditOrderComments() {
    var comments = {};
    $('#shifts .row_order option:selected').each(function (i, o) {
        var option = $(o);
        var id = getInt(option.val());
        var name = option.html();
        var comment = option.data('comment');
        if (!name || !comment) return;
        var html = '<p><b>' + name + '</b> - ' + comment + '</p>';
        comments[id] = html;
    })
    var html = '';
    for (var id in comments) {
        html += comments[id];
    }
    $('#comments').html(html);
}
