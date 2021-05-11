<?php declare(strict_types=1);

namespace App\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200101000000 extends AbstractMigration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !==
            'mysql', 'Migration can only be executed safely on \'mysql\'.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !==
            'mysql', 'Migration can only be executed safely on \'mysql\'.'
        );
    }
}
