<?php

declare(strict_types=1);

namespace App\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241121232600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'cria a tabela products';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('create table if not exists products(
                            id serial ,
                            code integer not null,
                            type_product_id integer  not null,
                            name varchar(25),
                            value decimal(12,2) not null,
                            created_at timestamp,
                            updated_at timestamp,
                            deleted_at timestamp default null,
                            unique (code),
                            
                            CONSTRAINT pk_product
                                primary key (id),
                                
                            CONSTRAINT fk_product_type_product
                                foreign key (type_product_id)
                                references  type_products(id)
                                
                        );');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('drop table products');

    }
}
