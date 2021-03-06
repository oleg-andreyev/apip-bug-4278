<?php


namespace App\GraphQl\Type\Definition\Output;

use ApiPlatform\Core\GraphQl\Type\Definition\TypeInterface;
use ApiPlatform\Core\GraphQl\Type\TypesContainerInterface;
use App\GraphQl\Type\Definition\Uuid;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

final class CartItemType extends InterfaceType implements TypeInterface
{
    /**
     * @var TypesContainerInterface
     */
    private $typesContainer;

    public function __construct(array $config, TypesContainerInterface $typesContainer)
    {
        parent::__construct($config);
        $this->typesContainer = $typesContainer;
    }

    public function getFields(): array
    {
        $this->config['fields'] = [
            'id' => $this->typesContainer->get($this->name . 'Id'),
            'product_id' => Type::int(),
            'variant_id' => Type::int(),
            'title' => Type::string()
        ];
        return parent::getFields(); // TODO: Change the autogenerated stub
    }

    public function getName(): string
    {
        return $this->name;
    }
}