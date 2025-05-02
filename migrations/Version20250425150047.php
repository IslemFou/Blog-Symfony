<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250425150047 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE users DROP FOREIGN KEY FK_1483A5E97294869C
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_1483A5E97294869C ON users
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users DROP article_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE users ADD article_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users ADD CONSTRAINT FK_1483A5E97294869C FOREIGN KEY (article_id) REFERENCES articles (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1483A5E97294869C ON users (article_id)
        SQL);
    }
}
