
var filterDatePicker = null;
var openedDatePicker = null;
if ($('.btn-select-period-month').length > 0) {
    $('.btn-select-period-month').click(function(e) { filterDatePicker.show(); e.preventDefault(); e.stopImmediatePropagation(); return false; });
    $('body *').click(function() { if (openedDatePicker != null) openedDatePicker.hide(); });
    const input = $('.btn-select-period-month').find('input')[0];
    filterDatePicker = new AirDatepicker(input, {
        showEvent: 'click',
        inline: false,
        autoClose: true,
        timepicker: false,
        selectedDates: [$(input).val()],
        dateFormat: 'yyyy-MM',
        range: false,
        view: 'months',
        minView: 'months',
        onShow(isFinished) { openedDatePicker = filterDatePicker; },
        onHide(isFinished) { openedDatePicker = null; },
        onSelect(dp) {
            if (dp.formattedDate === undefined) {
                dp.datepicker.hide();
                return;
            }
            redirectToPeriod(dp.formattedDate);
        },
        buttons: [
            {
                content(dp) { return 'Этот месяц' },
                onClick(dp) { redirectToPeriod((new moment()).format('YYYY-MM')); dp.hide(); }
            }
        ],
        position({$datepicker, $target, $pointer}) { calculateCalendarPosition($('.btn-select-period-month')[0], $datepicker, $pointer); }
    });
}
function calculateCalendarPosition($anchor, $datepicker, $pointer) {
    let coords = $anchor.getBoundingClientRect(),
        dpHeight = $datepicker.clientHeight,
        dpWidth = $datepicker.clientWidth;

    let top = coords.y + window.scrollY;
    let left = coords.x + coords.width / 2 - dpWidth / 2;
    if (left < 50) left = 50;

    $datepicker.style.left = `${left}px`;
    $datepicker.style.top = `${top}px`;
    $pointer.style.display = 'none';
}

function redirectToPeriod(period, field) {
    if (!field) field = 'period';
    var urlParts = window.location.href.replace('?', '&').split('&');
    var mainUrl = urlParts.shift();
    urlParts = replaceUrlPart(urlParts, field, period);
    window.location.href = mainUrl + '?' + urlParts.join('&');
}
function replaceUrlPart(urlParts, name, value) {
    var key = name + '=';
    var found = false;
    for(i in urlParts) {
        var part = urlParts[i];
        if (part.length > key.length && part.substr(0, key.length) === key) {
            urlParts[i] = key + value;
            found = true;
        }
    }
    if (!found) {
        urlParts.push(key + value);
    }
    return urlParts;
}