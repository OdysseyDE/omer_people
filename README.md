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
