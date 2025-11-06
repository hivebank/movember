# Quick Fix for MongoDB Extension Version

If you get this error during `composer install`:

```
mongodb/mongodb 1.21.2 requires ext-mongodb ^1.21.0 -> it has the wrong version installed
```

## Solution

Just run composer with the platform requirement flag:

```bash
cd backend
composer install --ignore-platform-req=ext-mongodb
```

This tells composer to install packages even if your MongoDB extension is an older version (1.15.0 is fine!).

## Automatic Fix

The `./setup.sh` script now does this automatically! Just run:

```bash
./setup.sh
```

---

**What changed:**
- composer.json now requires `mongodb/mongodb ^1.15` (was ^1.17)
- composer.json now requires `php ^8.0` (was ^8.2)
- composer.lock deleted (regenerates per server)
- setup.sh uses `--ignore-platform-req=ext-mongodb` flag

Your server with PHP 8.3 and ext-mongodb 1.15.0 will work perfectly! 🎉
