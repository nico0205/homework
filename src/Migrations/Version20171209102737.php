<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 09/12/17
 * Time: 09:21
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171209102737 extends AbstractMigration
{
    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.'
        );

        $this->addSql('CREATE SCHEMA account');
        $this->addSql('CREATE SCHEMA person');
        $this->addSql('CREATE SCHEMA cms');
        $this->addSql('CREATE SEQUENCE account.user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE person.person_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE cms.article_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(
            'CREATE TABLE account."user" (id INT NOT NULL, person_id INT DEFAULT NULL, username VARCHAR(255) NOT NULL, roles TEXT NOT NULL, password VARCHAR(255) NOT NULL, enabled BOOLEAN NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C5B2989CF85E0677 ON account."user" (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C5B2989C217BBB47 ON account."user" (person_id)');
        $this->addSql('COMMENT ON COLUMN account."user".roles IS \'(DC2Type:array)\'');
        $this->addSql(
            'CREATE TABLE person.person (id INT NOT NULL, name VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, middlename VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE TABLE cms.article (id INT NOT NULL, title VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'ALTER TABLE account."user" ADD CONSTRAINT FK_C5B2989C217BBB47 FOREIGN KEY (person_id) REFERENCES person.person (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.'
        );

        $this->addSql('ALTER TABLE account."user" DROP CONSTRAINT FK_C5B2989C217BBB47');
        $this->addSql('DROP SEQUENCE account.user_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE person.person_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE cms.article_id_seq CASCADE');
        $this->addSql('DROP TABLE account."user"');
        $this->addSql('DROP TABLE person.person');
        $this->addSql('DROP TABLE cms.article');
    }
}
