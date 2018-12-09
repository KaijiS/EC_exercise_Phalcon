<!DOCTYPE html>
<html>
<body>

<?php

$base_url = 'http://localhost/EC_exercise';

# 画像の処理
$raw_data = file_get_contents("./../sample_img/demo.png");
$raw_data = base64_encode($raw_data); // 画像バイナリをBase64形式に変換

// 商品の追加または商品情報の変更時のための情報
$insert_data = array(
    'name' => 'Pen',
    'description' => "This is a Pen",
    'price' => 100,
    'img' => $raw_data,
);

// 使用する操作以外をコマンとアウト
// $response = Show($base_url,3);               // 指定したIDの商品データを取得する
// $response = Search($base_url,'P');           // 商品を検索する
// $response= Insert($base_url,$insert_data);   // 商品を追加する
// $response = Edit($base_url,$insert_data,1); // 商品情報を編集する
// $response = Delete($base_url,26);            // 商品情報を削除する


// レスポンス確認用で表示
echo $response;
echo "<br>";


// 結果はjson形式で返されるのでデコードする
$result = json_decode($response,true);
echo $result;
echo $result['status'];
echo "<br>";
echo $result['data']['id'];
echo "<br>";

echo "<br>";
echo "<br>";
// 画像はBase64形式のままで表示可能
echo ('<img src="data:'.$result['data']['mime'].';base64,'.($result['data']['raw_data']).'">');




?>

</body>
</html>

<?php
function Show($base_url,$id){
/*
指定したIDの商品データを取得する

Parameters
----------
string $base_url : base_url
int    $id       : 指定するid

Return
----------
json $response : RESTfulAPIからのレスポンス
*/
    $response = file_get_contents($base_url.'/api/items/'.strval($id));
    return $response;
}
?>

<?php
function Search($base_url,$key){
/*
商品を検索する

Parameters
----------
string $base_url : base_url
string $key      : 検索キーワード

Return
----------
json $response : RESTfulAPIからのレスポンス
*/
    $response = file_get_contents($base_url.'/api/items/search/'.$key);
    return $response;
}
?>

<?php
function Insert($base_url,$data){
/*
商品を追加する

Parameters
----------
string $base_url : base_url
json   $data     : 追加する商品情報を要素として持つjson

Return
----------
json $response : RESTfulAPIからのレスポンス
*/
    $context = stream_context_create(
        array(
            'http' => array(
                'method'=> 'POST',
                'header'=> 'Content-type: application/json; charset=UTF-8',
                'content' => json_encode(
                    $data
                )
            )
        )
    );

    $response = file_get_contents($base_url.'/api/items', false, $context);

    return $response;
};
?>

<?php
function Edit($base_url,$insert_data,$id){
/*
商品情報を編集する

Parameters
----------
string $base_url    : base_url
json   $insert_data : 編集後のする商品情報を要素として持つjson
int    $id          : 指定するid

Return
----------
json $response : RESTfulAPIからのレスポンス
*/
    $context = stream_context_create(
        array(
            'http' => array(
                'method'=> 'PUT',
                'header'=> 'Content-type: application/json; charset=UTF-8',
                'content' => json_encode(
                    $insert_data
                )
            )
        )
    );
    
    file_get_contents($base_url.'/api/items/'.strval($id), false, $context);
}
?>

<?php
function Delete($base_url,$id){
/*
商品情報を削除する

Parameters
----------
string $base_url : base_url
int    $id       : 指定するid

Return
----------
json $response : RESTfulAPIからのレスポンス
*/
    $context = stream_context_create(
        array(
            'http' => array(
                'method'=> 'DELETE'
            )
        )
    );
    
    $response = file_get_contents($base_url.'/api/items/'.strval($id) , false, $context);

    return $response;
};
?>