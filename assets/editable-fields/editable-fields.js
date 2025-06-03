$(document).ready(function () {
    initEditableFields();
});

$(document).on("pjax:complete", "#pjax", function () {
    initEditableFields();
});

function initEditableFields() {
    let tables = document.querySelectorAll("table.editable-fields");
    tables.forEach((table) => {
        initEditableEvents("#"+table.id);
    });
}

let editableFieldsDebounceTimer = null;
let currentEditingRow = null;

function initSelect2(selector) {
    $(selector).select2({ width: '100%', dropdownAutoWidth : true }).on("select2:select", updateRow);
}

function parseRuDate(value, date = new Date()) {
    let dateParts = value.split('.');
    for(let index in dateParts) {
        if(+index === 0) {
            date.setDate(+dateParts[index]);
        }
        if(+index === 1) {
            if((0 < +dateParts[index]) && (+dateParts[index] <= 12)) {
                date.setMonth(+dateParts[index]-1);
            }
        }
        if(+index === 2) {
            if(+dateParts[index] > 0) {
                date.setFullYear(+dateParts[index]);
            }
        }
    }
    return date;
}

function initAirdate(selector) {

    document.querySelectorAll(selector).forEach((element) => {
        let raw = element.dataset.airConfig || '{}';
        let cfg = {};
        cfg = JSON.parse(`{${raw}}`);

        cfg.onSelect = function ({ date, formattedDate, datepicker }) {
            $(datepicker.$el).change();
            updateRow({ target: datepicker.$el });
        };

        let date = new AirDatepicker(element, cfg);
        element.addEventListener('input', (event) => {
            let newDate = new Date();
            if(date.focusDate) {
                newDate.setDate(date.focusDate.getDay());
                newDate.setMonth(date.focusDate.getMonth());
                newDate.setFullYear(date.focusDate.getFullYear());
            }
            let currentDate = parseRuDate(event.target.value, newDate);
            date.setFocusDate(currentDate, {viewDateTransition: true});
        })
        element.addEventListener('keyup', (event) => {
            if (event.keyCode === 13 && date.focusDate) {
                date.selectDate(date.focusDate);
            }
        })
    })
}

function initEditableEvents(selector) {
    initSelect2(selector+" .select2");
    initAirdate(selector+' .air-date');

    document.querySelectorAll(selector + ' tr[data-key="0"]').forEach((tr) => {
        tr.style.display = "none";
    })

    document.querySelectorAll(".btn-row-add").forEach((element) => {
        element.addEventListener("click", addRow)
    })

    document.querySelectorAll(selector + " .btn-row-remove").forEach((element) => {
        element.addEventListener("click", removeRow)
    })

    document.querySelectorAll(selector + " input").forEach((element) => {
        element.addEventListener("change", updateRow)
    })

    document.querySelectorAll(selector + " select").forEach((element) => {
        element.addEventListener("change", updateRow)
    })

    document.querySelectorAll(selector + " input").forEach((element) => {
        element.addEventListener("input", startEditRow)
    })

    document.querySelectorAll(selector + " input").forEach((element) => {
        element.addEventListener("focusin", checkEndEditRow)
    })
}

function sendEditableFieldAjax(url, data, pjaxId = null, pjaxUrl = null) {
    let options = {
        container: '#pjax',
        async: false,
        replace: false
    };
    if (pjaxId != null) {
        options.container = '#'+pjaxId;
    }
    if (pjaxUrl != null){
        options.url = pjaxUrl;
    }
    $.ajax({
        url: '/' + url,
        dataType: 'json',
        data: data,
        method: 'POST',
        success: function(data) {
            if (data.result) {
                $.pjax.reload(options);
            }
        }
    });
}

function addRow(event) {
    event.preventDefault();
    let selector = event.target.closest('.btn-row-add').dataset.for ?? '';
    if(selector) {
        selector = "#" + selector + " ";
    }
    document.querySelectorAll(selector + 'tr[data-key="0"]').forEach((el) => {
        el.style.display = 'table-row';
    })
}

function startEditRow(event) {
    let tr = event.target.closest('tr');
    if(currentEditingRow && editableFieldsDebounceTimer) {
        if(tr.dataset.key === currentEditingRow.dataset.key) {
            clearTimeout(editableFieldsDebounceTimer);
        }
    }

}

function checkEndEditRow(event) {
    let tr = event.target.closest('tr');
    if(currentEditingRow && editableFieldsDebounceTimer) {
        if(tr.dataset.key !== currentEditingRow.dataset.key) {
            clearTimeout(editableFieldsDebounceTimer);
            sendRowData(currentEditingRow)
        }
    }
}

function updateRow(event) {
    let tr = event.target.closest('tr');
    if(event.target.hasAttribute('name')) {
        if(currentEditingRow && editableFieldsDebounceTimer && currentEditingRow.dataset.key === tr.dataset.key) {
            clearTimeout(editableFieldsDebounceTimer);
        }
        currentEditingRow = tr;
        editableFieldsDebounceTimer = setTimeout(() => {
            sendRowData(tr);
        }, 1000)
        return;
    }
    if(event.target.classList.contains('linked-list')) {
        let linkedChildName = event.target.dataset.linkedChild ?? false;
        if(linkedChildName) {
            let linkedChild = tr.querySelector('[data-linked-id="'+linkedChildName+'"]');
            if(linkedChild && linkedChild.tagName === 'SELECT') {
                let label = event.target.selectedOptions[0].label ?? false;
                if(linkedChild.classList.contains('select2')) {
                    $(linkedChild).select2({
                        matcher: function (term, option) {
                            if(option.element.tagName !== 'OPTGROUP') {
                                return option;
                            }
                            if(event.target.value) {
                                return (option.element.label === label) ? option : '';
                            } else {
                                return option;
                            }
                        }
                    });
                    // $(linkedChild).trigger('change');
                } else {
                    linkedChild.querySelectorAll('optgroup').forEach((element) => {
                        if(event.target.value) {
                            if(label === element.label) {
                                element.style.display = 'block';
                                // element.removeAttribute('hidden');
                            } else {
                                element.style.display = 'none';
                                // element.setAttribute('hidden', '1');
                            }
                        } else {
                            element.style.display = 'block';
                        }
                    });
                }

            }
        }
    }
}

function sendRowData(tr) {
    let inputs = tr.querySelectorAll('*[name]')

    let data = {};
    inputs.forEach((element) => {
        if(element.type === 'checkbox') {
            data[element.name] = element.checked ? 1 : 0;
        } else if(element.type === 'text' && element.classList.contains('air-date')) {
            data[element.name] = parseRuDate(element.value).toISOString().split('T')[0];
        } else {
            data[element.name] = element.value;
        }
    })
    let action = (+tr.dataset.key === 0) ? '/create' : '/edit?id='+tr.dataset.key;
    let pjax = document.getElementById(tr.closest('table')?.dataset.pjaxId);

    sendEditableFieldAjax( tr.closest('table').dataset.base_url + action, data, pjax?.id, pjax?.dataset.url);
    currentEditingRow = null;
    editableFieldsDebounceTimer = null;
}

function removeRow(event) {
    event.cancelBubble = true;
    event.stopPropagation();
    event.preventDefault();
    if (!confirm('Удалить объект?')) return;
    let pjax = document.getElementById(event.target.closest('table')?.dataset.pjaxId);
    sendEditableFieldAjax(event.target.closest('table').dataset.base_url + '/delete?id='+event.target.closest('tr').dataset.key, {
        id: $(this).data('id'),
    }, pjax?.id, pjax?.dataset.url);
    $(this).closest('tr').remove();
}