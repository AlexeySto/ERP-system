<?php

namespace app\lib;

use kartik\mpdf\Pdf;

class XPdf extends Pdf
{

    public function addPage()
    {
        $api = $this->getApi();
        $api->AddPage();
    }

    public function writeHTML($html)
    {
        $api = $this->getApi();
        $api->writeHTML($html);
    }

    public function importPdf($file = null)
    {
        if (!file_exists($file)) {
            return;
        }
        $api = $this->getApi();
        $pageCount = $api->SetSourceFile($file);
        for ($i = 1; $i <= $pageCount; $i++) {
            $api->AddPage();
            $templateId = $api->ImportPage($i);
            $api->UseTemplate($templateId,0,0,null,null,true);
        }
    }
}
