# Liliana's Trade

This is the repo of the Liliana's Trade website!

It's a Django project that manage collection of magic card.

> Website available at address: [lilianastrade.h.minet.net](http://lilianastrade.h.minet.net)

## Table of Contents

- [App Structure](#app-structure)
- [Users](#users)
- [Installation](#installation)
- [Tests](#tests)
- [Deploy](#deploy)
- [Upgrading PostgreSQL](#upgrading-postgresql)
- [Authors](#authors)

## App Structure

- [Controller/](src/Controller): The controllers of the website
- [DataFixtures/](src/DataFixtures): The data fixtures of the website
- [Entity/](src/Entity): the entities of the website
- [Form/](src/Form): the forms of the website
- [Repository/](src/Repository): the repositories of the website
- [Security/](src/Security): the voters (ownership rules)

## Users

3 users :

* User
  * user@email.com
  * 1234

* Marie
  * marie@gmail.com
  * mariethebest

* Jean
  * jean_bon@gmail.com
  * toutestbon

## Installation

Stack: PHP 8.5 (FrankenPHP), Symfony 8.1, Doctrine ORM 3, EasyAdmin 5, PostgreSQL 18.

1. Clone the repo

    ```bash
    git clone git@github.com:julsql/lilianastrade.git
    cd lilianastrade
    ```

2. Create `.env` (not versioned)

    ```dotenv
    APP_ENV=dev
    APP_SECRET=change-me
    POSTGRES_PASSWORD=change-me
    ```

3. Launch the website on http://localhost:8005

    ```bash
    docker compose up --build
    ```

    On startup the entrypoint syncs the schema and loads the fixtures if the `user` table is empty.

## Tests

The functional tests need a PostgreSQL database (the `_test` suffix is added to the database name):

```bash
docker run -d --rm --name lilianastrade-testdb -p 5432:5432 \
    -e POSTGRES_USER=app -e POSTGRES_PASSWORD='!ChangeMe!' -e POSTGRES_DB=app_test postgres:18-alpine
vendor/bin/phpunit
```

The CI runs the tests on every pull request and before each build of `main`.

## Deploy

Deployment is fully automated through CI/CD:

1. On push to `main`, the [`docker.yml`](.github/workflows/docker.yml) workflow runs the tests, then builds the Docker image and pushes it to the GitHub Container Registry (GHCR).
2. Deployment is automatic: after the push, the CI pings the server (Keel webhook), which updates its pods. No SSH, no manual `kubectl`.

The Kubernetes manifests live in the [`k3s-manifests`](https://github.com/julsql/k3s-manifests) repo.

## Upgrading PostgreSQL

A major PostgreSQL upgrade (e.g. 14 → 18) cannot reuse the old data directory: dump and restore.
Do it **before** merging into `main`, since the merge triggers the deployment.

```bash
# 1. Stop the app, then dump the old database
kubectl scale deploy/lilianastrade --replicas=0
kubectl exec <old-postgres-pod> -- pg_dump -U app -Fc app > lilianastrade.dump

# 2. Deploy postgres:18 with a NEW volume (the 18+ image mounts /var/lib/postgresql, not .../data)

# 3. Restore, then restart the app
kubectl exec -i <new-postgres-pod> -- pg_restore -U app -d app --no-owner < lilianastrade.dump
kubectl scale deploy/lilianastrade --replicas=1
```

Set `serverVersion=18` in `DATABASE_URL`. On startup, `doctrine:schema:update` only switches
`messenger_messages.id` to an identity column: the business tables are left untouched.

Locally, `docker compose` starts PostgreSQL 18 on a fresh database (the old 14 data is ignored).

## Authors

- Jul SQL
