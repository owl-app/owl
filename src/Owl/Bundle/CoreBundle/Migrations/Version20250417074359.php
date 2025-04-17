<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250417074359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE owl_invoice CHANGE issue_date issue_date DATE NOT NULL, CHANGE transaction_date transaction_date DATE NOT NULL, CHANGE due_payment_date due_payment_date DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE owl_invoice CHANGE issue_date issue_date DATETIME NOT NULL, CHANGE transaction_date transaction_date DATETIME NOT NULL, CHANGE due_payment_date due_payment_date DATETIME NOT NULL');
    }
}
