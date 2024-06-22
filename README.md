# Projet S6 Symfony

## Auteurs :

- [Maxence PAULIN](https://github.com/MaxencePaulin)
- [Valentin MOUGENOT](https://github.com/valentinmougenot)

## Installation :

Modifier le fichier `.env` pour mettre votre propre mailtrap dans `MAILER_DSN`. (configurable sur [mailtrap.io](https://mailtrap.io/))

Ensuite, exécutez les commandes suivantes :
```bash
composer install
bin/console doctrine:database:create
bin/console doc:sc:up -f
bin/console doctrine:fixtures:load
symfony server:start 
```
