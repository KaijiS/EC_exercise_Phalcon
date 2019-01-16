<?php

use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\StringLength as StringLengthValidator;

class Users extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $oauth_name;

    /**
     *
     * @var string
     */
    public $access_token;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("EC");
        $this->setSource("users");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'users';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Users[]|Users|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Users|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }



    /**
     * 
     * バリデーションの設定
     */
    public function validation()
    {

        $validator = new Validation();

        // 同じユーザー名は登録できない
        $validator->add(
            [
                'name',
                'access_token'
            ],
            new Uniqueness(
                [
                    "message" => [
                        "name"  => 'The user name must be unique',
                        "access_token" => 'The AccessToken must be unique',
                    ]
                ]
            )
        );

        return $this->validate($validator);
    }

}
