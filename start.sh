#!/bin/sh

# Set the timezone. Base image does not contain the setup-timezone script, so an alternate way is used.
if [ "$CONTAINER_TIMEZONE" ]; then
    cp /usr/share/zoneinfo/${CONTAINER_TIMEZONE} /etc/localtime && \
	echo "${CONTAINER_TIMEZONE}" >  /etc/timezone && \
	echo "Container timezone set to: $CONTAINER_TIMEZONE"
fi

# Force immediate synchronisation of the time and start the time-synchronization service.
# In order to be able to use ntpd in the container, it must be run with the SYS_TIME capability.
# In addition you may want to add the SYS_NICE capability, in order for ntpd to be able to modify its priority.
ntpd -s

# Apache server name change
if [ ! -z "$APACHE_SERVER_NAME" ]
	then
		sed -i "s/#ServerName www.example.com:80/ServerName $APACHE_SERVER_NAME/" /etc/apache2/httpd.conf
		echo "Changed server name to '$APACHE_SERVER_NAME'..."
	else
		echo "NOTICE: Change 'ServerName' globally and hide server message by setting environment variable >> 'APACHE_SERVER_NAME=your.server.name' in docker command or docker-compose file"
fi

# PHP Config
if [ ! -z "$PHP_SHORT_OPEN_TAG" ]; then sed -i "s/\;\?\\s\?short_open_tag = .*/short_open_tag = $PHP_SHORT_OPEN_TAG/" /etc/php7/php.ini && echo "Set PHP short_open_tag = $PHP_SHORT_OPEN_TAG..."; fi
if [ ! -z "$PHP_OUTPUT_BUFFERING" ]; then sed -i "s/\;\?\\s\?output_buffering = .*/output_buffering = $PHP_OUTPUT_BUFFERING/" /etc/php7/php.ini && echo "Set PHP output_buffering = $PHP_SHORT_OUTPUT_BUFFERING..."; fi
if [ ! -z "$PHP_OPEN_BASEDIR" ]; then sed -i "s/\;\?\\s\?open_basedir = .*/open_basedir = $PHP_OPEN_BASEDIR/" /etc/php7/php.ini && echo "Set PHP open_basedir = $PHP_OPEN_BASEDIR..."; fi
if [ ! -z "$PHP_MAX_EXECUTION_TIME" ]; then sed -i "s/\;\?\\s\?max_execution_time = .*/max_execution_time = $PHP_MAX_EXECUTION_TIME/" /etc/php7/php.ini && echo "Set PHP max_execution_time = $PHP_MAX_EXECUTION_TIME..."; fi
if [ ! -z "$PHP_MAX_INPUT_TIME" ]; then sed -i "s/\;\?\\s\?max_input_time = .*/max_input_time = $PHP_MAX_INPUT_TIME/" /etc/php7/php.ini && echo "Set PHP max_input_time = $PHP_MAX_INPUT_TIME..."; fi
if [ ! -z "$PHP_MAX_INPUT_VARS" ]; then sed -i "s/\;\?\\s\?max_input_vars = .*/max_input_vars = $PHP_MAX_INPUT_VARS/" /etc/php7/php.ini && echo "Set PHP max_input_vars = $PHP_MAX_INPUT_VARS..."; fi
if [ ! -z "$PHP_MEMORY_LIMIT" ]; then sed -i "s/\;\?\\s\?memory_limit = .*/memory_limit = $PHP_MEMORY_LIMIT/" /etc/php7/php.ini && echo "Set PHP memory_limit = $PHP_MEMORY_LIMIT..."; fi
if [ ! -z "$PHP_ERROR_REPORTING" ]; then sed -i "s/\;\?\\s\?error_reporting = .*/error_reporting = $PHP_ERROR_REPORTING/" /etc/php7/php.ini && echo "Set PHP error_reporting = $PHP_ERROR_REPORTING..."; fi
if [ ! -z "$PHP_DISPLAY_ERRORS" ]; then sed -i "s/\;\?\\s\?display_errors = .*/display_errors = $PHP_DISPLAY_ERRORS/" /etc/php7/php.ini && echo "Set PHP display_errors = $PHP_DISPLAY_ERRORS..."; fi
if [ ! -z "$PHP_DISPLAY_STARTUP_ERRORS" ]; then sed -i "s/\;\?\\s\?display_startup_errors = .*/display_startup_errors = $PHP_DISPLAY_STARTUP_ERRORS/" /etc/php7/php.ini && echo "Set PHP display_startup_errors = $PHP_DISPLAY_STARTUP_ERRORS..."; fi
if [ ! -z "$PHP_LOG_ERRORS" ]; then sed -i "s/\;\?\\s\?log_errors = .*/log_errors = $PHP_LOG_ERRORS/" /etc/php7/php.ini && echo "Set PHP log_errors = $PHP_LOG_ERRORS..."; fi
if [ ! -z "$PHP_LOG_ERRORS_MAX_LEN" ]; then sed -i "s/\;\?\\s\?log_errors_max_len = .*/log_errors_max_len = $PHP_LOG_ERRORS_MAX_LEN/" /etc/php7/php.ini && echo "Set PHP log_errors_max_len = $PHP_LOG_ERRORS_MAX_LEN..."; fi
if [ ! -z "$PHP_IGNORE_REPEATED_ERRORS" ]; then sed -i "s/\;\?\\s\?ignore_repeated_errors = .*/ignore_repeated_errors = $PHP_IGNORE_REPEATED_ERRORS/" /etc/php7/php.ini && echo "Set PHP ignore_repeated_errors = $PHP_IGNORE_REPEATED_ERRORS..."; fi
if [ ! -z "$PHP_REPORT_MEMLEAKS" ]; then sed -i "s/\;\?\\s\?report_memleaks = .*/report_memleaks = $PHP_REPORT_MEMLEAKS/" /etc/php7/php.ini && echo "Set PHP report_memleaks = $PHP_REPORT_MEMLEAKS..."; fi
if [ ! -z "$PHP_HTML_ERRORS" ]; then sed -i "s/\;\?\\s\?html_errors = .*/html_errors = $PHP_HTML_ERRORS/" /etc/php7/php.ini && echo "Set PHP html_errors = $PHP_HTML_ERRORS..."; fi
if [ ! -z "$PHP_ERROR_LOG" ]; then sed -i "s/\;\?\\s\?error_log = .*/error_log = $PHP_ERROR_LOG/" /etc/php7/php.ini && echo "Set PHP error_log = $PHP_ERROR_LOG..."; fi
if [ ! -z "$PHP_POST_MAX_SIZE" ]; then sed -i "s/\;\?\\s\?post_max_size = .*/post_max_size = $PHP_POST_MAX_SIZE/" /etc/php7/php.ini && echo "Set PHP post_max_size = $PHP_POST_MAX_SIZE..."; fi
if [ ! -z "$PHP_DEFAULT_MIMETYPE" ]; then sed -i "s/\;\?\\s\?default_mimetype = .*/default_mimetype = $PHP_DEFAULT_MIMETYPE/" /etc/php7/php.ini && echo "Set PHP default_mimetype = $PHP_DEFAULT_MIMETYPE..."; fi
if [ ! -z "$PHP_DEFAULT_CHARSET" ]; then sed -i "s/\;\?\\s\?default_charset = .*/default_charset = $PHP_DEFAULT_CHARSET/" /etc/php7/php.ini && echo "Set PHP default_charset = $PHP_DEFAULT_CHARSET..."; fi
if [ ! -z "$PHP_FILE_UPLOADS" ]; then sed -i "s/\;\?\\s\?file_uploads = .*/file_uploads = $PHP_FILE_UPLOADS/" /etc/php7/php.ini && echo "Set PHP file_uploads = $PHP_FILE_UPLOADS..."; fi
if [ ! -z "$PHP_UPLOAD_TMP_DIR" ]; then sed -i "s/\;\?\\s\?upload_tmp_dir = .*/upload_tmp_dir = $PHP_UPLOAD_TMP_DIR/" /etc/php7/php.ini && echo "Set PHP upload_tmp_dir = $PHP_UPLOAD_TMP_DIR..."; fi
if [ ! -z "$PHP_UPLOAD_MAX_FILESIZE" ]; then sed -i "s/\;\?\\s\?upload_max_filesize = .*/upload_max_filesize = $PHP_UPLOAD_MAX_FILESIZE/" /etc/php7/php.ini && echo "Set PHP upload_max_filesize = $PHP_UPLOAD_MAX_FILESIZE..."; fi
if [ ! -z "$PHP_MAX_FILE_UPLOADS" ]; then sed -i "s/\;\?\\s\?max_file_uploads = .*/max_file_uploads = $PHP_MAX_FILE_UPLOADS/" /etc/php7/php.ini && echo "Set PHP max_file_uploads = $PHP_MAX_FILE_UPLOADS..."; fi
if [ ! -z "$PHP_ALLOW_URL_FOPEN" ]; then sed -i "s/\;\?\\s\?allow_url_fopen = .*/allow_url_fopen = $PHP_ALLOW_URL_FOPEN/" /etc/php7/php.ini && echo "Set PHP allow_url_fopen = $PHP_ALLOW_URL_FOPEN..."; fi
if [ ! -z "$PHP_ALLOW_URL_INCLUDE" ]; then sed -i "s/\;\?\\s\?allow_url_include = .*/allow_url_include = $PHP_ALLOW_URL_INCLUDE/" /etc/php7/php.ini && echo "Set PHP allow_url_include = $PHP_ALLOW_URL_INCLUDE..."; fi
if [ ! -z "$PHP_DEFAULT_SOCKET_TIMEOUT" ]; then sed -i "s/\;\?\\s\?default_socket_timeout = .*/default_socket_timeout = $PHP_DEFAULT_SOCKET_TIMEOUT/" /etc/php7/php.ini && echo "Set PHP default_socket_timeout = $PHP_DEFAULT_SOCKET_TIMEOUT..."; fi
if [ ! -z "$PHP_DATE_TIMEZONE" ]; then sed -i "s/\;\?\\s\?date.timezone = .*/date.timezone = $PHP_DATE_TIMEZONE/" /etc/php7/php.ini && echo "Set PHP date.timezone = $PHP_DATE_TIMEZONE..."; fi
if [ ! -z "$PHP_PDO_MYSQL_CACHE_SIZE" ]; then sed -i "s/\;\?\\s\?pdo_mysql.cache_size = .*/pdo_mysql.cache_size = $PHP_PDO_MYSQL_CACHE_SIZE/" /etc/php7/php.ini && echo "Set PHP pdo_mysql.cache_size = $PHP_PDO_MYSQL_CACHE_SIZE..."; fi
if [ ! -z "$PHP_PDO_MYSQL_DEFAULT_SOCKET" ]; then sed -i "s/\;\?\\s\?pdo_mysql.default_socket = .*/pdo_mysql.default_socket = $PHP_PDO_MYSQL_DEFAULT_SOCKET/" /etc/php7/php.ini && echo "Set PHP pdo_mysql.default_socket = $PHP_PDO_MYSQL_DEFAULT_SOCKET..."; fi
if [ ! -z "$PHP_SESSION_SAVE_HANDLER" ]; then sed -i "s/\;\?\\s\?session.save_handler = .*/session.save_handler = $PHP_SESSION_SAVE_HANDLER/" /etc/php7/php.ini && echo "Set PHP session.save_handler = $PHP_SESSION_SAVE_HANDLER..."; fi
if [ ! -z "$PHP_SESSION_SAVE_PATH" ]; then sed -i "s/\;\?\\s\?session.save_path = .*/session.save_path = $PHP_SESSION_SAVE_PATH/" /etc/php7/php.ini && echo "Set PHP session.save_path = $PHP_SESSION_SAVE_PATH..."; fi
if [ ! -z "$PHP_SESSION_USE_STRICT_MODE" ]; then sed -i "s/\;\?\\s\?session.use_strict_mode = .*/session.use_strict_mode = $PHP_SESSION_USE_STRICT_MODE/" /etc/php7/php.ini && echo "Set PHP session.use_strict_mode = $PHP_SESSION_USE_STRICT_MODE..."; fi
if [ ! -z "$PHP_SESSION_USE_COOKIES" ]; then sed -i "s/\;\?\\s\?session.use_cookies = .*/session.use_cookies = $PHP_SESSION_USE_COOKIES/" /etc/php7/php.ini && echo "Set PHP session.use_cookies = $PHP_SESSION_USE_COOKIES..."; fi
if [ ! -z "$PHP_SESSION_COOKIE_SECURE" ]; then sed -i "s/\;\?\\s\?session.cookie_secure = .*/session.cookie_secure = $PHP_SESSION_COOKIE_SECURE/" /etc/php7/php.ini && echo "Set PHP session.cookie_secure = $PHP_SESSION_COOKIE_SECURE..."; fi
if [ ! -z "$PHP_SESSION_NAME" ]; then sed -i "s/\;\?\\s\?session.name = .*/session.name = $PHP_SESSION_NAME/" /etc/php7/php.ini && echo "Set PHP session.name = $PHP_SESSION_NAME..."; fi
if [ ! -z "$PHP_SESSION_COOKIE_LIFETIME" ]; then sed -i "s/\;\?\\s\?session.cookie_lifetime = .*/session.cookie_lifetime = $PHP_SESSION_COOKIE_LIFETIME/" /etc/php7/php.ini && echo "Set PHP session.cookie_lifetime = $PHP_SESSION_COOKIE_LIFETIME..."; fi
if [ ! -z "$PHP_SESSION_COOKIE_PATH" ]; then sed -i "s/\;\?\\s\?session.cookie_path = .*/session.cookie_path = $PHP_SESSION_COOKIE_PATH/" /etc/php7/php.ini && echo "Set PHP session.cookie_path = $PHP_SESSION_COOKIE_PATH..."; fi
if [ ! -z "$PHP_SESSION_COOKIE_DOMAIN" ]; then sed -i "s/\;\?\\s\?session.cookie_domain = .*/session.cookie_domain = $PHP_SESSION_COOKIE_DOMAIN/" /etc/php7/php.ini && echo "Set PHP session.cookie_domain = $PHP_SESSION_COOKIE_DOMAIN..."; fi
if [ ! -z "$PHP_SESSION_COOKIE_HTTPONLY" ]; then sed -i "s/\;\?\\s\?session.cookie_httponly = .*/session.cookie_httponly = $PHP_SESSION_COOKIE_HTTPONLY/" /etc/php7/php.ini && echo "Set PHP session.cookie_httponly = $PHP_SESSION_COOKIE_HTTPONLY..."; fi

# enable xdebug coverage for testing with phpunit (already installed)
if [ ! -z "$PHP_XDEBUG_ENABLED" ]
	then
		echo "Enable XDebug..."
		echo 'zend_extension=/usr/lib/php7/modules/xdebug.so' >> /etc/php7/php.ini;
		echo 'xdebug.coverage_enable=On' >> /etc/php7/php.ini;
		echo 'xdebug.remote_enable=1' >> /etc/php7/php.ini;
		echo 'xdebug.remote_connect_back=1' >> /etc/php7/php.ini;
		echo 'xdebug.remote_log=/tmp/xdebug.log' >> /etc/php7/php.ini;
		echo 'xdebug.remote_autostart=true' >> /etc/php7/php.ini;
fi

# Start (ensure apache2 PID not left behind first) to stop auto start crashes if didn't shut down properly

echo "Clearing any old processes..."
rm -f /run/apache2/apache2.pid
rm -f /run/apache2/httpd.pid

echo "Starting apache..."
httpd -D FOREGROUND
