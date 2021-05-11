<?php

namespace App\GraphQl\Resolver;

use ApiPlatform\Core\GraphQl\Resolver\MutationResolverInterface;
use App\Entity\SaveForLater;

final class CreateSaveForLaterFromCartMutationResolver implements MutationResolverInterface
{
    /**
     * @param SaveForLater|null $item
     *
     * @return SaveForLater
     */
    public function __invoke($item, array $context)
    {
        // Query arguments are in $context['args'].

        // Do something with the book.
        // Or fetch the book if it has not been retrieved.

        return $item;
    }
}