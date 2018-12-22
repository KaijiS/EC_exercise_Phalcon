<?php

// namespace EC\Products;
namespace App\Models;

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\InclusionIn;

class Items extends Model
{
    protected $_id;
    protected $_name;
    protected $_description;
    protected $_price;
    protected $_mime;
    protected $_raw_data;

    /* ------ ここからセッターメソッド -----*/
    public function setName($name)
    {
        // 名前の長さをチェック
        if (strlen($name) < 10) {
            throw new \InvalidArgumentException('名前が短すぎます');
        }
        $this->_name = $name;
        return $this;
    }

    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }

    public function setPrice($price)
    {
        $this->_price = $price;
        return $this;
    }

    public function setMime($mime)
    {
        $this->_mime = $mime;
        return $this;
    }

    public function setRawData($raw_data)
    {
        $this->_raw_data = $raw_data;
        return $this;
    }

    /* ------ ここからゲッターメソッド -----*/
    public function getName($name)
    {
        return $this->_name;
    }

    public function getDescription($description)
    {
        return $this->_description;
    }

    public function getPrice($price)
    {
        return $this->_price;
    }

    public function getMime($mime)
    {
        return $this->_mime;
    }

    public function getRawData($raw_data)
    {
        return $this->_raw_data;
    }



    public function getSource()
    {
        return "items";
    }

    public function initialize()
    {
        $this->setSource("items");
    }


    public function validation()
    {
        $validator = new Validation();

        // $validator->add(
        //     'type',
        //     new InclusionIn(
        //         [
        //             'domain' => [
        //                 'Mechanical',
        //                 'Virtual',
        //                 'Droid',
        //             ]
        //         ]
        //     )
        // );

        $validator->add(
            'name',
            new Uniqueness(
                [
                    'message' => 'The item name must be unique',
                ]
            )
        );        

        return $this->validate($validator);
    }
}