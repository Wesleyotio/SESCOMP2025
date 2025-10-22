<?php

declare(strict_types=1);

namespace App\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241121231017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'cria a tabela de type_products no banco';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('create table if not exists type_products(
                            id serial,
                            name varchar(25) not null,
                            created_at timestamp,
                            updated_at timestamp,
                            deleted_at timestamp default null,
                            
                            CONSTRAINT pk_type_product
                                primary key (id)
                            
                        );');

    }

    public function down(Schema $schema): void
    {
       $this->addSql('drop table type_products');

    }
}
