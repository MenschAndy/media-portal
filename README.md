# Media Portal - PHP8+ Edition

Ein modernes, dunkles Media Portal mit Galerien, Chat und Raumverwaltung.

## Features

✨ **Dunkles Design** - Modernes, augenschonendes Interface
⚡ **Schnell** - Optimiert für PHP 8+
📸 **Galerien** - Bilder und Videos hochladen und anzeigen
💬 **Chat** - Echtzeit-Messaging mit privaten Nachrichten
🔐 **Benutzerkonten** - Sichere Registrierung und Anmeldung
🎯 **Raumverwaltung** - Erstellen und verwalten Sie öffentliche/private Räume
📱 **Responsive** - Funktioniert auf allen Geräten

## Anforderungen

- PHP 8.0 oder höher
- MySQL 5.7 oder höher
- Webserver (Apache, Nginx, etc.)

## Installation

### 1. Datenbank einrichten

```bash
# MySQL aufstarten
mysql -u root

# Datenbank erstellen
CREATE DATABASE media_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Tabellen erstellen

```bash
# In Ihrem Browser aufrufen:
http://localhost/media-portal/config/setup.php
```

Oder führen Sie direkt aus:
```bash
php config/setup.php
```

### 3. Konfiguration

Bearbeiten Sie `config/database.php` mit Ihren Datenbankdetails:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'media_portal');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 4. Uploads-Verzeichnis

```bash
mkdir -p uploads
chmod 755 uploads
```

## Nutzung

1. Öffnen Sie `http://localhost/media-portal`
2. Erstellen Sie ein neues Konto
3. Laden Sie Medien hoch
4. Chatten Sie mit anderen Benutzern
5. Erstellen und verwalten Sie Räume

## Dateistruktur

```
media-portal/
├── index.php           # Hauptseite
├── login.php           # Anmeldung
├── register.php        # Registrierung
├── rooms.php           # Raumbrowser
├── config/
│   ├── database.php    # DB-Konfiguration
│   └── setup.php       # DB-Setup
├── includes/
│   └── auth.php        # Authentifizierung
├── api/
│   ├── upload.php      # Datei-Upload
│   ├── get_media.php   # Medien abrufen
│   ├── get_messages.php # Nachrichten abrufen
│   ├── send_message.php # Nachricht senden
│   ├── get_rooms.php   # Räume abrufen
│   └── create_room.php # Raum erstellen
├── assets/
│   ├── css/
│   │   └── style.css   # Haupt-Styles
│   └── js/
│       └── main.js     # JavaScript
└── uploads/            # Hochgeladene Dateien
```

## Sicherheit

- Passwörter werden mit bcrypt gehasht
- SQL-Injektionen werden durch PDO-Prepared-Statements verhindert
- XSS-Schutz durch htmlspecialchars()
- CSRF-Protection kann implementiert werden

## Lizenz

MIT License
