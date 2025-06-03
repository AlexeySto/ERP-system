jQuery('form.disable-double-sending').find('button[type="submit"]').click(preventDoubleSendingCheck);
function preventDoubleSendingCheck(e) {
    $('button[type="submit"]').addClass('disabled');
    setTimeout(enableButton, 5000);
}

function enableButton() {
    $('.btn-success').removeClass('disabled');
}