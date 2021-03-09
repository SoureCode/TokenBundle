<?php

namespace SoureCode\Bundle\Token\Tests\Mock\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210212170026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial resource table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE resource_mock_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE resource_mock (id INT NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE resource_mock_id_seq CASCADE');
        $this->addSql('DROP TABLE resource_mock');
    }
}
