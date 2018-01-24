Used to generate files required to set env, secrets and version

# Usage: #

php deploy.php [env={ENV_NAME_JSON}]
               [env-production={ENV_PRODUCTION_NAME_JSON}]
               [version={VERSION_STRING_JSON}]
               [secrets={SECRETS_JSON}]

               [env-file-json={ENV_NAME_FILE_JSON}]
               [env-production-file-json={ENV_PRODUCTION_NAME_FILE_JSON}]
               [version-file-json={VERSION_STRING_FILE_JSON}]
               [secrets-file-json={SECRETS_FILE_JSON}]

               [env-file={ENV_NAME_FILE_PHP}]
               [env-production-file={ENV_PRODUCTION_NAME_FILE_PHP}]
               [version-file={VERSION_STRING_FILE_PHP}]
               [secrets-file={SECRETS_FILE_PHP}]

               [path-app-data={PATH_DEFAULT}]
               [path-app-config={PATH}]
               [server-config-filename={SERVER_CONFIG_FILE}]
               [server-config-key={SERVER_CONFIG_KEY}]


               [permissions-app-data-folder={FOLDER_PERMISSIONS}]
               [permissions-app-data-file={FILE_PERMISSIONS}]
               [permissions-app-config-folder={FOLDER_PERMISSIONS}]
               [permissions-app-config-file={FILE_PERMISSIONS}]

               [gitignore-entries={GITIGNORE_ENTRIES_JSON}]

## Standard Options ##

env='prod'                                      // name of current env
env-production='prod'                           // name of production env
version='unknown'                               // version of current code
secrets='{}'                                    // secrets as json (not recommended)

## JSON file options - you may supply values from a file ##

env-file-json=null                              // file path to json data file (some-file.json)
env-production-file-json=null                   // file path to json data file (some-file.json)
version-file-json=null                          // file path to json data file (some-file.json)
secrets-file-json=null                          // file path to json data file (some-file.json)

## PHP file options - you may provide you own PHP file to provide values ##

env-file=null                                   // file path to custom data file (some-file.php)
env-production-file=null                        // file path to custom data file (some-file.php)
version-file=null                               // file path to custom data file (some-file.php)
secrets-file=null                               // file path to custom data file (some-file.php)

## Over-ride defaults - you may change the default data and config paths ##

NOTE: changes to these must also be done on bootstrap

path-app-data='/../../../../../data/_server'    // path to app data folder
path-app-config=/../../../../../config          // path to app config
server-config-filename=_server.php              // name of app config file for server AKA: /../../../../../config/prod/_server.php
server-config-key=_server                       // config key for server config

## Over-ride defaults - you may change the default file permissions ##

permissions-app-data-folder=0755                // app data folder permissions
permissions-app-data-file=0655                  // app data file permissions
permissions-app-config-folder=0755              // app config folder permissions
permissions-app-config-file=0655                // app config file permissions

## .gitignore entries may be added for the data folder (secrets are added by default) ##

gitignore-entries=[]                            // gitignore entries as JSON array

## EXAMPLES ##

Set values directly:

```
php deploy.php env=\"my-env\" env-production=\"my-prod-env\" version=\"my-version\" secrets={\"my-secret\":\"NOT_RECOMMENDED\"}
```

Set values from JSON file:

```
php deploy.php env-file-json=./../data/env.json.dist env-production-file-json=./../data/env-production.json.dist version-file-json=./../data/version.json.dist secrets-file-json=./../data/secrets.json.dist
```

Set custom php files:

```
php deploy.php env-file=./../data/env.php.dist env-production-file=./../data/env-production.php.dist version-file=./../data/version.php.dist secrets-file=./../data/secrets.php.dist
```

Mixed:

```
php deploy.php env=\"my-env\" env-production=\"my-prod-env\" version-file-json=./../data/version.json.dist secrets-file=./../data/secrets.php.dist
```

