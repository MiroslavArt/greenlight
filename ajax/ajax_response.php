<?php
if(!function_exists('__CrmPropductRowListEndResponse'))
{
    function __CrmPropductRowListEndResponse($result)
    {
        $GLOBALS['APPLICATION']->RestartBuffer();
        header('Content-Type: application/json; charset='.LANG_CHARSET);


        if(!empty($result))
        {
            echo json_encode($result);
        }
        require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
        die();
    }
}