<?php


namespace App\GraphQl\Type\Definition\Input;

use ApiPlatform\Core\GraphQl\Type\Definition\TypeInterface;
use ApiPlatform\Core\GraphQl\Type\TypesContainerInterface;
use App\GraphQl\Type\Definition\Uuid;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class CartItemType extends InputObjectType implements TypeInterface
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
            'id' => $this->typesContainer->get('InputCartItemId'),
            'title' => Type::string()
        ];

        return parent::getFields();
    }

    public function getName(): string
    {
        return $this->name;
    }
}