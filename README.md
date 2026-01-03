# Contacts Directory

Jednoduchý adresář kontaktů postavený na Symfony. Kontakty se ukládají do SQLite databáze.

## Funkce

- Výpis kontaktů na URL `/` (seznam s paginací)
- Detail - Editace kontaktu na URL `/{slug}` (SEO URL podle jména/příjmení)
- Detail - vytvoření nového kontaktu na URL `/contact/new`
- Smazání kontaktu přes `/contact/{id}/delete`
- Automatické generování unikátního slugu z jména a příjmení

## Technologie

- PHP + Symfony
- Doctrine ORM + Migrations
- SQLite
- JavaScript

## Požadavky

Lokální požadavky:
- PHP (doporučeno 8.2+)
- Composer
- Symfony CLI (volitelné)
- SQLite

## Lokální instalace a spuštění

1. Klonování repozitáře
   ```bash
   git clone https://github.com/fpodhorsky/contacts-directory.git
   cd contacts-directory
   ```
2. Instalace závislostí composer
   ```bash
   composer install
   ```
3. Spuštění migrací
   ```bash
   php bin/console doctrine:migrations:migrate
   ```
4. Spuštění aplikace
   ```bash
   symfony server:start
   ```
   nebo

   ```bash
  php -S 127.0.0.1:8000 -t public
   ```

Aplikace poběží na:
 ```
  http://127.0.0.1:8000
   ```
