<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250615090821 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE guide_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE guide_unavailability_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE log_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE place_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE rating_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE refund_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE visitor_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE category (id INT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(100) NOT NULL, description TEXT DEFAULT NULL, icon VARCHAR(50) DEFAULT NULL, position INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_64C19C1727ACA70 ON category (parent_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE guide_unavailability (id INT NOT NULL, guide_id INT NOT NULL, date DATE NOT NULL, reason TEXT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DF4659DBD7ED1D4B ON guide_unavailability (guide_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE log (id INT NOT NULL, user_id INT DEFAULT NULL, action VARCHAR(50) NOT NULL, entity VARCHAR(50) NOT NULL, entity_id INT DEFAULT NULL, data JSON NOT NULL, ip_address VARCHAR(45) DEFAULT NULL, user_agent VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8F3F68C5A76ED395 ON log (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE place (id INT NOT NULL, category_id INT NOT NULL, name VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, photo VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_741D53CD12469DE2 ON place (category_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE rating (id INT NOT NULL, visit_id INT NOT NULL, user_id INT NOT NULL, rating INT NOT NULL, comment TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, status VARCHAR(20) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D889262275FA0FF2 ON rating (visit_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D8892622A76ED395 ON rating (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE refund (id INT NOT NULL, visit_id INT NOT NULL, requested_by_id INT NOT NULL, processed_by_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, reason TEXT NOT NULL, status VARCHAR(20) NOT NULL, rejection_reason TEXT DEFAULT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, processed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5B2C145875FA0FF2 ON refund (visit_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5B2C14584DA1E751 ON refund (requested_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5B2C14582FFD4FD3 ON refund (processed_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_preferences (user_id INT NOT NULL, notification_preferences JSON NOT NULL, language_preferences JSON NOT NULL, display_preferences JSON NOT NULL, PRIMARY KEY(user_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_preferences_favorite_places (user_id INT NOT NULL, place_id INT NOT NULL, PRIMARY KEY(user_id, place_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_32E8DF52A76ED395 ON user_preferences_favorite_places (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_32E8DF52DA6A219 ON user_preferences_favorite_places (place_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_preferences_visited_places (user_id INT NOT NULL, place_id INT NOT NULL, PRIMARY KEY(user_id, place_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E876084BA76ED395 ON user_preferences_visited_places (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E876084BDA6A219 ON user_preferences_visited_places (place_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE visitor (id INT NOT NULL, visit_id INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, is_present BOOLEAN NOT NULL, comments TEXT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_CAE5E19F75FA0FF2 ON visitor (visit_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category ADD CONSTRAINT FK_64C19C1727ACA70 FOREIGN KEY (parent_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE guide_unavailability ADD CONSTRAINT FK_DF4659DBD7ED1D4B FOREIGN KEY (guide_id) REFERENCES guide (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE log ADD CONSTRAINT FK_8F3F68C5A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE place ADD CONSTRAINT FK_741D53CD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rating ADD CONSTRAINT FK_D889262275FA0FF2 FOREIGN KEY (visit_id) REFERENCES visit (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rating ADD CONSTRAINT FK_D8892622A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE refund ADD CONSTRAINT FK_5B2C145875FA0FF2 FOREIGN KEY (visit_id) REFERENCES visit (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE refund ADD CONSTRAINT FK_5B2C14584DA1E751 FOREIGN KEY (requested_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE refund ADD CONSTRAINT FK_5B2C14582FFD4FD3 FOREIGN KEY (processed_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preferences ADD CONSTRAINT FK_402A6F60A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preferences_favorite_places ADD CONSTRAINT FK_32E8DF52A76ED395 FOREIGN KEY (user_id) REFERENCES user_preferences (user_id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preferences_favorite_places ADD CONSTRAINT FK_32E8DF52DA6A219 FOREIGN KEY (place_id) REFERENCES place (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preferences_visited_places ADD CONSTRAINT FK_E876084BA76ED395 FOREIGN KEY (user_id) REFERENCES user_preferences (user_id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preferences_visited_places ADD CONSTRAINT FK_E876084BDA6A219 FOREIGN KEY (place_id) REFERENCES place (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visitor ADD CONSTRAINT FK_CAE5E19F75FA0FF2 FOREIGN KEY (visit_id) REFERENCES visit (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX uniq_ca9ec735e7927c74
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE guide ADD user_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE guide DROP email
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE guide DROP roles
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE guide DROP password
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE guide ALTER country TYPE VARCHAR(255)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE guide ADD CONSTRAINT FK_CA9EC735A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_CA9EC735A76ED395 ON guide (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ADD phone VARCHAR(20) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ADD is_active BOOLEAN NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ADD last_login_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "user".created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "user".last_login_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visit ADD status VARCHAR(50) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visit DROP title
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visit DROP description
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visit ALTER country TYPE VARCHAR(255)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visit RENAME COLUMN max_tourists TO place_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visit ADD CONSTRAINT FK_437EE939DA6A219 FOREIGN KEY (place_id) REFERENCES place (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_437EE939DA6A219 ON visit (place_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visit DROP CONSTRAINT FK_437EE939DA6A219
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE category_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE guide_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE guide_unavailability_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE log_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE place_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE rating_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE refund_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE visitor_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category DROP CONSTRAINT FK_64C19C1727ACA70
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE guide_unavailability DROP CONSTRAINT FK_DF4659DBD7ED1D4B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE log DROP CONSTRAINT FK_8F3F68C5A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE place DROP CONSTRAINT FK_741D53CD12469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rating DROP CONSTRAINT FK_D889262275FA0FF2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rating DROP CONSTRAINT FK_D8892622A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE refund DROP CONSTRAINT FK_5B2C145875FA0FF2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE refund DROP CONSTRAINT FK_5B2C14584DA1E751
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE refund DROP CONSTRAINT FK_5B2C14582FFD4FD3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preferences DROP CONSTRAINT FK_402A6F60A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preferences_favorite_places DROP CONSTRAINT FK_32E8DF52A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preferences_favorite_places DROP CONSTRAINT FK_32E8DF52DA6A219
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preferences_visited_places DROP CONSTRAINT FK_E876084BA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preferences_visited_places DROP CONSTRAINT FK_E876084BDA6A219
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visitor DROP CONSTRAINT FK_CAE5E19F75FA0FF2
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE category
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE guide_unavailability
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE log
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE place
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE rating
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE refund
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_preferences
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_preferences_favorite_places
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_preferences_visited_places
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE visitor
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" DROP phone
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" DROP is_active
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" DROP created_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" DROP last_login_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE guide DROP CONSTRAINT FK_CA9EC735A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_CA9EC735A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE guide ADD email VARCHAR(180) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE guide ADD roles JSON NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE guide ADD password VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE guide DROP user_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE guide ALTER country TYPE VARCHAR(100)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX uniq_ca9ec735e7927c74 ON guide (email)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_437EE939DA6A219
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visit ADD title VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visit ADD description TEXT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visit DROP status
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visit ALTER country TYPE VARCHAR(100)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visit RENAME COLUMN place_id TO max_tourists
        SQL);
    }
}
