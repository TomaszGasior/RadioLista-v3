FROM node:20.10.0-alpine3.18

RUN apk --no-cache add entr

COPY entrypoint.sh /usr/local/bin/custom-entrypoint
ENTRYPOINT ["custom-entrypoint"]

WORKDIR /srv
