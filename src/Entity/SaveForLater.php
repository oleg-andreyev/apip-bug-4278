<?php declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use App\GraphQl\Resolver\CreateSaveForLaterFromCartMutationResolver;
use Ramsey\Uuid\Uuid;
use App\Model\Output\CreateSaveForLaterFromCartOutput;

/**
 * @ApiResource(
 *     graphql={
 *         "collection_query",
 *         "delete",
 *         "CreateFromCart": {
 *             "mutation": CreateSaveForLaterFromCartMutationResolver::class,
 *             "description": "Creating Save For Loter from Cart Object",
 *             "read": false,
 *             "write": false,
 *             "output": CreateSaveForLaterFromCartOutput::class,
 *             "args": {
 *                 "cart": { "type": "InputCart!" },
 *                 "item_id": { "type": "TargetItemId!" }
 *             }
 *         }
 *     }
 * )
 * @Entity()
 */
class SaveForLater
{
    /**
     * @ApiProperty(identifier=true)
     * @ORM\Id()
     * @ORM\Column(type="string")
     * @ORM\GeneratedValue()
     *
     * @var string
     */
    public $id;

    /**
     * @ApiProperty(required=true)
     * @ORM\Column(type="string", nullable=false)
     *
     * @var string
     */
    public $title = 'bbbb';

    public function __construct()
    {
        $this->id = Uuid::uuid1()->toString();
        $this->title = 'foobar';
    }
}