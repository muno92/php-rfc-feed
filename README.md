# PHP RFC Feed

[![CI](https://github.com/muno92/php-rfc-feed/actions/workflows/ci.yml/badge.svg)](https://github.com/muno92/php-rfc-feed/actions/workflows/ci.yml)

Unofficial Atom feed for tracking PHP RFC (Request for Comments) updates.

https://php-rfc-feed.muno92.dev/feed.xml

This application monitors the https://wiki.php.net/rfc and generates an Atom feed to track status changes of PHP RFCs.

## Development

### Requirements

- PHP 8.4
- Composer
- Docker
- Docker Compose

### Setup

```bash
git clone https://github.com/muno92/php-rfc-feed.git
cd php-rfc-feed
composer install
```

### Testing

Run the test suite:

```bash
php bin/phpunit
```

### Production Build

The application can be built and run using Docker Compose for production verification:

#### Build

```bash
docker compose build
```

#### Fetch RFCs

```bash
docker compose run --rm app php bin/console rfc:fetch
```


#### Generate Feed

```bash
docker compose run --rm app php bin/console rfc:generate-feed --output=feed.xml --limit=10
```
