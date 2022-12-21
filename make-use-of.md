```
{
    "name": "bravedave/min",
    "license": "MIT",
    "minimum-stability": "dev",
    "autoload": {
        "psr-4": {
            "": "src/app"
        }
    },
    "require": {
        "bravedave/esse": "dev-main"
    },
    "repositories": {
        "bravedave-esse": {
            "type": "git",
            "url": "https://github.com/bravedave/esse"
        }
    }
}
```

```
composer u

cp -r vendor/bravedave/esse/src/app/ src/
cp -r vendor/bravedave/esse/www .

cd www
php -S localhost:8080 _mvp.php
```