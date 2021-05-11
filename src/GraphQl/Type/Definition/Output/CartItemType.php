<?php


namespace App\GraphQl\Type\Definition\Output;

use ApiPlatform\Core\GraphQl\Type\Definition\TypeInterface;
use App\GraphQl\Type\Definition\Uuid;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

final class CartItemType extends InterfaceType implements TypeInterface
{
    public function __construct(array $config, bool $output)
    {
        $config['name'] = ($output ? 'Output' : 'Input') . $this->tryInferName();

        parent::__construct($config + [
            'fields' => [
                'id' => Uuid::createNamedType(($output ? 'Output' : 'Input') . 'CartItemId'),
                'product_id' => Type::int(),
                'variant_id' => Type::int(),
                'title' => Type::string()
            ]
        ]);
    }

    public static function create(bool $output)
    {
        return new self([], $output);
    }

    public function getName(): string
    {
        return $this->name;
    }
}