<?php


namespace App\GraphQl\Type\Definition\Input;

use ApiPlatform\Core\GraphQl\Type\Definition\TypeInterface;
use App\GraphQl\Type\Definition\Uuid;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class CartItemType extends InputObjectType implements TypeInterface
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Input' . $this->tryInferName(),
            'fields' => [
                'id' => Uuid::createNamedType('InputCartItemId'),
                'title' => Type::string()
            ]
        ]);
    }

    public static function create()
    {
        return new self();
    }

    public function getName(): string
    {
        return 'InputCartItem';
    }
}