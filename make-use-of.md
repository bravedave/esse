1. Create a composer file
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

2. update to install files
```
composer u

cp -r vendor/bravedave/esse/src/app/ src/
cp -r vendor/bravedave/esse/www .
```

3. Run
```
cd www
php -S localhost:8080 _mvp.php
```

you will get a logon screen, but there are no users or database

1. a data folder was created in src/data
   1. rename the esse-defaults-sample.json to esse-defaults.json
   2. edit esse-defaults.json, set authentication false
   3. refresh - no logon requirement
      1. create a user in users with a password
      2. re-enable auth
      3. etc .. etc..

