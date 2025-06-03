<?php

use app\modules\rbac\assets\ModuleAsset;
use kartik\editable\Editable;
use app\grid\GridViewClean;
use app\widgets\Card;
use yii\helpers\Html;
use app\widgets\MultipleInput;
use app\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $roles array */
/* @var $dataProvider yii\data\ArrayDataProvider */

ModuleAsset::register($this);

$this->title = Yii::$app->urlManager->getLastTitle();
$actionEditPartial = ['/divisions/edit-partial'];

$columns = [
    [
        'label' => 'Страница',
        'attribute' => 'title',
        'format' => 'raw',
        'value' => function($model) {
            $result = '<i class="row-expand level-' . ($model['level'] ?? 0) . '">&nbsp;</i>';
            if (($model['items_count'] ?? 0) > 0) {
                $result = '<i class="btn btn-sm fas fa-plus row-expand level-' . ($model['level'] ?? 0) .
                    '" data-id="' . ($model['id'] ?? 0) . '"></i>';
            }
            return $result.$model['title'];
        }
    ],
    [
        'label' => 'Право',
        'attribute' => 'permission.description',
        'contentOptions' => function($item) {
            return [
                'data-bs-toggle' => 'tooltip',
                'title' => $item['permission']['name'] ?? ''
            ];
        }
    ],
];
foreach ($roles as $role) {
    $columns[] = [
        'label' => '<span data-bs-toggle="tooltip" title="' . $role->description . '">' . $role->title .
            '</span><div class="d-flex mt-1 justify-content-center">' .
            '<span class="btn btn-outline-primary btn-sm fa fa-pen mx-1 btn-role-update" data-title="' . $role->title .'" data-name="' . $role->name . '" title="Редактировать роль"></span>' .
            '<span class="btn btn-outline-success btn-sm fa fa-clone mx-1 btn-role-copy" data-title="' . $role->title .'" data-name="' . $role->name . '" title="Копировать в новую роль"></span>' .
            '<span class="btn btn-outline-danger btn-sm fa fa-trash mx-1 btn-role-delete" data-title="' . $role->title .
            '" data-name="' . $role->name . '" title="Удалить роль"></span></div>',
        'encodeLabel' => false,
        'contentOptions' => ['class' => 'text-center'],
        'attribute' => $role->name,
        'format' => 'raw',
        'value' => function($model, $key, $index, $column) {
            if($model['permission'] ?? '') {
                return '<input class="switch-permission" type="checkbox"'
                    .(($model[$column->attribute] ?? '') ? ' checked' : '')
                    .' data-role="' . $column->attribute
                    .'" data-permission="' . ($model['permission']['name'] ?? '')
                    .'" data-children="' . (($model['children'] ?? false) ? implode(',', $model['children']) : '')
                    .'" data-id="' . ($model['id'] ?? '')
                    .'" data-checked="' . (($model[$column->attribute] ?? '') ? '1' : '0')
                    .'"'
                    .(($model['level'] === 1 || ($model['level'] === 2 && ($model['items_count'] ?? 0) > 0)) ? ' disabled' : '')
                    .'>';
            }
            return '';
        },
    ];
}
?>

<?php Card::begin([]); ?>

<?= GridViewClean::widget([
    'dataProvider' => $dataProvider,
    'columns' => $columns,
    'tableOptions' => ['class' => 'table align-middle table-check table-bordered table-striped mb-0'],
    'summary' => '<span class="btn btn-outline-success btn-sm fa fa-bars" data-bs-toggle="modal" data-bs-target="#orderRoleModal" hidden></span><span class="btn btn-outline-success btn-sm fa fa-plus btn-role-create" title="Добавить роль"></span>',
    'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
    'rowOptions' => function($model) {
        $options = [
            'data-id' => ($model['id'] ?? 0),
            'data-parent_id' => ($model['parent_id'] ?? 0),
            'data-level' => $model['level'],
            'class' => 'table-warning'
        ];
        if (($model['level'] ?? 0) !== 1) {
            $options['hidden'] = 1;
            $options['class'] = ($model['level'] ?? 0) === 2 ? 'table-success' : 'table-danger';
        }
        return $options;
    }
]);
?>

<?php

Card::end();

?>
<!--Role delete modal -->
<div class="modal fade" id="deleteRoleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Удаление роли: <span id="deleteRoleTitle"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" role="alert">
                    Невозможно удалить роль, так как она назначена следующим пользователям:
                </div>
                <div id="deleteRoleUsers" class="list-group">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button id="retryDeleteRole" type="button" class="btn btn-primary" data-name="">Повторить</button>
            </div>
        </div>
    </div>
</div>
<!-- Role editor modal -->
<div class="modal fade" id="editRoleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRoleTitle">Добавление роли</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="role_update_form" class="needs-validation">
                    <input id="roleName" type="hidden" name="name">
                    <input id="roleCopyFrom" type="hidden" name="from_role_name">
                    <div class="mb-3">
                        <label for="roleTitle" class="form-label">Название</label>
                        <input type="text" name="title" class="form-control" id="roleTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="roleDescription" class="form-label">Описание</label>
                        <input type="text" name="description" class="form-control" id="roleDescription">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" id="form_update_role_submit" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<!-- Roles order modal -->
<div class="modal fade" id="orderRoleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRoleTitle">Изменение порядка столбцов</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="list-group">
                    <?php foreach ($roles as $role) :?>
                        <div class="list-group-item list-group-item-action draggable" data-name="<?= $role->name ?>"><?= $role->title ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" id="form_update_role_submit" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</div>

