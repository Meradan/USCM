# USCM

Web site to support the [USCM RPG](https://www.uscm.se/) campaign

## Prerequisite

1. [Install php](https://www.php.net/manual/en/install.php)

## Setup

1. Open ssh tunnel to server
```sh
ssh -p $port $user@$machine
```

2. Copy /var/www/html/skynet/config.php from server to repository root

3. Edit config.php
```php
$db_host="localhost:3306";
```

4. Open tunnel forwarding to remote database
```sh
ssh -p $port -N -L 3306:127.0.0.1:3306 $user@$machine
```

5. Run php server
```sh
php -S localhost:8000
```

6. Open [http://localhost:8000](http://localhost:8000)
