// const baseUrl = '<?= '/' . Yii::$app->controller->getUniqueId() ?>';
window.addEventListener('load',function () {
    document.querySelectorAll('.btn-row-edit').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            rowEditStart(e.target.closest('tr'));
        })
    })

    document.querySelectorAll('.btn-row-add').forEach((btn) => {
        btn.addEventListener('click', rowCreate)
    })
});

function rowCreate() {
    setEditing(true);
    let formRow = document.querySelector('#edit-form-row').content.cloneNode(true).children[0];
    formRow.querySelector('.btn-row-edit-save').onclick = () => { rowEditAccept(formRow) }
    formRow.querySelector('.btn-row-edit-cancel').onclick = () => { rowEditCancel(formRow) }
    document.querySelector('table tbody').insertAdjacentElement('beforeend', formRow);
}

function rowEditStart(row) {
    setEditing(true);
    let id = row.dataset.key;
    let formRow = document.querySelector('#edit-form-row').content.cloneNode(true).children[0];
    formRow.dataset.key = id;
    fetch(baseUrl + '/view?id='+id, {
        headers: {
            "X-Requested-With": "XMLHttpRequest"
        }
    })
        .then((res) => res.json())
        .then((data) => {
            let cellsData = row.querySelectorAll('td');
            let index = 0;
            formRow.querySelectorAll('td').forEach((td) => {
                let input = td.querySelector('*[name]');
                if(input) {
                    input.value = data[input.name];
                } else {
                    if(!td.classList.contains('gridActions')) {
                        td.innerHTML = cellsData[index].innerHTML;
                    }
                }
                index++;
            })
            row.style.display = 'none';
            row.parentNode.insertBefore(formRow, row);
            formRow.querySelector('.btn-row-edit-save').onclick = () => { rowEditAccept(formRow) }
            formRow.querySelector('.btn-row-edit-cancel').onclick = () => { rowEditCancel(formRow) }
        })
}

function rowEditAccept(row) {
    let form = new FormData();
    row.querySelectorAll('*[name]').forEach((element) => {
        form.append(element.name, element.value);
    });
    form.append(yii.getCsrfParam(), yii.getCsrfToken());
    let url = row.dataset.key ? '/edit?id=' + row.dataset.key : '/create'
    fetch(baseUrl + url, {
        method: 'POST',
        headers: {
            "X-Requested-With": "XMLHttpRequest"
        },
        body: form
    })
        .then((res) => res.json())
        .then((data) => {
            if(data.result === true) {
                window.location.reload();
            }
        })
}

function setEditing(value) {
    document.querySelectorAll('.btn-row-edit, .btn-row-add').forEach((element) => {
        if(value) {
            element.classList.add('disabled');
        } else {
            element.classList.remove('disabled');
        }

    })
}

function rowEditCancel(row) {
    setEditing(false);
    row.remove();
    if(row.dataset.key) {
        document.querySelector('tr[data-key="'+row.dataset.key+'"]').style.display = 'table-row';
    }
}