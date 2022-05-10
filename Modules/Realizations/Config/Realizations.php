<?php

namespace App\Modules\Realizations\Config;

use CodeIgniter\Config\BaseConfig;

class Realizations extends BaseConfig
{
    /**
     * Tables (Database table names)
     *
     * @var array
     */
    public $database = [
        'realizations'  => 'realizations',
        'categories'    => 'realizations_categories',
        'images'        => 'realizations_images',
        'offers'        => 'realizations_offers',
    ];

    /**
     * Image attribute settings
     * 
     * @var array
     */
    public $image = [
        'fileLocation'  => 'uploads/realizations',
        'saveOrginal'   => true,
        'maxWeight'     => 4000, //kb
        'defaultType'   => 'png',
        'default'       => [
            'png'       => [
                'base64' => "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"
            ]
        ]
    ];
}
