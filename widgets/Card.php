<?php

namespace app\widgets;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
//use app\helpers\Access;

class Card extends \yii\base\Widget
{
    public $title         = '';
    public $options       = [];
    public $titleOptions  = [];
    public $headerOptions = [];
    public $bodyOptions   = [];

    /**
     * Initializes the widget.
     * This renders the form open tag.
     */
    public function init()
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        if(empty($this->options['class'])) $this->options['class'] = 'card';
        else Html::addCssClass($this->options, ['widget' => 'panel']);

        echo Html::beginTag('div', $this->options);
        if($this->title)
        {
            if(empty($this->titleOptions['class'])) $this->titleOptions['class'] = 'card-title';
            $title = Html::tag('h4', $this->title, $this->titleOptions);

            if(empty($this->headerOptions['class'])) $this->headerOptions['class'] = 'card-header';
            echo Html::tag('div', $title, $this->headerOptions);
        }
        echo Html::beginTag('div', ['class' => 'card-content collapse show']);
        echo Html::beginTag('div', ['class' => 'card-body']);
    }

    /**
     * Runs the widget.
     * This registers the necessary javascript code and renders the form close tag.
     * @throws InvalidCallException if `beginField()` and `endField()` calls are not matching
     */
    public function run()
    {
        echo Html::endTag('div');
        echo Html::endTag('div');
        echo Html::endTag('div');
    }
}
