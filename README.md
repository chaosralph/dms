# DMS System

Kleines Dokumenten-Management-System (DMS) auf PHP-Basis mit Upload, Kategorien und Export-Funktionen.

## Voraussetzungen

- PHP 8.x
- MySQL / MariaDB
- Apache (z. B. XAMPP)

## Installation (kurz)

1. Projekt in dein Webverzeichnis kopieren.
2. Datenbank anlegen und `setup.sql` importieren.
3. Konfiguration in `config.php` anpassen.
4. Schreibrechte fuer `uploads/` sicherstellen.
5. Anwendung im Browser aufrufen.

## Wichtige Ordner

- `api/` - Backend-Endpunkte
- `assets/` - CSS/JavaScript
- `includes/` - Helfer, DB, PDF-Logik
- `uploads/` - Hochgeladene Dateien

## Sicherheitshinweise

- Keine Zugangsdaten in Git committen.
- Fuer Produktion HTTPS aktivieren.
- Upload-Dateitypen und Dateigroessen begrenzen.
