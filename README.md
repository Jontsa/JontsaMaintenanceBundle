# Symfony maintenance mode bundle

A small bundle for Symfony 4/5 which provides commands to put your application in maintenance break during which
all requests receive HTTP 503 response. Optionally you can whitelist your own IP-address or for example
your load balancer for health check pings.

## Features

- Put your site to maintenance mode with single command
- Responds with HTTP 503 to requests during maintenance
- Optional IP-address whitelist to allow traffic from
- Lightweight bundle

## Requirements

- Symfony 4 or 5
- PHP 7.1+
