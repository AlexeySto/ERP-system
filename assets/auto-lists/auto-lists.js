$(document).ready(function () {
    initAutoLists();
});

$(document).on('pjax:complete', '#pjax', function (xhr, textStatus, options) {
    initAutoLists();
});

let debounceTimer = null;

function initAutoLists() {
    document.querySelectorAll('.table-responsive').forEach((el) => {
        el.style.overflowX = 'visible';
    })
    let lists = document.querySelectorAll('input.auto-list');
    lists.forEach((element) => {
        if(!element.parentElement.classList.contains('live-search')) {
            element.addEventListener("input", onInput);
            element.addEventListener("focusin", onFocusIn);
            element.addEventListener("focusout", onFocusOut);

            let liveSearch = document.createElement("div");
            liveSearch.classList.add("live-search");

            let liveSearchNotFound = document.createElement("div");
            liveSearchNotFound.innerText = "Ничего не найдено";
            liveSearchNotFound.classList.add(
                "live-search-not-found",
                "box-decorated"
            );

            let liveSearchResults = document.createElement("div");
            liveSearchResults.classList.add(
                "live-search-results",
                "box-decorated"
            );

            element.parentNode.insertBefore(liveSearch, element);
            liveSearch.appendChild(element);
            liveSearch.appendChild(liveSearchResults);
            liveSearch.appendChild(liveSearchNotFound);
        }

    })
}

onInput = (e) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => performSearch(e), 300);
};

onFocusIn = (e) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => performSearch(e), 300);
};

onFocusOut = (e) => {
    setTimeout(() => {
        clearTimeout(debounceTimer);
        hideResults(e);
        hideNotFound(e);
    }, 50)
};

onClickResult = (e) => {};

_onClickResult = (e) => {
    onClickResult(e.target.parentNode.querySelector(".live-search-result"));
    hideResults(e);
};

performSearch = (e) => {
    hideResults(e);
    hideNotFound(e);

    e.target.parentNode.querySelector('.live-search-results').innerHTML = "";
    sendPredictAjax(+e.target.dataset.list_id, e.target.value, e);
};

showNotFound = (e) => {
    e.target.parentNode.querySelector('.live-search-not-found').classList.add("active");
};

hideNotFound = (e) => {
    e.target.parentNode.querySelector('.live-search-not-found').classList.remove("active");
};

showResults = (e) => {
    e.target.parentNode.querySelector('.live-search-results').classList.add("active");
};

hideResults = (e) => {
    e.target.closest('.live-search').querySelector('.live-search-results').classList.remove("active");
};

addItem = (item, e) => {
    const result = document.createElement("div");
    result.insertAdjacentHTML("beforeend", item.trim());
    result.classList.add("live-search-result");
    result.addEventListener('pointerdown', setInputValue);
    e.target.parentNode.querySelector('.live-search-results').insertAdjacentElement("beforeEnd", result);
};

function setInputValue(e) {
    let input = e.target.closest('.live-search').querySelector('*[name]');
    input.value = e.target.innerText.trim();
    input.dispatchEvent(new Event('change'));
    hideResults(e);
}

function sendPredictAjax(listId, value, e) {
    $.ajax({
        url: 'auto-lists/predict?id=' + listId + '&value=' + value,
        dataType: 'json',
        method: 'POST',
        success: (res) => {
            if (res.items && res.items.length) {
                showResults(e);
                res.items.forEach((item) => this.addItem(item, e));
            }

            if (res.items && res.items.length < 1) {
                showNotFound(e);
            }
        }
    });
}
