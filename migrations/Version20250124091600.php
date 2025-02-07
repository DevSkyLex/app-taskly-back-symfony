<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250124091600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project_member (member_id UUID NOT NULL, project_id UUID NOT NULL, role VARCHAR(32) DEFAULT NULL, PRIMARY KEY(member_id, project_id))');
        $this->addSql('CREATE INDEX IDX_674011327597D3FE ON project_member (member_id)');
        $this->addSql('CREATE INDEX IDX_67401132166D1F9C ON project_member (project_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_USER_PROJECT ON project_member (member_id, project_id)');
        $this->addSql('COMMENT ON COLUMN project_member.member_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN project_member.project_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE project_member ADD CONSTRAINT FK_674011327597D3FE FOREIGN KEY (member_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_member ADD CONSTRAINT FK_67401132166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE project_member DROP CONSTRAINT FK_674011327597D3FE');
        $this->addSql('ALTER TABLE project_member DROP CONSTRAINT FK_67401132166D1F9C');
        $this->addSql('DROP TABLE project_member');
        $this->addSql('ALTER TABLE "user" DROP created_at');
        $this->addSql('ALTER TABLE "user" DROP updated_at');
        $this->addSql('ALTER TABLE "user" DROP deleted_at');
    }
}
