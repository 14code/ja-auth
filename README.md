# 14code/ja-auth
Just another Auth server...

## Docker commands
### Running
docker build -t ja-auth-app .   
docker run -d --rm -v "$PWD":/app -w /app -p 8081:8080 ja-auth-app     
docker exec -it ... /bin/sh    

Open http://localhost:8081/

### Contributing
docker run -it --rm -v "$PWD":/app -v "$HOME/.ssh":/root/.ssh -w /app composer update
docker run -it --rm -v "$PWD":/app -w /app php:7.2-cli vendor/bin/phpunit tests
