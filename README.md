# SESCOMP2025
Uma simples API para trabalhar com testes unitários

## Pré-requisitos
- Docker instalado
- Docker Compose configurado
- Make disponível no sistema

## Inicialização do Projeto

### 1. Iniciar containers em background
```bash
docker-compose up -d --build
```
Este comando inicializa todos os serviços necessários em segundo plano.

### 2. Configurar ambiente
```bash
make configure
```
Realiza a configuração inicial do projeto.

### 3. Executar migrações em modo teste
```bash
make migrations-test
```
Aplica todas as migrações necessárias em modo teste.

### 4. Executar migrações 
```bash
make migrations-test
```
Aplica todas as migrações necessárias.


## Comandos Disponíveis no Makefile
Para visualizar todos os comandos disponíveis no projeto, consulte o arquivo Makefile.
