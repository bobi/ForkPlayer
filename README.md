# ForkPlayer http://forkplayer.tv/

##RemoteFork that can be run with PHP on Linux/router/NAS.

* Configure webserver with examples below
* Configure RemoteFork on TV to your router/NAS IP and port
* Open RemoteFork Player DLNA or you can also, click on "Перейти по адресу" and enter your http://IP:port then add address to start page

**Apache config example:**
```apache
Listen 89

<VirtualHost *:89>
	Define FP_LOCAL_VIDEO_PATH /Path/to/Folder/with/videos
    Define FP_DOCUMENT_ROOT /Path/to/forkplayer/script
    
	DocumentRoot ${FP_DOCUMENT_ROOT}
	Alias "/localvideo" ${FP_LOCAL_VIDEO_PATH}
	
	ErrorLog /Path/to/error/log/forkplayer.error.log
	CustomLog /Path/to/access/logs/forkplayer.access.log combined
	
	<Directory ${FP_DOCUMENT_ROOT}>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
	</Directory>

	<Directory ${FP_LOCAL_VIDEO_PATH}>
		Options Indexes FollowSymLinks
		IndexOptions Charset=UTF-8
		AllowOverride None
		Require all granted
	</Directory>

</VirtualHost>
```
_FP_LOCAL_VIDEO_PATH_ - Path to folder with videos

_FP_DOCUMENT_ROOT_ - Path to ForkPlayer script

Change _FP_LOCAL_VIDEO_PATH_, _FP_DOCUMENT_ROOT_, Path to logs - accordintg to your router config.

**nginx config example:**

```nginx
server {
	listen 89;
	charset utf-8;

	root /Path/to/forkplayer/script;
	index index.php;
	server_name localhost;

	default_type application/json;
	add_header Access-Control-Allow-Origin *;

	location /localvideo {
		alias /Path/to/Folder/with/videos;
		autoindex on;
	}
	location / {
		rewrite ^/$ /index.php;
	}
	location /test {
		rewrite ^/test/?$ /test.php;
	}
	location /parserlink {
		rewrite ^/parserlink/?$ /parserlink.php;
	}
	location /treeview {
		rewrite ^/treeview/?$ /index.php;
	}
	location /plugin {
		rewrite ^/plugin/([^/]+)/?$ /index.php?plugin=$1;
	}
	location ~ .php$ {
		fastcgi_pass   127.0.0.1:9123;
		fastcgi_index  index.php;
		fastcgi_param FP_LOCAL_VIDEO_PATH /Path/to/Folder/with/videos;
		include fastcgi.conf;
		include fastcgi_params;			
	}
}	
```
