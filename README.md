# omer_people
Personenverwaltung für Odyssey of the Mind Deutschland e.V.

# Vorraussetzungen
- PHP >5.5
- composer

# Installation
- composer update

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

