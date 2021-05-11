<?php


namespace App\GraphQl\Type\Definition\Interfaces;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class MutationResponse extends InterfaceType
{
    public function __construct()
    {
        parent::__construct([
            'fields' => [
                'code' => Type::nonNull(Type::int()),
                'success' => Type::nonNull(Type::boolean()),
                'message' => Type::nonNull(Type::string()),
            ]
        ]);
    }
}