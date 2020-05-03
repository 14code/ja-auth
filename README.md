# 14code/ja-auth
Just another Auth server...

## Docker commands
### Running
docker build -t ja-auth-app .   
docker run -d --rm -v "$PWD":/app -w /app -p 8081:8080 ja-auth-app     
docker exec -it ... /bin/sh    

Open http://localhost:8081/

### Install keys

Private key:  
docker exec -it $container_name openssl genrsa -out .keys/private.key 2048  
or just run  
docker run -it --rm -v "$PWD":/app -w /app php:7.3-cli openssl genrsa -out .keys/private.key 2048

Public key:  
docker exec -it $container_name openssl rsa -in .keys/private.key -pubout -out .keys/public.key
or just run  
docker run -it --rm -v "$PWD":/app -w /app php:7.3-cli openssl rsa -in .keys/private.key -pubout -out .keys/public.key

Encryption key:  
docker exec -it $container_name php -r 'echo base64_encode(random_bytes(32)), PHP_EOL;' > .keys/encryption.key
or just run  
docker run -it --rm -v "$PWD":/app -w /app php:7.3-cli php -r 'echo base64_encode(random_bytes(32)), PHP_EOL;' > .keys/encryption.key


### Contributing
docker run -it --rm -v "$PWD":/app -v "$HOME/.ssh":/root/.ssh -w /app composer update

docker run -it --rm -v "$PWD":/app -w /app php:7.2-cli vendor/bin/phpunit tests


### Inspirations
http://paul-m-jones.com/post/2016/04/12/psr-7-and-session-cookies/
https://github.com/relayphp/Relay.Middleware/blob/1.x/src/SessionHeadersHandler.php
https://www.php.net/manual/de/class.sessionhandlerinterface.php

**Token Introspection Endpoint**  
https://www.oauth.com/oauth2-servers/token-introspection-endpoint/