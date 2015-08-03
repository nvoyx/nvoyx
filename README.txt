REQUIREMENTS

NGINX
PHP-FPM
MySql or MariaDB
WebP
ImageMagick
XCache (both opcode and variable should be enabled)
Aspell (plus any languages you want to support)
php-fpm php-cli php-common php-mysql php-gd php-xml php-mbstring php-mcrypt php-xcache php-pdo php-pear php-pecl-imagick php-process php-pspell php-devel


FOLDER/FILE SETUP

create the following directories:
	log
	keys
	resources/files/compress
	resources/files/documents
	resources/files/images/ckeditor
	resources/files/images/cms
	resources/files/images/twitter
	session
	socket
	tmp
	rollback
	recovery

make the above folders plus blocks/private/ajax writeable by the server.

copy configuration/config.json.example to configuration/config.json and set to writeable by the server. Read then delete the notes in config.json.

LOAD BALANCERS
if you need to sync file uploads between servers, add the alternative server(s) as an array under the SYNC option in config.json
Set the authentication to wither password or key. If you are using key, then add the key to the keys folder. Set the owner of the key file to the domain
user and set the permissions to 600. It is worth logging onto the box and testing the connection via the key, before testing through the website.

NGINX EXAMPLE CONFIG

server {
        listen 80;
        server_name some.domain.com;
        access_log off;
        error_log /dev/null crit;
        root /path/to/some.domain.com/public;
        index index.php;
        location /favicon.png {log_not_found off;}
        location /robots.txt {}
        location /nginx_protected_files {
                internal;
                alias /path/to/some.domain.com/resources;
        }
	location / {
                fastcgi_param SCRIPT_FILENAME /path/to/som.domain.com/public/index.php;
                fastcgi_pass unix:/path/to/some.domain.com/socket/php.sock;
                fastcgi_read_timeout 5m;
                fastcgi_index index.php;
                include fastcgi_params;
                try_files $uri /index.php?url=$uri&$args;
        }
}


server {
        listen 443 ssl spdy;
        server_name some.domain.com;
        ssl_certificate /path/to/certificates/some.domain.com.crt;
        ssl_certificate_key /path/to/some.domain.com.pem;
        access_log off;
        error_log /dev/null crit;
        root /path/to/some.domain.com/public;
        index index.php;
        location /favicon.png {log_not_found off;}
        location /robots.txt {}
        location /nginx_protected_files {
                internal;
                alias /path/to/some.domain.com/resources;
        }
	location / {
                fastcgi_param HTTPS on;
                fastcgi_param SCRIPT_FILENAME /path/to/some.domain.com/public/index.php;
                fastcgi_pass unix:/path/to/some.domain.com/socket/php.sock;
                fastcgi_read_timeout 5m;
                fastcgi_index index.php;
                include fastcgi_params;
                try_files $uri /index.php?url=$uri&$args;
        }
}



PHP-FPM EXAMPLE CONFIG

[some.domain.com]

listen = /path/to/some.domain.com/socket/php.sock
listen.backlog = -1
listen.mode = 0660
listen.owner = some_user
listen.group = nginx

; Unix user/group of processes
user = some_user
group = nginx

; Choose how the process manager will control the number of child processes.
pm = dynamic
pm.max_children = 75
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500

; Logging
php_admin_value[error_log] = /path/to/some.domain.com/log/error.log
catch_workers_output = yes

; Pass environment variables
env[HOSTNAME] = $HOSTNAME
env[PATH] = /usr/bin:/bin
env[TMP] = /path/to/some.domain.com/tmp
env[TMPDIR] = /path/to/some.domain.com/tmp
env[TEMP] = /path/to/some.domain.com/tmp

; host-specific php ini settings here
php_admin_value[open_basedir] = /path/to/some.domain.com/:/usr/share/misc/:/usr/bin/cwebp:/usr/bin/dwebp:/path/to/certificates
php_admin_value[post_max_size] = 1000M
php_admin_value[upload_max_filesize] = 1000M
php_admin_value[memory_limit] = 256M
php_admin_value[upload_tmp_dir] = /path/to/some.domain.com/tmp
php_admin_value[session.save_path] = /path/to/some.domain.com/session
php_admin_value[sendmail_from] = no-reply@some.domain.com
php_admin_value[openssl.cafile] = /path/to/certificates/cert.crt


EXAMPLE CRON FILE

#! /bin/bash
DATE=`date`
echo ""
echo "started daily cron for some.domain.com - $DATE"
echo "-----"
echo ""
echo "* running some.domain.com admin..."
/usr/bin/wget -nv --delete-after -t 1 https://some.domain.com/settings/resources/cron/daily.php
echo "* done"
echo "-----"
echo ""
echo "some.domain.com daily cron successfully completed OK"
exit 0

Crons exist for hourly, daily, weekly and monthly.



USERS
create a user with the same name as defined in php-fpm config.
useradd -g nginx some_user



DATABASE

create a user/database with the same name as defined in php-fpm config.



FINISH

visit some.domain.com and complete field to finish installation



LOGIN

visit some.domain.com/settings/user/login (if your certifcate is self-signed, you will receive a warning).



ADMIN TOOLBAR

once logged in, you may call up the toolbar from any frontend page by pressing the backtick (`) key.
