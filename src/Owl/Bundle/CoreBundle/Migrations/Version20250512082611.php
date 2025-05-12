<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250512082611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE owl_company ADD currency_id INT NOT NULL');
        $this->addSql('ALTER TABLE owl_company ADD CONSTRAINT FK_58BC081838248176 FOREIGN KEY (currency_id) REFERENCES owl_currency (id)');
        $this->addSql('CREATE INDEX IDX_58BC081838248176 ON owl_company (currency_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE owl_company DROP FOREIGN KEY FK_58BC081838248176');
        $this->addSql('DROP INDEX IDX_58BC081838248176 ON owl_company');
        $this->addSql('ALTER TABLE owl_company DROP currency_id');
    }
}
