<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221211081230 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'First migration - use either doctrine:migrations:migrate or doctrine:schema:create';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE notification (id VARCHAR(36) NOT NULL, content CLOB NOT NULL,
            language VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, send_success_count INTEGER NOT NULL,
            send_fail_count INTEGER NOT NULL, created_at DATETIME NOT NULL, processing_started_at DATETIME DEFAULT NULL,
            processed_at DATETIME DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE recipient (id VARCHAR(36) NOT NULL,
            preferred_language VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE recipient_contact_channel (id VARCHAR(36) NOT NULL,
            recipient_id VARCHAR(36) DEFAULT NULL, channel VARCHAR(255) NOT NULL,
            address VARCHAR(255) NOT NULL, PRIMARY KEY(id),
            CONSTRAINT FK_6D7BBACFE92F8F78 FOREIGN KEY (recipient_id) REFERENCES recipient (id)
            ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D7BBACFA2F98E47 ON recipient_contact_channel (channel)');
        $this->addSql('CREATE INDEX IDX_6D7BBACFE92F8F78 ON recipient_contact_channel (recipient_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE recipient');
        $this->addSql('DROP TABLE recipient_contact_channel');
    }
}
