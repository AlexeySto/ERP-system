<?php


namespace app\modules\rbac\models;

use yii\helpers\ArrayHelper;
use app\models\User;

class AuthAccess
{
    private static function generateNameFromTitle($title)
    {
        $converter = array(
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'e',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'y',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'sch',
            'ь' => '',
            'ы' => 'y',
            'ъ' => '',
            'э' => 'e',
            'ю' => 'yu',
            'я' => 'ya',
            'А' => 'A',
            'Б' => 'B',
            'В' => 'V',
            'Г' => 'G',
            'Д' => 'D',
            'Е' => 'E',
            'Ё' => 'E',
            'Ж' => 'Zh',
            'З' => 'Z',
            'И' => 'I',
            'Й' => 'Y',
            'К' => 'K',
            'Л' => 'L',
            'М' => 'M',
            'Н' => 'N',
            'О' => 'O',
            'П' => 'P',
            'Р' => 'R',
            'С' => 'S',
            'Т' => 'T',
            'У' => 'U',
            'Ф' => 'F',
            'Х' => 'H',
            'Ц' => 'C',
            'Ч' => 'Ch',
            'Ш' => 'Sh',
            'Щ' => 'Sch',
            'Ь' => '',
            'Ы' => 'Y',
            'Ъ' => '',
            'Э' => 'E',
            'Ю' => 'Yu',
            'Я' => 'Ya',
        );
        $converter[' '] = '_';

        return mb_convert_case(strtr($title, $converter), MB_CASE_LOWER);
    }

    public static function createRole($title, $description, $name = null)
    {
        $title = trim($title);
        if (!$name) {
            $name = self::generateNameFromTitle($title);
        }
        $auth = \Yii::$app->authManager;
        if ($auth->getRole($name)) {
            return false;
        }
        $role = new AuthItem(compact(['title', 'description', 'name']));
        $role->type = 1;
        if ($role->save()) {
            return $role->name;
        }
        return false;
    }

    public static function updateRole($name, $title, $description, $updateName = false)
    {
        $role = AuthItem::findOne(['name' => $name, 'type' => 1]);
        if (!$role) {
            return false;
        }
        $role->title = trim($title);
        if ($updateName) {
            $role->name = self::generateNameFromTitle($role->title);
        }
        $role->description = trim($description);
        if ($role->save()) {
            return $role->name;
        }
        return false;
    }

    public static function copyRolePermissions($fromName, $toName)
    {
        $auth = \Yii::$app->authManager;
        $from = $auth->getRole($fromName);
        $to = $auth->getRole($toName);
        if($from && $to) {
            $permissions = $auth->getPermissionsByRole($fromName);
            $auth->removeChildren($to);
            foreach ($permissions as $permission) {
                $auth->addChild($to, $permission);
            }
            return true;
        }
        return false;
    }

    public static function getRoles()
    {
        $fullAccess = \Yii::$app->getModule('rbac')->fullAccess['roles'] ?? null;
        $items = AuthItem::find()->where(['type' => 1])->andFilterWhere(['not in', 'name', $fullAccess])->orderBy('title')->all();
        foreach ($items as $item) {
            if(!$item->title) {
                $item->title = $item->description;
            }
        }
        return $items;
    }

    public static function getChildPermissions($permission)
    {
        return AuthItemChild::find()->select(['child'])->where(['parent' => $permission])->column();
    }

