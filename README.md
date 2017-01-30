# ForkPlayer

##RemoteFork that can be run on with PHP on router or NAS.

Apache config example:
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
**FP_LOCAL_VIDEO_PATH** - Path to folder with videos

**FP_DOCUMENT_ROOT** - Path to ForkPlayer script

Change **FP_LOCAL_VIDEO_PATH**, **FP_DOCUMENT_ROOT**, Path to logs - accordintg to your router config.

Configure RemoteFork on TV to your router/NAS IP and port 89

Сlick on "Перейти по адресу" and enter your http://IP:port then add address to start page

