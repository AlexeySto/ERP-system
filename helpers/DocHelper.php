<?php

namespace app\helpers;

use app\models\Order;
use app\models\OrderWork;
use kartik\mpdf\Pdf;

class DocHelper
{
    /**
     * Возвращает сумму прописью
     * @author runcore
     * @uses morph(...)
     */
    public static function num2str($num) {
        $nul='ноль';
        $ten=array(
            array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
            array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
        );
        $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
        $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
        $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
        $unit=array( // Units
            array('копейка' ,'копейки' ,'копеек',	 1),
            array('рубль'   ,'рубля'   ,'рублей'    ,0),
            array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
            array('миллион' ,'миллиона','миллионов' ,0),
            array('миллиард','милиарда','миллиардов',0),
        );
        //
        list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
        $out = array();
        if (intval($rub)>0) {
            foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
                if (!intval($v)) continue;
                $uk = sizeof($unit)-$uk-1; // unit key
                $gender = $unit[$uk][3];
                list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
                else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ($uk>1) $out[]= static::morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
            } //foreach
        }
        else $out[] = $nul;
        $out[] = static::morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
        if ($kop > 0) $out[] = $kop.' '.static::morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
        return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
    }

    /**
     * Склоняем словоформу
     * @ author runcore
     */
    protected static function morph($n, $f1, $f2, $f5) {
        $n = abs(intval($n)) % 100;
        if ($n>10 && $n<20) return $f5;
        $n = $n % 10;
        if ($n>1 && $n<5) return $f2;
        if ($n==1) return $f1;
        return $f5;
    }

    public static function orderWorkTypeListWithOptions($orderId, $workTypeId = 0)
    {
        $result = [];
        if (!($order = Order::findOne($orderId))) {
            return $result;
        }
        list($workTypes, $workTypesOptions) = \app\models\WorkType::getListWithOptions();
        foreach($order->works as $work) {
            $options = $workTypesOptions[$work->worktype_id] ?: [];
            $result[] = [
                'id' => $work->worktype_id,
                'name' => $workTypes[$work->worktype_id] ?: $work->worktype_id,
                'tarif' => $options['data-tarif'] ?: 0,
                'step' => $options['data-step'] ?: 1,
            ];
        }
        return $result;
    }

    public static function orderName($id)
    {
        static $cache = [];
        if (!isset($cache[$id])) {
            $order = \app\models\Order::findOne($id);
            $cache[$id] = $order ? (string)$order : $id;
        }
        return $cache[$id];
    }

    public static function orderNameAndComment($id)
    {
        static $cache = [];
        if (!isset($cache[$id])) {
            $order = \app\models\Order::findOne($id);
            $cache[$id][0] = $order ? (string)$order : $id;
            $cache[$id][1] = $order ? (string)$order->comment : '';
        }
        return $cache[$id];
    }

    public static function getPdf($title, $content, $filename, $download = false, $landscape = false)
    {
        $cssPath = \Yii::getAlias('@app/views/common') . '/pdf-bootstrap.css';
        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'marginLeft' => 15,
            'marginRight' => 10,
            'marginTop' => 15,
            'marginBottom' => 10,
            'marginHeader' => 0,
            'marginFooter' => 0,

            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => $landscape ? Pdf::ORIENT_LANDSCAPE : Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => $download ? Pdf::DEST_DOWNLOAD : Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            //'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'cssFile' => $cssPath,
            // any css to be embedded if required
            //'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => $title],
            // call mPDF methods on the fly
            'methods' => [
                //'SetHeader'=>[$invoiceNr],
                //'SetFooter'=>['{PAGENO}'],
                'SetHTMLFooter' => ['<p class="text-right">Страница {PAGENO} ({nb})</p>'],
            ]
        ]);

        // return the pdf output as per the destination setting
        $pdf->filename = $filename;
        return $pdf->render();
    }
}