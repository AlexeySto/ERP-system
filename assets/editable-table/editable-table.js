let moduleName = $('#editable-table').data('module-name');
let moduleSelectAttributes = $('#editable-table').data('module-select-attributes').split(" ");
let moduleInputAttributes = $('#editable-table').data('module-input-attributes').split(" ");
let searchForm = $('#editable-table-search');

$(document).ready(function () {
    init();
});

$(document).on('pjax:complete', '#pjax', function (xhr, textStatus, options) {
    init();
});

function init() {
    $('.add_row').click(function (e) {
        e.cancelBubble = true;
        e.stopPropagation();
        e.preventDefault();

        createReward();
    });

    moduleSelectAttributes.forEach((item) => {
        if (item) {
            initSelect2('#editable-table .' + item);
        }
    })

    //initSelect2('#edit-reward .salary_operation_type_id');
    rewardInitEvents();
}

function initSelect2(selector) {
    $(selector).select2().on('select2:select', updateReward);
}

function rewardInitEvents(selector) {
    $('#editable-table .reward-remove').click(rewardItemRemove);
    $('#editable-table input').change(updateReward);
    $('#editable-table-search input').change(sendSearchForm)
    $('#editable-table-search select').change(sendSearchForm)
}

function sendAjax(method, data) {
    let url = method;

    if (method === 'delete') {
        url = url + '/' + data.reward_id;
        data = {};
    }

    $.ajax({
        url: '/' + moduleName + '/' + url,
        dataType: 'json',
        data: data,
        method: 'POST',
        success: function (result) {
            if (result.success && result.reward_id) {
                $.pjax.reload({container: '#pjax', async: false});
            }
        }
    });
}

function sendSearchForm() {
    searchForm.submit();
}

function createReward() {
    sendAjax('update', {create: 'create'});
}

function updateReward(element) {
    if ($(element.target).attr('data-user-id') !== undefined) {
        updateWRC(element)
        return;
    }

    let dataElement = $(element.target).parents('tr');
    let property = {};

    property['id'] = dataElement.data('key');

    moduleSelectAttributes.forEach((item) => {
        if (item){
            property[item] = dataElement.find('.' + item).val();
        }
    });

    moduleInputAttributes.forEach((item) => {
        if (item){
            property[item] = dataElement.find('.' + item).val();
        }

    });

    console.log(property)

    sendAjax('update', property);
}

function updateWRC(element) {
    let property = {};
    property['user_id'] = $(element.target).attr('data-user-id');
    property['production_stage_id'] = $(element.target).attr('data-production-stage-id');
    property['date'] = $(element.target).attr('data-date');
    property['type'] = $(element.target).attr('data-type');
    property['value'] = $(element.target).val();

    sendAjax('update', property);
}

function rewardItemRemove(e) {
    e.cancelBubble = true;
    e.stopPropagation();
    e.preventDefault();

    if (!confirm('Удалить объект ?')) return;

    console.log($(this).data('id'));

    sendAjax('delete', {
        reward_id: $(this).data('id'),
    });

    $(this).closest('.reward-item').remove();
}