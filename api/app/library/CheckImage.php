<?php

use Phalcon\Mvc\User\Component;

class CheckImage extends Component
{
    function getMimeType($raw_data){
    /*
    データのMIME情報を返す
    */
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_buffer($finfo, $raw_data);
        finfo_close($finfo);
        return $mime;
    }
    function checkMimeType($mime){
    /*
    画像データのMIMEタイプのチェックを行う
    画像用のMIMEではない場合はエラーを出す
    */
        switch($mime){
            case 'image/gif':
                break;
            
            case 'image/jpeg':
                break;
            
            case 'image/bmp':
                break;
            case 'image/png':
                break;
            
            default:
                throw new RuntimeException('画像形式が誤っています');
        }
    }
}