<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181218210649 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE messages (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_messages (user_id INT NOT NULL, message_id INT NOT NULL, INDEX IDX_A043354CA76ED395 (user_id), INDEX IDX_A043354C537A1329 (message_id), PRIMARY KEY(user_id, message_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE users_messages ADD CONSTRAINT FK_A043354CA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users_messages ADD CONSTRAINT FK_A043354C537A1329 FOREIGN KEY (message_id) REFERENCES messages (id)');
        $this->addSql('ALTER TABLE offers CHANGE price price NUMERIC(10, 0) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users_messages DROP FOREIGN KEY FK_A043354C537A1329');
        $this->addSql('DROP TABLE messages');
        $this->addSql('DROP TABLE users_messages');
        $this->addSql('ALTER TABLE offers CHANGE price price NUMERIC(10, 2) DEFAULT NULL');
    }
}
