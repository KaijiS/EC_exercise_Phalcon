# EC_exercise  / api


### 下記の情報を持つ商品データの登録・検索・変更・削除を行う認証機能つきRESTfulなAPIの実装   

- 商品タイトル
- 説明文
- 価格
- 商品画像

レスポンス : json形式  
登録，変更時に渡すデータ : json形式  
画像のやり取りはBase64形式でjsonの要素とする
認証方法はトークン認証



## 使用した技術
- 言語 : PHP 7.2.1
- フレームワーク : Phalcon
- データベース : MySQL  
- トークン仕様 : JWT

使用したライブラリ ("library/php_jwt/"の中)  
https://github.com/carloscgo/Phalcon-JWT/tree/master/app/library/vendor/firebase/php-jwt


## テーブル構成
### Items

|           |名前        |データ型      |照合順序        | 属性     | デフォルト値|           その他|  
|-----------|-----------|-------------|---------------|---------| ----------|---------------|  
|         id|         id|     int(100)|               | unsigned|        なし| AUTO_INCREMENT| 
| 商品タイトル|       name| varchar(100)|utf8_unicode_ci| collate?|        なし|               |  
|   商品の説明|description| varchar(500)|utf8_unicode_ci| collate?|        なし|               |  
|        価格|      price|   bigint(20)|               | unsigned|        なし|               | 
|     商品画像|      mime|   varchar(64)|utf8_unicode_ci|         |       NULL|               | 
|     商品画像|      raw |      longblob|               |         |       NULL|               |  

  
### Users

|               |名前      |データ型      |照合順序        | 属性     | デフォルト値|           その他|  
|---------------|---------|-------------|---------------|---------| ----------|---------------|  
|             id|       id|     int(100)|               | unsigned|        なし| AUTO_INCREMENT| 
|        ユーザ名|      name| varchar(100)|utf8_unicode_ci| collate?|        なし|               |  
|使用した外部OAuth|oauth_name|varchar(100)|utf8_unicode_ci| collate?|        なし|               |  
|その外部OAuthのID| oauth_id|     int(100)|               | unsigned|        なし|               | 


## APIのURL


|      |URL                  |処理                                                   |  
|------|---------------------|------------------------------------------------------|  
|   GET|           /api/items|一覧表示:                                   商品一覧を表示|  
|   GET|         /api/items/2|情報表示:                              idが2番の情報を表示| 
|   GET|/api/items/search/Pen|検索   :                   'Pen'を含む名前 の商品情報を検索| 
|  POST|           /api/items|登録   :                  商品情報を追加(ログインユーザのみ)|  
|   PUT|         /api/items/2|変更   : プライマリーキーが2の商品情報を更新(ログインユーザのみ)|  
|DELETE|         /api/items/2|削除   : プライマリーキーが2の商品情報を削除(ログインユーザのみ)|  
|  POST|           /users/jwt|取得   :                               JWTを発行してもらう|  
  

POSTやPUT 時の渡すjsonデータ　: '{"name":[商品名], "description":[説明文], "price":[価格], "img":[画像のバイナリ(base64)]}'

例) Curlをshellで叩いた時 (追加時)

```curl.sh
curl=`cat <<EOS
curl
 -i
 -X POST
 -H 'Content-Type: application/json'
 -H 'Authorization: Bearer [エンコードされたJWT]'
 http://localhost/EC_exercise_Phalcon/
 -d '{"name":"Pen", "description":"This is a pen", "price":108, "img":null}' 
eval ${curl}
```


基本的なResponsのJson形式

例) "o"で検索した時 

```response.json
{
    "status-code": 200,
    "status": "FOUND",
    "data": [
        {
            "id": "29",
            "name": "Note",
            "price": "216"
        },
        {
            "id": "32",
            "name": "Text Book",
            "price": "1080"
        }
    ],
    "message": null
}
```

※`"message"`はエラーメッセージなどが内容として入っており、`"data"`と同様に複数入る





