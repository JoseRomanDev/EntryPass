<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migración inicial consolidada — crea el esquema completo desde cero.
 * Reemplaza las migraciones parciales y desordenadas anteriores.
 */
final class Version20260310000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crea el esquema completo: events, users, purchases, tickets.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE events (title VARCHAR(255) NOT NULL, description TEXT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, price DOUBLE PRECISION NOT NULL, capacity INT NOT NULL, category VARCHAR(50) DEFAULT NULL, status BOOLEAN NOT NULL, id VARCHAR(36) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE users (email VARCHAR(180) NOT NULL, name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, id VARCHAR(36) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE TABLE purchases (quantity INT NOT NULL, total_price DOUBLE PRECISION NOT NULL, status VARCHAR(20) NOT NULL, purchased_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, id VARCHAR(36) NOT NULL, user_id VARCHAR(36) NOT NULL, event_id VARCHAR(36) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_AA6431FEA76ED395 ON purchases (user_id)');
        $this->addSql('CREATE INDEX IDX_AA6431FE71F7E88B ON purchases (event_id)');
        $this->addSql('ALTER TABLE purchases ADD CONSTRAINT FK_AA6431FEA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE purchases ADD CONSTRAINT FK_AA6431FE71F7E88B FOREIGN KEY (event_id) REFERENCES events (id)');
        $this->addSql('CREATE TABLE tickets (qr_code_hash VARCHAR(255) NOT NULL, is_used BOOLEAN NOT NULL, scanned_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, id VARCHAR(36) NOT NULL, purchase_id VARCHAR(36) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_54469DF42B3121D4 ON tickets (qr_code_hash)');
        $this->addSql('CREATE INDEX IDX_54469DF4558FBEB9 ON tickets (purchase_id)');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_54469DF4558FBEB9 FOREIGN KEY (purchase_id) REFERENCES purchases (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tickets DROP CONSTRAINT FK_54469DF4558FBEB9');
        $this->addSql('ALTER TABLE purchases DROP CONSTRAINT FK_AA6431FEA76ED395');
        $this->addSql('ALTER TABLE purchases DROP CONSTRAINT FK_AA6431FE71F7E88B');
        $this->addSql('DROP TABLE tickets');
        $this->addSql('DROP TABLE purchases');
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP TABLE users');
    }
}
