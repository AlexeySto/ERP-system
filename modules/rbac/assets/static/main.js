const baseUrl = '/rbac';

function updateSections(level = 2) {
    let trs = document.querySelectorAll('tr[data-level="'+level+'"]:has(input:disabled)');
    trs.forEach((tr) => {
        updateSection(tr);
    });
    if(--level > 0) {
        updateSections(1);
    }
}

function updateSection(tr) {
    let checkboxes = tr.querySelectorAll('input');
    checkboxes.forEach((checkbox) => {
        let selector = 'tr[data-parent_id="'+tr.dataset.id+'"] input[data-role="'+checkbox.dataset.role+'"]';
        let children = document.querySelectorAll(selector + ':checked');
        checkbox.checked = children.length > 0;
        if(+tr.dataset.level === 2 && children.length === 0) {
            checkbox.removeAttribute('disabled');
            checkbox.checked = (+checkbox.dataset.checked === 1);
        }
        children.forEach((element) => {
            let childPermissions = element.dataset.children ?? false;
            if(childPermissions) {
                childPermissions = childPermissions.split(',');
                childPermissions.forEach((permission) => {
                    let child = document.querySelector(selector + '[data-permission="'+permission+'"]');
                    if(child) {
                        child.setAttribute('disabled', true);
                        child.checked = true;
                    }
                })
            }
        })
    })
}

updateSections();

function updateBranchColumn(tr, roleName, childPermissions='', checked = false) {
    if(+tr.dataset.level > 1) {
        let parentTr = document.querySelector('tr[data-id="'+tr.dataset.parent_id+'"]');
        let parentCheckbox = parentTr.querySelector('input[data-role="'+roleName+'"]');
        if(+parentTr.dataset.level === 2 && childPermissions) {
            childPermissions = childPermissions.split(',');
            childPermissions.forEach((permission) => {
                let child = document.querySelector('tr[data-parent_id="'+parentTr.dataset.id+'"] input[data-role="'+roleName+'"][data-permission="'+permission+'"]');
                if(child) {
                    if(checked) {
                        child.setAttribute('disabled', true);
                        child.checked = true;
                    } else {
                        child.removeAttribute('disabled');
                        child.checked = (+child.dataset.checked === 1);
                    }
                }
            })
        }
        let checkboxes = document.querySelectorAll('tr[data-parent_id="'+tr.dataset.parent_id+'"] input[data-role="'+roleName+'"]:checked')
        if(+parentTr.dataset.level === 2) {
            if(checkboxes.length > 0) {
                parentCheckbox.setAttribute('disabled', 'true');
                parentCheckbox.checked = checkboxes.length > 0;
            } else {
                parentCheckbox.removeAttribute('disabled');
                parentCheckbox.checked = (+parentCheckbox.dataset.checked === 1);
            }
        } else {
            if(parentCheckbox.hasAttribute('disabled')) {
                parentCheckbox.checked = checkboxes.length > 0;
            }
        }

        updateBranchColumn(parentTr, roleName);
    }
}

//переключение прав
document.querySelectorAll('.switch-permission').forEach((checkBox) => {
    checkBox.addEventListener('click', (event) => {
        updateBranchColumn(event.target.closest('tr'), event.target.dataset.role, (event.target.dataset.children ?? ''), event.target.checked);
        let data = new FormData;
        data.append('role', event.target.dataset.role);
        data.append('permission', event.target.dataset.permission);
        data.append('value', event.target.checked ? 1 : 0);
        event.target.dataset.checked = event.target.checked ? 1 : 0;
        data.append(yii.getCsrfParam(), yii.getCsrfToken());
        fetch(baseUrl + '/switch-permission', {
            method: 'POST',
            body: data
        });
    })
});

//Дерево
document.querySelectorAll('.row-expand').forEach((span) => {
    span.addEventListener('click', (event) => {
        switchRowExpand(event.target);
    })
});

