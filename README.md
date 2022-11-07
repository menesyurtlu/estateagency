## About Estate Agency API

This project is written for solving problems that Estate Agency had. This project has Repository classes, so if you want to use custom dataprovider rather than Laravel Eloquent supports, you just need to write new repository class and connect your data provider easily.

## Setting Google Maps API

To use this project as expected, you need to create an `API Key` from `Google Cloud Platform`. And enable `Distance Matrix API` from Google Cloud Platform Library<br>
After creating the API Key, update `.env` file, set `GOOGLE_MAPS_DISTANCE_API_KEY`.

## Setting Estate Address
While calculating distances and durations to appointment's destination app needs a global variable that defines the address of estate. So just add another line to `.env` file to define it.

Example `.env` file <br>
`GOOGLE_MAPS_DISTANCE_API_KEY=yourApiKey`<br>
`ESTATE_ADDRESS=cm27pj`

## Truncating tables
As of we are using Laravel Framework, it has some console commands to make everything easy. If we want to truncate all tables we just need to run `php artisan migrate:fresh`. After running this command successfully we need to run another command `php artisan passport:install --force`. Via this command Laravel Passport creates/replaces current keys to use with authentication. We used `--force` option, because after truncating all tables on database, it removes keys stored in database, too. So we need to create them and replace the key files in storage folder.
