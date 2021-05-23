# Planning Crocos
## Lancement du projet en développement
Installer :
- Docker
- docker-compose

Puis exécuter :
```bash
mkdir -p ./docker/data/{.symfony,.composer,.config,.quasar-starter-kits,.yarn,.cache,.npm} && touch docker/data/.yarnrc
```

Et enfin lancer le projet de cette manière :
```bash
COMPOSE_DOCKER_CLI_BUILD=1 DOCKER_BUILDKIT=1 CURRENT_USER=$(id -u):$(id -g) docker-compose -p crocoplan-dev -f docker/docker-compose-dev.yml up --build
```

Pour supprimer les données de la base et la ré-initialiser :
```bash
CURRENT_USER=$(id -u):$(id -g) docker-compose -p crocoplan-dev -f docker/docker-compose-dev.yml down --volumes
```

## Lancement du serveur en mode PROD
```bash
COMPOSE_DOCKER_CLI_BUILD=1 DOCKER_BUILDKIT=1 docker-compose -p crocoplan-run -f docker/docker-compose-run.yml up --build
```
L'application sera accessible sur l'adresse http://localhost:81

Si vous avez également lancé le dev, vous pouvez copier la base de donnée de cette manière
```bash
docker exec -i crocoplan-dev_db_1 mysqldump -u crocoplan -pcrocoplan crocoplan | docker exec -i crocoplan-run_db_1 mysql -u crocoplan -pcrocoplan crocoplan
```

## Extraction des fichiers de PROD (pour une sorte de build en local)
Après avoir lancé l'application en mode PROD (RUN), lancez les commandes suivantes à la ine du projet :
```bash
rm -rf build
docker cp crocoplan-run_api_server_1:/var/www/html build
docker cp crocoplan-run_client_app_1:/usr/share/nginx/html/. build/public/
```

## Génération des entités côté client (TypeScript)
On suit https://api-platform.com/docs/client-generator/typescript/ , on se connecte d'abord à la partie cliente
```bash
docker exec -it crocoplan-client_app-dev sh
```
Puis :
```bash
npx @api-platform/client-generator --generator typescript http://crocoplan-api_server-dev:8000/api src/
```
