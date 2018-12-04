<?php

namespace EC\Products;

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\InclusionIn;

class Items extends Model
{
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