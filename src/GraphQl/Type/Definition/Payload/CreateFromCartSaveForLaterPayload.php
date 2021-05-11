<?php


namespace App\GraphQl\Type\Definition\Payload;


use ApiPlatform\Core\GraphQl\Type\Definition\TypeInterface;
use ApiPlatform\Core\GraphQl\Type\TypesContainerInterface;
use App\GraphQl\Type\Definition\Interfaces\MutationResponse;
use App\GraphQl\Type\Definition\Output\CartType as OutputCartType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CreateFromCartSaveForLaterPayload extends ObjectType implements TypeInterface
{
    /**
     * @var TypesContainerInterface
     */
    private $typesContainer;
    private $fieldsInitilized = false;

    public function __construct(TypesContainerInterface $typesContainer)
    {
        parent::__construct([
            'interfaces' => [
                function () {
                    return new MutationResponse();
                }
            ]
        ]);
        $this->typesContainer = $typesContainer;
    }

    public function getFields(): array
    {
        if ($this->fieldsInitilized) {
            return parent::getFields();
        }

        $interfaces = $this->getInterfaces();
        $fields = array_merge(...array_map(static function (InterfaceType $type) {
            return $type->getFields();
        }, $interfaces));

        $this->config['fields'] = $fields + [
            'Cart' => $this->typesContainer->get('OutputCart'),
            'SaveForLater' => Type::listOf($this->typesContainer->get('SaveForLater'))
        ];

        $this->fieldsInitilized = true;

        return parent::getFields();
    }

    public function getName(): string
    {
        return 'CreateFromCartSaveForLaterPayload';
    }
}