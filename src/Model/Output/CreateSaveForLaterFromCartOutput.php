<?php

namespace App\Model\Output;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use Ramsey\Uuid\Uuid;

class CreateSaveForLaterFromCartOutput implements DataTransformerInterface
{
    public function transform($object, string $to, array $context = [])
    {
        return [
            'Cart' => [
                'id' => Uuid::uuid1()->toString()
            ],
            'SaveForLater' => [
                $object
            ]
        ];
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return isset($context['resolver_context']);
    }
}