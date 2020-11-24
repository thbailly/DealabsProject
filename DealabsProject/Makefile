init:
	mv .env.install .env.local
	docker-compose build

start:          ## Start Docker containers
	docker-compose up -d

install:
	./install.sh
stop:           ## Stop the Docker containers
	docker-compose stop

updateDb:
	./updateDb.sh

getApiToken:
	./getApiToken.sh