APP_CONTAINER:= app
BD_CONTAINER:= market-sales-db-1

DOCKER_RUN:= docker exec -it ${APP_CONTAINER}

configure:
	@echo "Configura dependências do PHP..."
	${DOCKER_RUN} bash -c "cd backend/config && cp .env.example .env"
	${DOCKER_RUN} bash -c "cd backend && composer install"

test:
	@echo "Executa somente a suite de testes unitários..."
	${DOCKER_RUN} bash -c "cd backend && composer test"

test-coverage:
	@echo "Executando cobertura de testes..."
	${DOCKER_RUN} bash -c "cd backend && composer test-coverage"

test-integration:
	@echo "Executa somente a suite de testes de integração"
	${DOCKER_RUN} bash -c "cd backend && composer test-integration"

test-report:
	@echo "Gerando html da cobertura de testes..."
	${DOCKER_RUN} bash -c "cd backend && composer test-coverage-html"

phpstan:
	@echo "Executando analise estática..."
	${DOCKER_RUN} bash -c "cd backend && composer phpstan"

phpcs:
	@echo "Executando o lint..."
	${DOCKER_RUN} bash -c "cd backend && composer phpcs"

phpcbf:
	@echo "Executando o correções para o lint"
	${DOCKER_RUN} bash -c "cd backend && composer phpcbf"

mess:
	${DOCKER_RUN} bash -c "cd backend && composer mess"

migrations:
	@echo "Executando migrations..."
	${DOCKER_RUN} bash -c "cd backend && composer migrations"

migrations-generate:
	@echo "Criando uma nova migration..."
	${DOCKER_RUN} bash -c "cd backend && composer migrations-generate"

migrations-test:
	@echo "Executando migrations em modo teste..."
	${DOCKER_RUN} bash -c "cd backend && composer migrations-test"

show-coverage:
	@echo "Mostrando a cobertura de código pelo navegador"
	xdg-open backend/storage/coverage/index.html


