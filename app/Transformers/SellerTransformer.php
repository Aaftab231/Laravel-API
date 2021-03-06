<?php

namespace App\Transformers;

use App\Seller;
use League\Fractal\TransformerAbstract;

class SellerTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Seller $seller)
    {
        return [
            "identifier"=>(int)$seller->id,
            "name"=>(string)$seller->name,
            "email"=>(string)$seller->email,
            "isverified"=>(int)$seller->verified,
            "creationDate"=>$seller->created_at,
            "lastChange"=>$seller->updated_at,
            "deleteDate"=>isset($seller->deleted_at) ? (string)$seller->deleted_at:null,
        ];
    }
}
