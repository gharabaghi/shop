<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211010125549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D39D86650F');
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D3DE18E50B');
        $this->addSql('DROP INDEX IDX_161498D3DE18E50B ON card');
        $this->addSql('DROP INDEX IDX_161498D39D86650F ON card');
        $this->addSql('ALTER TABLE card ADD user_id INT NOT NULL, ADD product_id INT NOT NULL, DROP user_id_id, DROP product_id_id');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D34584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_161498D3A76ED395 ON card (user_id)');
        $this->addSql('CREATE INDEX IDX_161498D34584665A ON card (product_id)');
        $this->addSql('ALTER TABLE product CHANGE visit visit INT NOT NULL, CHANGE count count INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D3A76ED395');
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D34584665A');
        $this->addSql('DROP INDEX IDX_161498D3A76ED395 ON card');
        $this->addSql('DROP INDEX IDX_161498D34584665A ON card');
        $this->addSql('ALTER TABLE card ADD user_id_id INT NOT NULL, ADD product_id_id INT NOT NULL, DROP user_id, DROP product_id');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D39D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D3DE18E50B FOREIGN KEY (product_id_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_161498D3DE18E50B ON card (product_id_id)');
        $this->addSql('CREATE INDEX IDX_161498D39D86650F ON card (user_id_id)');
        $this->addSql('ALTER TABLE product CHANGE visit visit INT NOT NULL, CHANGE count count INT NOT NULL');
    }
}
