jQuery(document).ready(function () {
    jQuery('#product-edit').submit(productEditSubmit);
    jQuery('.btn-add-row').click(productEditAddRow);
    jQuery('.btn-add-row-piece').click(productEditAddRowPiece);
    jQuery('.btn-add-row-cons').click(productEditAddRowCons);
    productEditAssignActions('#product-works');
    productEditAssignPieceActions('#product-work-pieces');
    productEditAssignConsActions('#product-consumables');
});

function productEditValidate(o) {
    var obj = jQuery(o);
    var tagName = o.tagName.toUpperCase();
    var tagType = obj.attr('type');
    var isOk = false;
    if (tagName == 'SELECT' || (tagType && tagType.toUpperCase() == 'NUMBER')) {
        console.log(tagName, isOk);
        isOk = getFloat(obj.val()) > 0;
    } else {
        isOk = obj.val().length > 0;
    }
    if (!isOk) obj.addClass('has-error');
    else obj.removeClass('has-error');
}
function productEditValidateAll() {
    jQuery('#product-works input').each(function (i, o) {
        productEditValidate(o);
    });
    jQuery('#product-work-pieces input, #product-work-pieces select').each(function (i, o) {
        productEditValidate(o);
    });
    jQuery('#product-consumables input, #product-consumables select').each(function (i, o) {
        productEditValidate(o);
    });
}
function productEditSubmit(e) {
    productEditValidateAll();
    if (jQuery('#product-edit .required.has-error').length === 0) {
        return true;
    }
    if (e) {
        e.cancelBubble = true;
        e.stopPropagation();
        e.preventDefault();
    }
    return false;
}

function productEditAddRow(e) {
    if (e) {
        e.cancelBubble = true;
        e.stopPropagation();
        e.preventDefault();
    }

    var id = getInt(jQuery('.btn-add-row').attr('data-id'));
    var tid = 't' + id;
    var html = '<tr id="product-work-' + tid + '">' + jQuery('#template tr').html() + '</tr>';
    html = html.replace(/\[xxx\]/g, '[' + tid + ']');
    id++;
    jQuery('.btn-add-row').attr('data-id', id);

    jQuery('#product-works tbody').append(html);
    productEditAssignActions('#product-work-' + tid);
}

function productEditAddRowPiece(e) {
    if (e) {
        e.cancelBubble = true;
        e.stopPropagation();
        e.preventDefault();
    }

    var id = getInt(jQuery('.btn-add-row-piece').attr('data-id'));
    var tid = 't' + id;
    var html = '<tr id="product-work-piece-' + tid + '">' + jQuery('#template-work-piece tr').html() + '</tr>';
    html = html.replace(/\[xxx\]/g, '[' + tid + ']');
    id++;
    jQuery('.btn-add-row-piece').attr('data-id', id);

    jQuery('#product-work-pieces tbody').append(html);
    productEditAssignPieceActions('#product-work-piece-' + tid);
}

function productEditAddRowCons(e) {
    if (e) {
        e.cancelBubble = true;
        e.stopPropagation();
        e.preventDefault();
    }

    var id = getInt(jQuery('.btn-add-row-cons').attr('data-id'));
    var tid = 't' + id;
    var html = '<tr id="product-consumable-' + tid + '">' + jQuery('#template-consumable tr').html() + '</tr>';
    html = html.replace(/\[xxx\]/g, '[' + tid + ']');
    id++;
    jQuery('.btn-add-row-cons').attr('data-id', id);

    jQuery('#product-consumables tbody').append(html);
    productEditAssignPieceActions('#product-consumable-' + tid);
}

function productEditAssignActions(selector) {
    jQuery(selector + ' .btn-delete-row').click(productEditRemove);
    jQuery(selector + ' .numeric0').numeric({ decimal: false, negative: true, decimalPlaces : 0 });
    jQuery(selector + ' .row_norm').keyup(productEditNormChanged).change(productEditNormChanged);
    jQuery(selector + ' .row_tarif').keyup(productEditNormChanged).change(productEditNormChanged);
    jQuery(selector + ' .row_dec_numbers').keyup(productEditDecNumberChanged).change(productEditDecNumberChanged);
    jQuery(selector + ' .row_worktype').change(productEditWorktypeChanged);

    jQuery(selector + ' .row_work_name').autocomplete({
        source: function( request, response ) {
            $.ajax( {
                url: URL_PROD_WORKS,
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function( data ) {
                    response( data );
                }
            } );
        },
        minLength: 2,
        select: function( event, ui ) {
            var target = jQuery(event.target);
            var tr = target.closest('tr');
            target.val(ui.item.value);
            tr.find('.row_tarif').val(ui.item.tarif).change();
            tr.find('.row_dec_numbers').val(ui.item.dec_numbers).change();
        }
    } );
    jQuery(selector + ' input').blur(function () { productEditValidate(this); });
}

function productEditAssignConsActions(selector) {
    jQuery(selector + ' .btn-delete-row').click(productEditRemove);
    jQuery(selector + ' .row_item_id').change(productEditConsChanged);
}

function productEditAssignPieceActions(selector) {
    jQuery(selector + ' .btn-delete-row').click(productEditRemove);
    jQuery(selector + ' .row_item_id').change(productEditWorkpieceChanged);
}

function productEditRemove() {
    if(!confirm('Удалить строку?')) return false;
    jQuery(this).closest('tr').remove();
    // shiftsEditPlanChanged();
}

function productEditDecNumberChanged() {
    var row = jQuery(this).closest('tr');
    var dec = getInt(jQuery(this).val());
    console.log(dec);
    row.find('.row_norm').attr('step', (1 / Math.pow(10, dec)));
}

function productEditNormChanged() {
    var row = jQuery(this).closest('tr');
    var tarif = getFloat(row.find('input.row_tarif').val());
    var q = getFloat(row.find('.row_norm').val());
    var salary = tarif * q;
    row.find('span.row_salary').html(Math.round(salary));
    row.find('input.row_salary').val(Math.round(salary));
    shiftsEditPlanTotal();
}

function shiftsEditPlanTotal() {
    var total = 0;
    jQuery('#product-works tr').each(function (i, o) {
        var salary = getInt(jQuery(o).find('input.row_salary').val());
        total += salary;
    });
    jQuery('.total_salary').html(total);
}

function productEditWorktypeChanged() {
    var row = jQuery(this).closest('tr');
    var tarif = jQuery(this).find('option:selected').data('tarif');
    var step = jQuery(this).find('option:selected').data('step');
    row.find('span.row_tarif').html(tarif);
    row.find('input.row_tarif').val(tarif);
    row.find('.row_norm').attr('step', step);
    row.find('.row_norm').change();
}

function productEditWorkpieceChanged() {
    var row = jQuery(this).closest('tr');
    var step = jQuery(this).find('option:selected').data('step');
    row.find('.row_q').attr('step', step);
    row.find('.row_q').change();
}

function productEditConsChanged() {
    var row = jQuery(this).closest('tr');
    var step = jQuery(this).find('option:selected').data('step');
    row.find('.row_q').attr('step', step);
    row.find('.row_q').change();
}
