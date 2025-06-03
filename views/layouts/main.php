<?php

use app\assets\AppExtraAsset;
use app\assets\DisableDoubleSendingAsset;
use app\assets\ExtFormsInitAsset;
use app\theme\skote\assets\SkoteAsset;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\web\View;

/** @var View $this */

SkoteAsset::register($this);
DisableDoubleSendingAsset::register($this);
ExtFormsInitAsset::register($this);
AppExtraAsset::register($this);
$isLogged = !Yii::$app->user->isGuest;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php $this->registerCsrfMetaTags() ?>
    <base href="<?= Url::to(['/'], true) ?>"/>
    <title><?= Html::encode($this->title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <?php $this->head() ?>
</head>

<body data-topbar="dark" data-layout="horizontal">
<?php $this->beginBody() ?>

<div id="layout-wrapper">

    <header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex">

                <button type="button" class="btn btn-sm px-3 font-size-16 d-lg-none header-item waves-effect waves-light" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                    <i class="fa fa-fw fa-bars"></i>
                </button>

                <div class="navbar-brand-box">
                    <a href="<?= Url::to(['/']) ?>" class="logo font-size-16 text-white">
                         <?= Yii::$app->name ?>
                    </a>
                </div>

            </div>

            <div class="d-flex">

                <div class="dropdown d-none d-lg-inline-block ms-1">
                    <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen">
                        <i class="bx bx-fullscreen"></i>
                    </button>
                </div>

                <?php if ($isLogged) { ?>
                <div class="dropdown d-inline-block" id="profile-menu">
                    <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php $name =  (string)Yii::$app->user->identity ?>
                        <img class="rounded-circle header-profile-user" src="<?= Yii::$app->user->identity->avatar ?>"
                             alt="<?= $name ?>">
                        <span class="d-none d-xl-inline-block ms-1" key="t-henry"><?= $name ?></span>
                        <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                    </button>
                    <div id="page-header-dropdown" class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="<?= Url::to(['/site/profile']) ?>"><i class="bx bx-user font-size-16 align-middle me-1"></i> <span key="t-profile">Профиль</span></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="<?= Url::to(['/site/logout']) ?>"><i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span key="t-logout">Выход</span></a>
                    </div>
                </div>
                <?php } ?>

            </div>
        </div>
    </header>

    <?php if ($isLogged) { ?>
    <div class="topnav">
        <div class="container-fluid">
            <?= \app\theme\skote\widgets\Menu::widget(['id' => 'main-menu']) ?>
        </div>
    </div>
    <?php } ?>

    <div class="main-content">

        <div class="page-content admin100">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0 font-size-18"><?= $this->title ?></h4>

                            <div class="page-title-right">
                                <?= \app\theme\skote\widgets\Breadcrumbs::widget(['homeLink' => ['url' => '/', 'label' => '<i class="fa fa-home"></i>']]) ?>
                            </div>

                        </div>
                    </div>
                </div>

                <?= \app\widgets\Flashes::widget() ?>

                <?= $content ?>

            </div>
        </div>

        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <?= date('Y') ?> © <?= Yii::$app->name ?>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-sm-end d-none d-sm-block">
                            ...
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
