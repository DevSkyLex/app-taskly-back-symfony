<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250212131719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_invitation DROP CONSTRAINT project_invitation_pkey');
        $this->addSql('ALTER TABLE project_invitation ADD id UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN project_invitation.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE project_invitation ADD PRIMARY KEY (id)');
        $this->addSql('DROP INDEX uniq_user_project');
        $this->addSql('ALTER TABLE project_member DROP CONSTRAINT project_member_pkey');
        $this->addSql('ALTER TABLE project_member ADD id UUID NOT NULL');
        $this->addSql('ALTER TABLE project_member ALTER role SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN project_member.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE project_member ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX project_member_pkey');
        $this->addSql('ALTER TABLE project_member DROP id');
        $this->addSql('ALTER TABLE project_member ALTER role DROP NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_user_project ON project_member (member_id, project_id)');
        $this->addSql('ALTER TABLE project_member ADD PRIMARY KEY (member_id, project_id)');
        $this->addSql('DROP INDEX project_invitation_pkey');
        $this->addSql('ALTER TABLE project_invitation DROP id');
        $this->addSql('ALTER TABLE project_invitation ADD PRIMARY KEY (invited_id, sender_id, project_id)');
    }
}
