# Supabase `gotrue-php`

PHP Client library to interact with Supabase GoTrue.

> **Note:** This repository is in Alpha and is not ready for production usage. API's will change as it progresses to initial release.


### TODO

- [ ] Support for PHP 7.4 
- [ ] Running unit and integration tests together results in test failures 


## Quick Start Guide

### Installing the module

```bash
composer require supabase/gotrue-php
```

### Connecting to the gotrue backend

```php

use Supabase\GoTrue\GoTrueClient;

$client = new GoTrueClient($reference_id, $api_key);
```

### Examples

[Examples directory](examples)

### Testing

Setup the testing Env

```
cp .env.example tests/.env
```

#### For the `REFERENCE_ID`
Once signed on to the dashboard, navigate to, Project >> Project Settings >> General settings. Copy the Reference ID for use in the `.env`.

#### For the `API_KEY`
Once signed on to the dashboard, navigate to, Project >> Project Settings >> API >> Project API keys. Choose either the `anon` `public` or the `service_role` key.

Populate the `tests/.env` to include `REFERENCE_ID` and `API_KEY`.

#### Running all tests

```
vendor/bin/phpunit
```
