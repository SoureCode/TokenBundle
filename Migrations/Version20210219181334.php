<?php

declare(strict_types=1);

namespace SoureCode\Bundle\Token\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210219181334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add data field';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE token ADD data VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE token DROP data');
    }
}
