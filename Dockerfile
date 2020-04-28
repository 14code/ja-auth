FROM php:7.4-cli-alpine
RUN apk update && apk upgrade
COPY . /app
WORKDIR /app
CMD [ "php", "-S", "0.0.0.0:8080", "-t" ,"./public/" ]
