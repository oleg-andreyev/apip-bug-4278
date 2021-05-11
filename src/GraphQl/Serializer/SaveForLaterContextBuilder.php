<?php

namespace App\GraphQl\Serializer;

use ApiPlatform\Core\GraphQl\Serializer\SerializerContextBuilderInterface;
use App\Entity\SaveForLater;

class SaveForLaterContextBuilder implements SerializerContextBuilderInterface
{
    /**
     * @var SerializerContextBuilderInterface
     */
    private $decorated;

    public function __construct(SerializerContextBuilderInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function create(string $resourceClass, string $operationName, array $resolverContext, bool $normalization): array
    {
        $context = $this->decorated->create($resourceClass, $operationName, $resolverContext, $normalization);

        if ($resourceClass === SaveForLater::class && true === $normalization) {
            $context['resolver_context'] = $resolverContext;
        }

        return $context;
    }
}