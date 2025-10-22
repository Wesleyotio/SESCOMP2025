<?php

declare(strict_types=1);

namespace App\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241121232815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'criando a tabela sales';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('create table if not exists sales(
                            id serial ,
                            value_sale decimal(12,2) not null,
                            value_tax decimal(12,2) not null,
                            created_at timestamp,
                            updated_at timestamp,
                            deleted_at timestamp default null,
                            
                            CONSTRAINT pk_sale
                                primary key (id)
                                
                        );');
    }

    public function down(Schema $schema): void
    {
       $this->addSql('drop table sales');

    }
}
