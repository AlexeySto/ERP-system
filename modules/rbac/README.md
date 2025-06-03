INSTALLATION
------------

config/web.php:

~~~
'bootstrap' => [
    ...,
    'rbac'
]
~~~

~~~
'modules' => [
    'rbac' => [
        ...,
        'class' => 'app\modules\rbac\Module',
            'fullAccess' => [
                'roles' => [
                    'role_name',
                    ...
                ],
                'userIds' => [1]
            ]
    ]
],
~~~

~~~
'components' => [
    'user' => [
        ...,
        'accessChecker' => 'app\modules\rbac\AccessChecker',
    ]
~~~

migrations

~~~
yii migrate --migrationPath=@yii/rbac/migrations
yii migrate --migrationPath=modules/rbac/migrations
~~~

REQUIREMENTS
------------
components/UrlManager.php,
config/menuitems.php

PERMISSIONS TEMPLATE
------------

~~~
module_controller_action[_AdditionalRules...]
~~~

Module (required): app id or module id (basic, backend, some-other-module)

Controller (required): Controller route name (site, some-controller)

Action (required): Action route name (some-action)

AdditionalRules: any custom parts

~~~
app_cash-flow-transaction-categories_index_Read
~~~