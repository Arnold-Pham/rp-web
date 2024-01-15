<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231030102656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, address VARCHAR(255) NOT NULL, zip_code INTEGER NOT NULL, city VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE airport (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, iata_code VARCHAR(255) NOT NULL, zone VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE car (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, brand VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL, color VARCHAR(10) NOT NULL, plate VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE option (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, valet_a_id INTEGER DEFAULT NULL, valet_b_id INTEGER DEFAULT NULL, extra SMALLINT NOT NULL, CONSTRAINT FK_5A8600B03D654571 FOREIGN KEY (valet_a_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_5A8600B02FD0EA9F FOREIGN KEY (valet_b_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_5A8600B03D654571 ON option (valet_a_id)');
        $this->addSql('CREATE INDEX IDX_5A8600B02FD0EA9F ON option (valet_b_id)');
        $this->addSql('CREATE TABLE parking (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, airport_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, daily_price DOUBLE PRECISION NOT NULL, CONSTRAINT FK_B237527A289F53C8 FOREIGN KEY (airport_id) REFERENCES airport (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_B237527A289F53C8 ON parking (airport_id)');
        $this->addSql('CREATE TABLE personal_data (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, car_id INTEGER DEFAULT NULL, address_id INTEGER DEFAULT NULL, invoice_id INTEGER DEFAULT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, phone_number INTEGER NOT NULL, type BOOLEAN NOT NULL, company_name VARCHAR(255) DEFAULT NULL, gender VARCHAR(20) NOT NULL, CONSTRAINT FK_9CF9F45EC3C6F69F FOREIGN KEY (car_id) REFERENCES car (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9CF9F45EF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9CF9F45E2989F1FD FOREIGN KEY (invoice_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9CF9F45EC3C6F69F ON personal_data (car_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9CF9F45EF5B7AF75 ON personal_data (address_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9CF9F45E2989F1FD ON personal_data (invoice_id)');
        $this->addSql('CREATE TABLE place (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, parking_id INTEGER NOT NULL, label VARCHAR(20) NOT NULL, available BOOLEAN NOT NULL, CONSTRAINT FK_741D53CDF17B2DD FOREIGN KEY (parking_id) REFERENCES parking (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_741D53CDF17B2DD ON place (parking_id)');
        $this->addSql('CREATE TABLE reservation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, option_id INTEGER DEFAULT NULL, personal_data_id INTEGER DEFAULT NULL, airport_id INTEGER DEFAULT NULL, parking_id INTEGER DEFAULT NULL, place_id INTEGER DEFAULT NULL, email VARCHAR(255) NOT NULL, code VARCHAR(50) NOT NULL, date_a DATETIME NOT NULL, date_b DATETIME NOT NULL, flight_a VARCHAR(255) NOT NULL, flight_b VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, valet BOOLEAN NOT NULL, date_c DATETIME NOT NULL, CONSTRAINT FK_42C84955A7C41D6F FOREIGN KEY (option_id) REFERENCES option (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_42C84955B4D2A817 FOREIGN KEY (personal_data_id) REFERENCES personal_data (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_42C84955289F53C8 FOREIGN KEY (airport_id) REFERENCES airport (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_42C84955F17B2DD FOREIGN KEY (parking_id) REFERENCES parking (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_42C84955DA6A219 FOREIGN KEY (place_id) REFERENCES place (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_42C84955A7C41D6F ON reservation (option_id)');
        $this->addSql('CREATE INDEX IDX_42C84955B4D2A817 ON reservation (personal_data_id)');
        $this->addSql('CREATE INDEX IDX_42C84955289F53C8 ON reservation (airport_id)');
        $this->addSql('CREATE INDEX IDX_42C84955F17B2DD ON reservation (parking_id)');
        $this->addSql('CREATE INDEX IDX_42C84955DA6A219 ON reservation (place_id)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(255) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, phone_number INTEGER NOT NULL, picture VARCHAR(255) NOT NULL, zone VARCHAR(255) NOT NULL, gender VARCHAR(255) NOT NULL, date_c DATETIME DEFAULT NULL, date_e DATETIME DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6496B01BC5B ON user (phone_number)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE airport');
        $this->addSql('DROP TABLE car');
        $this->addSql('DROP TABLE option');
        $this->addSql('DROP TABLE parking');
        $this->addSql('DROP TABLE personal_data');
        $this->addSql('DROP TABLE place');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
