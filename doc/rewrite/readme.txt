
有三种 route 类型

1. ROUTE_TYPE_NO_REWEIRTE
    完全不用重新
 
 
 2.ROUTE_TYPE_PATHINFO
    phpinfo 重新
   
   配置如下:
   A.Apache
		 #<IfModule mod_rewrite.c>
		RewriteEngine On
		RewriteBase /
		RewriteCond %{REQUEST_URI} !^/static
		RewriteCond %{REQUEST_FILENAME} !-f
		RewriteCond %{REQUEST_FILENAME} !-d
		
		
		RewriteRule ^(.+)$  index.php/$1 [L]
		
		#</IfModule>
 
 	B.Nginx
	 	server {
	
			if (!-f $request_filename) {
				rewrite ^/(.*)$ /index.php/$1 last;
				break;
			}
		
			location ~ .*\.(php|php5)
			{
				#fastcgi_index index.php; #如果有请将这个条注释掉
				
				set $path_info "";
				set $real_script_name $fastcgi_script_name;
				if ($fastcgi_script_name ~ (.*\.php)(/.+) ) {
					set $real_script_name $1;
					set $path_info $2;
				}
				fastcgi_param SCRIPT_FILENAME $document_root$real_script_name;
				fastcgi_param SCRIPT_NAME $real_script_name;
				fastcgi_param PATH_INFO $path_info;
			}
		}

3.ROUTE_TYPE_METHOD
  使用参数传递路径
  这种模式可以不用rewrite 这样所有的url 都类似  /index.php?m=login     通过 m 来传递路径(当然 m 可以换成其他参数 Router::$pathMethodKey)

  如果rewrite 配置如下
   A.Apache
		 #<IfModule mod_rewrite.c>
		RewriteEngine On
		RewriteBase /
		RewriteCond %{REQUEST_URI} !^/static
		RewriteCond %{REQUEST_FILENAME} !-f
		RewriteCond %{REQUEST_FILENAME} !-d
		
		RewriteRule ^(.*)$  index.php?m=$1&%{QUERY_STRING} [L]
		
		#</IfModule>
 
  	B.Nginx
	 	server {
	
		    if (!-f $request_filename) {
		        rewrite ^/(.*)$ /index.php?m=$1 last;
		        break;
		    }
		
			location ~ .*\.(php|php5)
			{
				#fastcgi_index index.php; #如果有请将这个条注释掉
				
			}
		}