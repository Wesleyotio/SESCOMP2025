<?php

declare(strict_types=1);

namespace App\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241121233032 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'criando a tabela sales_items';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('create table if not exists sale_items(
                            sale_id integer  not null,
                            product_id integer  not null,
                            amount integer not null,
                            created_at timestamp,
                            updated_at timestamp,
                            deleted_at timestamp default null,
                            
                            CONSTRAINT pk_sale_item PRIMARY KEY (sale_id, product_id),
                            
                            CONSTRAINT fk_sale_item
                                foreign key (sale_id)
                                references  sales(id),
                                
                            CONSTRAINT fk_sale_item_product
                                foreign key (product_id)
                                references  products(id)
                                
                        );');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('drop table sale_items');

    }
}