function switchRowExpand(sender) {
    let children = document.querySelectorAll('tr[data-parent_id="' + sender.dataset.id + '"')
    let hide = false;
    if (sender.classList.contains('fa-plus')) {
        sender.classList.remove('fa-plus');
        sender.classList.add('fa-minus');
        hide = false;
    } else {
        sender.classList.remove('fa-minus');
        sender.classList.add('fa-plus');
        hide = true;
    }
    children.forEach((tr) => {
        if (hide) {
            let span = tr.querySelector('.row-expand');
            if (span && span.classList.contains('fa-minus')) {
                switchRowExpand(span);
            }
        }
        tr.hidden = hide;
    });
}

//Добавление, редактирование и копирование роли
document.querySelectorAll('.btn-role-create').forEach((element) => {
    element.addEventListener('click', () => {
        showRoleEditor();
    })
});
document.querySelectorAll('.btn-role-update').forEach((element) => {
    element.addEventListener('click', (event) => {
        showRoleEditor(event.target);
    })
});
document.querySelectorAll('.btn-role-copy').forEach((element) => {
    element.addEventListener('click', (event) => {
        showRoleEditor(event.target, true);
    })
});

function showRoleEditor(target = null, copy = false) {
    let title = document.getElementById('editRoleTitle');
    title.innerText = "Добавление роли";
    if(target) {
        if(copy) {
            title.innerText = 'Копирование роли "'+target.dataset.title+'"';
        } else {
            title.innerText = 'Редактирование роли "'+target.dataset.title+'"';
        }
    }
    let roleName = document.getElementById('roleName');
    let roleTitle = document.getElementById('roleTitle');
    let roleDescription = document.getElementById('roleDescription');
    let roleCopyFrom = document.getElementById('roleCopyFrom');
    roleName.value = '';
    roleTitle.value = '';
    roleDescription.value = '';
    if (target) {
        if(copy) {
            roleCopyFrom.value = target.dataset.name;
        } else {
            roleName.value = target.dataset.name;
            roleTitle.value = target.parentElement.previousElementSibling.innerText;
            roleDescription.value = target.parentElement.previousElementSibling.title;
        }
    }

    let modal = new bootstrap.Modal(document.getElementById('editRoleModal'));
    modal.show();
    return modal;
}

document.querySelector('#form_update_role_submit').addEventListener('click', (event) => {
    let form = document.querySelector('#role_update_form');
    if (form.checkValidity()) {
        const formData = new FormData(form);
        let action = '/create-role';
        if (formData.get('name')) {
            action = '/update-role?name=' + formData.get('name');
        }
        formData.append(yii.getCsrfParam(), yii.getCsrfToken());
        fetch(baseUrl + action, {
            method: 'POST',
            body: formData
        }).then((res) => res.json())
            .then((data) => {
                if (data.result) {
                    window.location.reload();
                }
            });
    }
    form.classList.add('was-validated');
})

//Удаление роли
document.querySelectorAll('.btn-role-delete').forEach((element) => {
    element.addEventListener('click', (event) => {
        if (confirm('Вы действительно хотите удалить роль "' + event.target.dataset.title + '"'))
            showRoleDelete(event.target.dataset.name, event.target.dataset.title);
    })
});

document.querySelector('#retryDeleteRole').addEventListener('click', (event) => {
    showRoleDelete(event.target.dataset.name);
});

let deleteModal = null;

function showRoleDelete(name, title = null) {
    if(!deleteModal) {
        deleteModal = new bootstrap.Modal(document.getElementById('deleteRoleModal'));
    }
    if(title) {
        document.querySelector('#deleteRoleTitle').innerText = title;
    }
    document.querySelector('#retryDeleteRole').dataset.name = name;
    let userList = document.querySelector('#deleteRoleUsers');
    userList.innerHTML = '';
    const formData = new FormData;
    formData.append('name', name);
    formData.append(yii.getCsrfParam(), yii.getCsrfToken());
    fetch(baseUrl + '/delete-role', {
        method: 'POST',
        body: formData
    }).then((res) => res.json())
        .then((data) => {
            if (data.result === true) {
                window.location.reload();
            } else {
                data.users.forEach((user) => {
                    let anchor = document.createElement('a');
                    anchor.classList.add('list-group-item', 'list-group-item-action');
                    anchor.innerText = user.name
                    anchor.setAttribute('href', '/people/edit/'+user.id);
                    anchor.setAttribute('target', '_blank');
                    userList.appendChild(anchor);
                })
                deleteModal.show();
            }
        });
}