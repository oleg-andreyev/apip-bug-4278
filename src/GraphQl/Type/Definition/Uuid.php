<?php

namespace App\GraphQl\Type\Definition;

use ApiPlatform\Core\GraphQl\Type\Definition\TypeInterface;
use GraphQL\Error\Error;
use GraphQL\Language\AST\IntValueNode;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;

class Uuid extends ScalarType implements TypeInterface
{
    public function serialize($value)
    {
        $canCast = is_string($value)
            || is_int($value)
            || (is_object($value) && method_exists($value, '__toString'));

        if (! $canCast) {
            throw new Error('ID cannot represent value: ' . Utils::printSafe($value));
        }

        return (string) $value;
    }

    public function parseValue($value)
    {
        if (!is_string($value)) {
            throw new Error('ID cannot represent value: ' . Utils::printSafe($value));
        }

        if (!\Ramsey\Uuid\Uuid::isValid($value)) {
            throw new Error('ID cannot represent value: ' . Utils::printSafe($value));
        }

        return (string) $value;
    }

    public function parseLiteral(Node $valueNode, ?array $variables = null)
    {
        if ($valueNode instanceof StringValueNode || $valueNode instanceof IntValueNode) {
            return $valueNode->value;
        }

        // Intentionally without message, as all information already in wrapped Exception
        throw new Error();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public static function createNamedType(string $name)
    {
        return new self(['name' => $name]);
    }
}