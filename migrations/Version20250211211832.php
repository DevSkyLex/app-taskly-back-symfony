<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250211211832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project_invitation (id UUID NOT NULL, invited_id UUID NOT NULL, sender_id UUID NOT NULL, project_id UUID NOT NULL, status VARCHAR(255) NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E9BB1A90C2ED4747 ON project_invitation (invited_id)');
        $this->addSql('CREATE INDEX IDX_E9BB1A90F624B39D ON project_invitation (sender_id)');
        $this->addSql('CREATE INDEX IDX_E9BB1A90166D1F9C ON project_invitation (project_id)');
        $this->addSql('COMMENT ON COLUMN project_invitation.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN project_invitation.invited_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN project_invitation.sender_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN project_invitation.project_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN project_invitation.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE project_invitation ADD CONSTRAINT FK_E9BB1A90C2ED4747 FOREIGN KEY (invited_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_invitation ADD CONSTRAINT FK_E9BB1A90F624B39D FOREIGN KEY (sender_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_invitation ADD CONSTRAINT FK_E9BB1A90166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE project_invitation DROP CONSTRAINT FK_E9BB1A90C2ED4747');
        $this->addSql('ALTER TABLE project_invitation DROP CONSTRAINT FK_E9BB1A90F624B39D');
        $this->addSql('ALTER TABLE project_invitation DROP CONSTRAINT FK_E9BB1A90166D1F9C');
        $this->addSql('DROP TABLE project_invitation');
    }
}
