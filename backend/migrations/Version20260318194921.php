<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260318194921 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE purchases (quantity INT NOT NULL, total_price DOUBLE PRECISION NOT NULL, status VARCHAR(20) NOT NULL, purchased_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, id VARCHAR(36) NOT NULL, user_id VARCHAR(36) NOT NULL, event_id VARCHAR(36) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_AA6431FEA76ED395 ON purchases (user_id)');
        $this->addSql('CREATE INDEX IDX_AA6431FE71F7E88B ON purchases (event_id)');
        $this->addSql('ALTER TABLE purchases ADD CONSTRAINT FK_AA6431FEA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE purchases ADD CONSTRAINT FK_AA6431FE71F7E88B FOREIGN KEY (event_id) REFERENCES events (id)');
        $this->addSql('ALTER TABLE tickets DROP CONSTRAINT fk_54469df4a76ed395');
        $this->addSql('ALTER TABLE tickets DROP CONSTRAINT fk_54469df471f7e88b');
        $this->addSql('DROP INDEX idx_54469df4a76ed395');
        $this->addSql('DROP INDEX idx_54469df471f7e88b');
        $this->addSql('ALTER TABLE tickets ADD purchase_id VARCHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE tickets DROP purchased_at');
        $this->addSql('ALTER TABLE tickets DROP user_id');
        $this->addSql('ALTER TABLE tickets DROP event_id');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_54469DF4558FBEB9 FOREIGN KEY (purchase_id) REFERENCES purchases (id)');
        $this->addSql('CREATE INDEX IDX_54469DF4558FBEB9 ON tickets (purchase_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchases DROP CONSTRAINT FK_AA6431FEA76ED395');
        $this->addSql('ALTER TABLE purchases DROP CONSTRAINT FK_AA6431FE71F7E88B');
        $this->addSql('DROP TABLE purchases');
        $this->addSql('ALTER TABLE tickets DROP CONSTRAINT FK_54469DF4558FBEB9');
        $this->addSql('DROP INDEX IDX_54469DF4558FBEB9');
        $this->addSql('ALTER TABLE tickets ADD purchased_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE tickets ADD event_id VARCHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE tickets RENAME COLUMN purchase_id TO user_id');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT fk_54469df4a76ed395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT fk_54469df471f7e88b FOREIGN KEY (event_id) REFERENCES events (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_54469df4a76ed395 ON tickets (user_id)');
        $this->addSql('CREATE INDEX idx_54469df471f7e88b ON tickets (event_id)');
    }
}