    public static function getAccessDataSet()
    {
        $pages = [];
        $counter = 0;
        $menuItems = \Yii::$app->urlManager->menuData;
        $auth = \Yii::$app->authManager;
        $permissions = [];
        foreach ($auth->getPermissions() as $perm) {
            $permissions[$perm->name] = $perm->description;
        }

        $sectionPermission = [
            'name' => null,
            'description' => 'Доступ к разделу'
        ];

        foreach ($menuItems as $menuItem) {
            $section = [
                'id' => ++$counter,
                'level' => 1,
                'items_count' => 0,
                'title' => $menuItem['label'],
                'permission' => $sectionPermission
            ];
            $subsections = [];
            foreach ($menuItem['items'] ?? [] as $item) {
                list($controller, $actionId) = \Yii::$app->createController($item['url']);
                if(!$controller) continue;
                $searchString = implode('_', [$controller->module->id, $controller->id, $controller->createAction($actionId)->id]);
                $basePermissionName = $item['permission'] ?? null;
                $basePermission = $auth->getPermission($basePermissionName ?? $searchString);
                if($basePermission && !key_exists($basePermission->name, $permissions)) {
                    $basePermission = null;
                }
                if($basePermission) {
                    unset($permissions[$basePermission->name]);
                }
                if(!$basePermission) {
                    continue;
                }
                $foundPermissions = [];
                foreach ($permissions as $name => $description) {
                    $permission = $auth->getPermission($name);
                    $hasChild = $basePermission && $auth->hasChild($permission, $basePermission);
                    if($permission && (strpos($name, $searchString) === 0) || $hasChild) {
                        $foundPermissions[] = compact('name', 'description');
                        unset($permissions[$name]);
                    }
                }
                $level = 2;
                $parent_id = $section['id'];
                if($basePermission) {
                    $subsections[] = [
                        'id' => ++$counter,
                        'parent_id' => $section['id'],
                        'level' => $level,
                        'items_count' => count($foundPermissions),
                        'title' => $item['label'],
                        'permission' => ['name' =>$basePermission->name, 'description' => $basePermission->description],
                    ];
                    $parent_id = $counter;
                    $level = 3;
                }
                foreach ($foundPermissions as $foundPermission) {
                    $subsections[] = [
                        'id' => ++$counter,
                        'parent_id' => $parent_id,
                        'level' => $level,
                        'title' => $item['label'],
                        'permission' => $foundPermission,
                        'children' => static::getChildPermissions($foundPermission['name'])
                    ];
                }
            }
            $section['items_count'] = count($subsections);
            $pages[] = $section;
            $pages = array_merge($pages, $subsections);
        }
        if($count = count($permissions)) {
            $section = [
                'id' => ++$counter,
                'level' => 1,
                'items_count' => $count,
                'title' => 'Другое',
            ];
            $subsections = [];
            foreach ($permissions as $name => $description) {
                $subsections[] = [
                    'id' => ++$counter,
                    'parent_id' => $section['id'],
                    'level' => 2,
                    'title' => '',
                    'permission' => compact('name', 'description'),
                ];
            }
            $pages = array_merge($pages, [$section], $subsections);
        }
        $roles = ArrayHelper::getColumn(self::getRoles(), 'name');
        $roles_permissions = AuthItemChild::find()->select(['parent', 'child'])->where(['parent' => $roles])->all();
        $mapped = ArrayHelper::map($roles_permissions, 'parent', 'parent', 'child');
        foreach ($pages as $index => $page) {
            if(key_exists('permission', $page)&& key_exists($page['permission']['name'], $mapped)) {
                foreach ($mapped[$page['permission']['name']] as $item) {
                    $pages[$index][$item] = 1;
                }
            }
        }
        \Yii::warning($pages);
        return $pages;
    }

    public static function getRoleUsers($roleName)
    {
        return User::find()
            ->select(['id', 'name' => 'fio'])
            ->leftJoin('auth_assignment', 'id = user_id')
            ->where(['item_name' => $roleName])
            ->asArray()->all();
    }

    public static function switchRole($role, $permission, $value)
    {
        $auth = \Yii::$app->authManager;
        $role = $auth->getRole($role);
        $permission = $auth->getPermission($permission);
        if($role && $permission) {
            if($value) {
                if(!$auth->hasChild($role, $permission)) {
                    $auth->addChild($role, $permission);
                }
                return true;
            } else {
                if($auth->hasChild($role, $permission)) {
                    $auth->removeChild($role, $permission);
                }
                return false;
            }
        }
        return false;
    }
}