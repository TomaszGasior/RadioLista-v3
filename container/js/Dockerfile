FROM node:14.21.2-alpine3.17

RUN apk --no-cache add su-exec entr

COPY entrypoint.sh /usr/local/bin/custom-entrypoint
ENTRYPOINT ["custom-entrypoint"]

WORKDIR /srv
