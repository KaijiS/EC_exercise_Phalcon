<?php

use Phalcon\Mvc\User\Component;

class ImageDataBase64 extends Component
{
/*
Base64でエンコードされた画像データの情報に関するクラス
*/
    private $_scheme ='data:application/octet-stream;base64,';
    private $_row_data;  // Base64でエンコードされた画像データ
    private $_width;     // 画像データの横のピクセル数
    private $_height;    // 画像データの縦のピクセル数
    private $_ch;        // 画像データのチャンネル数
    private $_bits;      // 画像データの量子化ビット数
    private $_mime;      // 画像データのMIMEタイプ情報
    public function __construct(string $raw_data){
    /*
    このコンストラクタにて画像データの様々な情報を設定する
    Parameters
    ----------
    string $raw_data : Base64でエンコードされた画像データ
    */
        $this->_raw_data = $raw_data;
        $image_size = getimagesize($this->_scheme . $raw_data);
        $this->_width = $image_size[0];
        $this->_height = $image_size[1];
        $this->_ch = $image_size[2];
        $this->_bits = $image_size["bits"];
        $this->_mime = $image_size["mime"];
    }
    // ----------ここからゲッターメソッド----------
    public function getWidth(){
        return $this->_width;
    }
    public function getHeight(){
        return $this->_height;
    }
    public function getCh(){
        return $this->_ch;
    }
    public function getBits(){
        return $this->_bits;
    }
    public function getMime(){
        return $this->_mime;
    }
    // ---------- ここまで　----------
    public function checkMimeType(){
    /*
    画像データのMIMEタイプのチェックを行う
    画像用のMIMEではない場合はエラーを出す
    */
        checkMimeType($this->_mime);
    }
    public function makeOriginal(){
    /*
    オリジナル画像を生成
    画像サイズを変更する場合に使用することがほとんどかと。。。
    Returns
    ----------
    original_image : 各mimetypeに対応したオリジナル画像データ
    */
        switch($this->_mime){
            case 'image/gif':
                $original_image = imagecreatefromgif($this->_scheme . $this->_raw_data);
                break;
            
            case 'image/jpeg':
                $original_image = imagecreatefromjpeg($this->_scheme . $this->_raw_data);
                break;
            
            case 'image/bmp':
                $original_image = imagecreatefrombmp($this->_scheme . $this->_raw_data);
                break;
            case 'image/png':
                $original_image = imagecreatefrompng($this->_scheme . $this->_raw_data);
                break;
            
            default:
                throw new RuntimeException('画像形式が誤っています',$this->_mime);
                // exit(0);
        }
        return $original_image;
    }
    public function reSize($width=600, $height=400){
        /*
        オリジナル画像を生成
        画像サイズを変更する場合に使用することがほとんどかと。。。
        まだ未完成!!!!
        Parameters
        ----------
        $width  : リサイズ後の横のサイズ
        $height : リサイズ後の縦のサイズ
        */
        //元画像のサイズ
        $originai_width=$this->_width;
        $original_height=$this->_height;
        //まずは、変更先の画像のサイズで空画像を作成
        $make_image=imagecreatetruecolor($width,$height);
        //空の画像にオリジナルの画像を座標をあわせてのせる
        $make_result=imagecopyresized(
            $make_image,                //空の画像を指定
            $this->makeOriginal(),      //元画像を指定
            0,0,                        //空画像の左上の座標を指定
            $copy_width,$copy_height,   //コピー元の座標
            $width,$height,             //空画像の幅、高さ
            $origin_width,$origin_height//コピー元の幅、高さ
        );
        //保存先に保存を実行
        $output_image=imagejpeg(
            $make_image,
            NULL,
            100             //クオリティの度合いを指定。100が最高品質
        );
    }
}