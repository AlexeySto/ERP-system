<?php

namespace app\grid;

class NumericColumn extends \yii\grid\DataColumn
{
    public $format = 'raw';
    public $thisPage = true;
    public $total = 0;
    public $decimal = 2;
    public $decimalSep = '.';
    public $thousandSep = ' ';
    public $unit = '';
    public $totals = [];

    /**
     * {@inheritdoc}
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);

        if ($value) {

            if($this->decimal == 0) $value = (int)$value;
            else $value = (float)$value;

            if($this->total !== FALSE) $this->total += $value;

            if ($this->content != null) {
                return call_user_func($this->content, $model, $key, $index, $this);
            }

            $value = number_format($value, $this->decimal, $this->decimalSep, $this->thousandSep);
            if($this->unit) {
                $value .= ' ' . $this->unit;
            }
            return $value;
        }

        return parent::renderDataCellContent($model, $key, $index);
    }

    /**
     * Renders the footer cell content.
     * The default implementation simply renders [[footer]].
     * This method may be overridden to customize the rendering of the footer cell.
     * @return string the rendering result
     */
    protected function renderFooterCellContent()
    {
        if($this->total === FALSE) return '&nbsp;';

        $html = [];
        if ($this->thisPage) {
            $value = '<span class="text-nowrap" title="' . 'На этой странице' . '">' . number_format($this->total, $this->decimal, $this->decimalSep, $this->thousandSep);
            if ($this->unit) {
                $value .= ' ' . $this->unit;
            }
            $value .= '</span>';
            $html[] = $value;
        }

        if (is_array($this->totals) && isset($this->totals[$this->attribute])) {
            $value = '<span class="text-nowrap" title="' . 'На всех страницах' . '">' . number_format($this->totals[$this->attribute], $this->decimal, $this->decimalSep, $this->thousandSep);
            if($this->unit) {
                $value .= ' ' . $this->unit;
            }
            $value .= '</span>';
            $html[] = $value;
        }

        return implode(' / ', $html);
    }
}
