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
CURRENT_USER=$(id -u):$(id -g) docker-compose -f docker/docker-compose-dev.yml up
```

Pour supprimer les données de la base et la ré-initialiser :
```bash
CURRENT_USER=$(id -u):$(id -g) docker-compose -f docker/docker-compose-dev.yml down --volumes
```

# Historique de création du projet (À NE PAS EXÉCUTER, il s'agit juste d'historique)
## Partie `api-server`
```bash
CURRENT_USER=$(id -u):$(id -g) docker-compose -f docker/docker-compose-dev.yml build crocoplan-api_server-dev
docker run -it --rm -v "$(pwd):/workspace" -v "$(pwd)/docker/data/.symfony:/.symfony" -v "$(pwd)/docker/data/.composer:/.composer" -u $(id -u):$(id -g) -w /workspace crocoplan-api_server-dev sh
```
Puis une fois dans le container :
```bash
symfony new api-server --version=lts --no-git --dir=api-server-new
mv api-server-new/* api-server-new/.env api-server-new/.gitignore api-server/
rm -r api-server-new
cd api-server
# En suivant https://api-platform.com/docs/distribution/#using-symfony-flex-and-composer-advanced-users
composer req api
# En suivant https://symfony.com/doc/4.4/setup/web_server_configuration.html
composer require symfony/apache-pack
cp .gitignore .dockerignore
```
Puis éditer le fichier .env pour remplacer l'URL de la base de donnée :
```bash
DATABASE_URL=mysql://crocoplan:crocoplan@crocoplan-db_dev:3306/crocoplan?serverVersion=5.6
```
## Partie `client-app`
```bash
CURRENT_USER=$(id -u):$(id -g) docker-compose -f docker/docker-compose-dev.yml build crocoplan-client_app-dev
docker run -it --rm -v $(pwd):/workspace -v $(pwd)/docker/data/.config:/.config -v $(pwd)/docker/data/.quasar-starter-kits:/.quasar-starter-kits -v $(pwd)/docker/data/.yarn:/.yarn -v $(pwd)/docker/data/.yarnrc:/.yarnrc -v $(pwd)/docker/data/.cache:/.cache -w /workspace -u $(id -u):$(id -g) crocoplan-client_app-dev sh
```
Puis une fois au sein du container:
```bash
quasar create client-app
```
Puis les réponses à fournir:
```
? Project name (internal usage for dev) client-app
? Project product name (must start with letter if building mobile apps) CrocoPlan
? Project description Application de planning des crocos
? Author Anthony OGIER et Benoit MASSINI
? Pick your favorite CSS preprocessor: (can be changed later) Sass
? Pick a Quasar components & directives import strategy: (can be changed later) Auto import
? Check the features needed for your project: ESLint (recommended), TypeScript, Vuex, Axios
? Pick a component style: Class
? Pick an ESLint preset: Standard
? Continue to install project dependencies after the project has been created? (recommended) yarn
```
Quitter ensuite ce container, puis copier le `.gitignore`:
```
cp client-app/.gitignore client-app/.dockerignore
```