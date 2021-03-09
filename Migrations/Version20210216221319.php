<?php

declare(strict_types=1);

namespace SoureCode\Bundle\Token\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210216221319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add token';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE token (id INT NOT NULL, type VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, resource_type VARCHAR(255) NOT NULL, resource_id INT NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE token_id_seq CASCADE');
        $this->addSql('DROP TABLE token');
    }
}
