<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250212113656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_invitation DROP CONSTRAINT project_invitation_pkey');
        $this->addSql('ALTER TABLE project_invitation DROP id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_PROJECT_INVITED ON project_invitation (project_id, invited_id)');
        $this->addSql('ALTER TABLE project_invitation ADD PRIMARY KEY (invited_id, sender_id, project_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_PROJECT_INVITED');
        $this->addSql('DROP INDEX project_invitation_pkey');
        $this->addSql('ALTER TABLE project_invitation ADD id UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN project_invitation.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE project_invitation ADD PRIMARY KEY (id)');
    }
}
