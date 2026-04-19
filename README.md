# DMS System

Kleines Dokumenten-Management-System (DMS) auf PHP-Basis mit Upload, Kategorien und Export-Funktionen.

## Screenshots

Platzhalter fuer Screenshots (optional):

- `docs/screenshots/login.png` - Login
- `docs/screenshots/upload.png` - Upload mit Kamera
- `docs/screenshots/dashboard.png` - Uebersicht / Liste

Beispiel-Markdown:

![Login](docs/screenshots/login.png)
![Upload](docs/screenshots/upload.png)
![Dashboard](docs/screenshots/dashboard.png)

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

## API-Uebersicht

Beispiel-Endpunkte (je nach Deployment-Pfad):

- `GET api/documents.php` - Dokumente abrufen
- `POST api/upload.php` - Dokument/Bild hochladen
- `GET api/categories.php` - Kategorien laden
- `POST api/document-update.php` - Dokumentdaten aktualisieren
- `GET api/export.php` - Export (z. B. PDF)

Hinweis: Falls Authentifizierung aktiv ist, muessen Session/Cookie oder Token mitgesendet werden.

## Sicherheitshinweise

- Keine Zugangsdaten in Git committen.
- Fuer Produktion HTTPS aktivieren.
- Upload-Dateitypen und Dateigroessen begrenzen.

## Deployment-Checkliste

- [ ] `config.php` fuer Zielserver angepasst (DB, Pfade, URL)
- [ ] Datenbank importiert (`setup.sql`)
- [ ] Schreibrechte fuer `uploads/` gesetzt
- [ ] HTTPS aktiv und weitergeleitet
- [ ] `.htaccess` auf dem Zielserver aktiv (mod_rewrite/mod_headers)
- [ ] Fehlerlogging aktiviert, `display_errors` in Produktion aus
- [ ] Backup-Strategie fuer Datenbank und Uploads definiert
