# qdmingshang static mirror

This repository contains a static mirror of the public website pages and assets.

Deploy by serving this directory as the web root. The entry file is `index.html`.

## PHP admin test branch

The `php-admin-test` branch adds a lightweight test admin at `/rsadmin/`.

Deployment notes:

- Use PHP 7.4+ or PHP 8.2.
- Set the site index order to prefer `index.php` before `index.html` if you want the editable homepage preview.
- Make `rsadmin/data` writable by PHP after deployment:

```bash
chown -R www:www /www/wwwroot/qdmingshang/rsadmin/data
```

- First visit `/rsadmin/` and create your own admin account. No default password is committed.

Notes:

- This is a static mirror, not the original PHP CMS source code or database.
- Backend editing, dynamic publishing, and database features are not included.
- Some old resources were already missing on the origin site and are not included.
