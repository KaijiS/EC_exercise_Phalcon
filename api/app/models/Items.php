<?php

use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\StringLength as StringLengthValidator;

class Items extends \Phalcon\Mvc\Model
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
    public $description;

    /**
     *
     * @var integer
     */
    public $price;

    /**
     *
     * @var string
     */
    public $mime;

    /**
     *
     * @var string
     */
    public $raw_data;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("EC");
        $this->setSource("items");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'items';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Items[]|Items|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Items|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }


    public function validation()
    {

        $validator = new Validation();

        // $validator->add(
        //     'name',
        //     new Uniqueness(
        //         [
        //             'message' => 'The item name must be unique',
        //         ]
        //     )
        // );

        
        $validator->add(
            [
                "name",
                "description",
            ],
            new StringLengthValidator(
                [
                    "max" => [
                        "name"  => 5,
                        "description" => 5,
                    ],
                    "min" => [
                        "name"  => 1,
                        "description"  => 1,
                    ],
                    "messageMaximum" => [
                        "name"  => "Name is longer than 100 characters",
                        "description" => "Description is longer than 100 characters",
                    ],
                    "messageMinimum" => [
                        "name"  => "Name must be at least 1 character",
                        "description" => "Description must be at least 1 character",
                    ]
                ]
            )
        );
        return $this->validate($validator);
    }

}
