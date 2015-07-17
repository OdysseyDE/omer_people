# omer_people
Personenverwaltung für Odyssey of the Mind Deutschland e.V.

# Vorraussetzungen
- PHP >5.5
- php5-pgsql
- composer
- PostgreSQL >9.5

# Installation
- composer update
- SQL ausführen

# REST Endpunkte
- GET  /people/test - Gibt die aktuelle (Server-) Zeit zurück
- GET  /people/db - Ausgabe von DB-Settings und geladenen Treibern
  - (NUR im Debug mode)
- GET  /people/php - Ausgabe phpinfo()
  - (NUR im Debug mode)
- GET  /people/$ID$ - Holt Person $ID$
  - return 404 wenn nicht gefunden
- POST /people - Legt eine neue Person an, ID wird erzeugt (uniqid('',true))
  - return 201 wenn Erfolg mit Location: header auf neues Object sowie JSON-kodierter ID
  - return 409 wenn Constraints verletzt
- POST /people/$ID$ - Legt eine neue Person mit id = $ID$ an
  - return 201 wenn Erfolg mit Location: header auf neues Object sowie JSON-kodierter ID
  - return 409 wenn Constraints verletzt
- PUT  /people/$ID$ - Updated Person $ID$
  - return 204 No Content wenn erfolgreich
  - return 409 wenn Constraint verletzt (z.B. ID parameter != id im JSON-Dokument (wenn gesetzt)
- DELETE /people/$ID$ - Löscht Person $ID$
  - return 403 wenn nicht erfolgreich

# Apache conf
```Apache
<Directory "/var/www/html/api">
	DirectoryIndex index.php
	<IfModule mod_rewrite.c>
		RewriteEngine On
		RewriteRule ^$ index.php [QSA,L]
		RewriteCond %{REQUEST_FILENAME} !-f
		RewriteCond %{REQUEST_FILENAME} !-d
		RewriteRule ^(.*)$ index.php [QSA,L]
	</IfModule>
	#FallbackResource "index.php"
</Directory>
```
# TODO
- Suche (vorerst Name, Vorname)
- Optimistic locking
- Auth
- (Unit-) Tests
