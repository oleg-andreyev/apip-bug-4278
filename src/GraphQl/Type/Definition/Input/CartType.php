<?php

namespace App\GraphQl\Type\Definition\Input;

use ApiPlatform\Core\GraphQl\Type\Definition\TypeInterface;
use App\GraphQl\Type\Definition\Uuid;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

final class CartType extends InputObjectType implements TypeInterface
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Input' . $this->tryInferName(),
            'fields' => [
                'id' => Uuid::createNamedType('InputCartId'),
                'items' => [
                    'type' => Type::listOf(CartItemType::create()),
                ]
            ]
        ]);
    }

    public function getName(): string
    {
        return 'InputCart';
    }
}