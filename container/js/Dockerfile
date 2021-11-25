FROM node:14.15.5-alpine3.13

RUN apk --no-cache add su-exec entr

COPY entrypoint.sh /usr/local/bin/custom-entrypoint
ENTRYPOINT ["custom-entrypoint"]

WORKDIR /srv
