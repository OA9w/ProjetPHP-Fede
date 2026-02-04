<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260131081352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact (id SERIAL NOT NULL, first_name VARCHAR(120) NOT NULL, last_name VARCHAR(120) NOT NULL, phone VARCHAR(40) NOT NULL, email VARCHAR(180) DEFAULT NULL, photo_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE contact_group (contact_id INT NOT NULL, group_id INT NOT NULL, PRIMARY KEY(contact_id, group_id))');
        $this->addSql('CREATE INDEX IDX_40EA54CAE7A1254A ON contact_group (contact_id)');
        $this->addSql('CREATE INDEX IDX_40EA54CAFE54D947 ON contact_group (group_id)');
        $this->addSql('CREATE TABLE contact_field (id SERIAL NOT NULL, contact_id INT NOT NULL, name VARCHAR(120) NOT NULL, value TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_76DF5557E7A1254A ON contact_field (contact_id)');
        $this->addSql('CREATE INDEX idx_contact_field_name ON contact_field (name)');
        $this->addSql('CREATE TABLE "group" (id SERIAL NOT NULL, name VARCHAR(120) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_group_name ON "group" (name)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE contact_group ADD CONSTRAINT FK_40EA54CAE7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE contact_group ADD CONSTRAINT FK_40EA54CAFE54D947 FOREIGN KEY (group_id) REFERENCES "group" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE contact_field ADD CONSTRAINT FK_76DF5557E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE contact_group DROP CONSTRAINT FK_40EA54CAE7A1254A');
        $this->addSql('ALTER TABLE contact_group DROP CONSTRAINT FK_40EA54CAFE54D947');
        $this->addSql('ALTER TABLE contact_field DROP CONSTRAINT FK_76DF5557E7A1254A');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE contact_group');
        $this->addSql('DROP TABLE contact_field');
        $this->addSql('DROP TABLE "group"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
