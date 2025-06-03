<?php

namespace app\grid;

use himiklab\sortablegrid\SortableGridAsset;
use yii\grid\Column;
use yii\helpers\Html;
use yii\helpers\Url;

class GridViewClean extends \yii\grid\GridView
{
    public $showPageSummary = false;
    public $sortableAction = '';
    public $tableOptions = ['class' => 'table align-middle table-check table-striped mb-0'];

    public $responsive = true;

    public $pageSizeSet = [20 => 20, 50 => 50, 'all' => 'Все'];
    public $createRowModel = false;
    public $showPageSize = false;
    public $pageSizeInHeader = false;

    public $headerRowOptions = ['class' => 'table-light'];

    public $layout = '
<div class="row py-2 d-flex justify-content-between align-items-center"><div class="col-md col-sm-12 d-flex justify-content-center justify-content-md-start">{actions}</div><div class="col-md col-sm-12 d-flex justify-content-center">{pager}</div><div class="col-md col-sm-12 d-flex justify-content-center justify-content-md-end">{summary}</div></div>
{items}
<div class="row py-2 d-flex justify-content-between align-items-center"><div class="col-md col-sm-12 d-flex justify-content-center justify-content-md-start">{actions}</div><div class="col-md col-sm-12 d-flex justify-content-center">{pager}</div><div class="col-md col-sm-12 d-flex justify-content-center justify-content-md-end">{summary}</div></div>
';

    public $actions = '';

    public $pager = ['class' => 'app\widgets\LinkPager'];

    public function init()
    {
        parent::init();
        $this->sortableAction ?? Url::to($this->sortableAction);
    }

    /**
     * {@inheritdoc}
     */
    public function renderSection($name)
    {
        switch ($name) {
            case '{actions}':
                return $this->actions;
            case '{items}':
                $items = parent::renderSection($name);
                if ($this->responsive) {
                    $items = '<div class="table-responsive">' . $items . '</div>';
                }
                return $items;
            default:
                return parent::renderSection($name);
        }
    }

    /**
     * Renders the table header.
     * @return string the rendering result.
     */
    public function renderTableHeader()
    {
        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column Column */
            $cells[] = $column->renderHeaderCell();
        }
        $content = Html::tag('tr', implode('', $cells), $this->headerRowOptions);
        if ($this->filterPosition === self::FILTER_POS_HEADER) {
            $content = $this->renderFilters() . $content;
        } elseif ($this->filterPosition === self::FILTER_POS_BODY) {
            $content .= $this->renderFilters();
        }

        return "<thead>\n" . $content . "\n" . ($this->showHeaderFooter ? '<!-- theadfoot -->' : '') . "</thead>";
    }

    public $showHeaderFooter = false;
    protected $_theadfoot = '';

    public function renderSummary()
    {
        $result = '';
        if($this->showPageSize && $this->pageSizeInHeader) {
            $this->pageSizeInHeader = false;
            $result.= '&nbsp;';
            $perPage = !empty($_GET['per-page']) ? $_GET['per-page'] : $this->pageSizeSet[array_key_first($this->pageSizeSet)] ;
            $result.= "Показывать:&nbsp;";
            $result .= Html::dropDownList('per-page', $perPage, $this->pageSizeSet, ['id' => 'page-size-selector']);
        } else {
            $this->pageSizeInHeader = true;
        }
        return parent::renderSummary().$result;
    }

    /**
     * Renders the table footer.
     * @return string the rendering result.
     */
    public function renderTableFooter()
    {
        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column Column */
            $cells[] = $column->renderFooterCell();
        }
        $content = Html::tag('tr', implode('', $cells), $this->footerRowOptions);
        if ($this->filterPosition === self::FILTER_POS_FOOTER) {
            $content .= $this->renderFilters();
        }

        $this->_theadfoot = $content;

        return "<tfoot>\n" . $content . "\n</tfoot>";
    }

    public function renderTableBody()
    {
        $models = array_values($this->dataProvider->getModels());
        $keys = $this->dataProvider->getKeys();
        $rows = [];
        foreach ($models as $index => $model) {

            $key = $keys[$index];
            if ($this->beforeRow !== null) {
                $row = call_user_func($this->beforeRow, $model, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }

            $rows[] = $this->renderTableRow($model, $key, $index);

            if ($this->afterRow !== null) {
                $row = call_user_func($this->afterRow, $model, $key, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }
        }

        $createRow = '';
        if($this->createRowModel) {
            $createRow = $this->renderTableRow($this->createRowModel, 0, count($rows))."\n";
        }
        $content = '';

        if (empty($rows) && $this->emptyText !== false) {
            $colspan = count($this->columns);

            $content = "<tbody>\n<tr><td colspan=\"$colspan\">" . $this->renderEmpty() . "</td></tr>\n".$createRow."</tbody>";
        } else {
            $content = "<tbody>\n" . implode("\n", $rows) . "\n".$createRow."</tbody>";
        }

        if ($this->showPageSummary) {
            $summary = $this->renderPageSummary();

            return $this->pageSummaryPosition === self::POS_TOP ? ($summary.$content) : ($content.$summary);
        }

        return $content;
    }

    /**
     * Renders the data models for the grid view.
     * @return string the HTML code of table
     */
    public function renderItems()
    {
        $caption = $this->renderCaption();
        $columnGroup = $this->renderColumnGroup();
        $tableHeader = $this->showHeader ? $this->renderTableHeader() : false;
        $tableBody = $this->renderTableBody();

        $tableFooter = false;
        $tableFooterAfterBody = false;

        if ($this->showFooter) {
            if ($this->placeFooterAfterBody) {
                $tableFooterAfterBody = $this->renderTableFooter();
            } else {
                $tableFooter = $this->renderTableFooter();
            }
        }
        if ($this->showHeaderFooter) {
            $tableHeader = str_replace('<!-- theadfoot -->', $this->_theadfoot, $tableHeader);
        }

        $content = array_filter([
            $caption,
            $columnGroup,
            $tableHeader,
            $tableFooter,
            $tableBody,
            $tableFooterAfterBody,
        ]);

        return Html::tag('table', implode("\n", $content), $this->tableOptions);
    }

    protected function registerWidget()
    {
        $view = $this->getView();
        $view->registerJs("jQuery('#{$this->options['id']}').SortableGridView('{$this->sortableAction}');");
        SortableGridAsset::register($view);
    }

    public function run()
    {
        if ($this->sortableAction) {
            $this->registerWidget();
        }
        parent::run();
    }
}
