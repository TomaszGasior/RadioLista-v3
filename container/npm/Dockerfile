FROM node:14-alpine3.10

RUN apk --no-cache add su-exec entr

COPY entrypoint.sh /usr/local/bin/custom-entrypoint
ENTRYPOINT ["custom-entrypoint"]

WORKDIR /srv
