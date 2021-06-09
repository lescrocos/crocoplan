#!/usr/bin/env bash
set -e

[[ -f config/jwt/private.pem ]] || (
  mkdir -p config/jwt
  JWT_PASSPHRASE=${JWT_PASSPHRASE:-$(grep '^JWT_PASSPHRASE=' .env | cut -f 2 -d '=')}
  echo "$JWT_PASSPHRASE" | openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
  echo "$JWT_PASSPHRASE" | openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout
  chmod -R 640 config/jwt/*
  chgrp -R www-data config/jwt/*
)

apache2-foreground
