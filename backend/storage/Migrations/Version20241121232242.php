<?php

declare(strict_types=1);

namespace App\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241121232242 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'cria a tabela taxes';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('create table if not exists taxes(
                            id serial ,
                            type_product_id integer  not null,
                            value decimal(4,2) not null,
                            created_at timestamp,
                            updated_at timestamp,
                            deleted_at timestamp default null,
                            
                            CONSTRAINT pk_tax
                                primary key (id),
                                
                            CONSTRAINT fk_tax_type_product
                                foreign key (type_product_id)
                                references  type_products(id)
                                
                            
                        );');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('drop table taxes');

    }
}
