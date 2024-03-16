<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240311100910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F36109D86650F');
        $this->addSql('DROP INDEX IDX_8C9F36109D86650F ON file');
        $this->addSql('ALTER TABLE file CHANGE user_id user_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36109D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8C9F36109D86650F ON file (user_id_id)');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_906517449D86650F');
        $this->addSql('DROP INDEX IDX_906517449D86650F ON invoice');
        $this->addSql('ALTER TABLE invoice CHANGE user_id user_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_906517449D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_906517449D86650F ON invoice (user_id_id)');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F36109D86650F');
        $this->addSql('DROP INDEX IDX_8C9F36109D86650F ON file');
        $this->addSql('ALTER TABLE file CHANGE user_id_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36109D86650F FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8C9F36109D86650F ON file (user_id)');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_906517449D86650F');
        $this->addSql('DROP INDEX IDX_906517449D86650F ON invoice');
        $this->addSql('ALTER TABLE invoice CHANGE user_id_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_906517449D86650F FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_906517449D86650F ON invoice (user_id)');
        $this->addSql('ALTER TABLE user DROP roles');
    }
}
