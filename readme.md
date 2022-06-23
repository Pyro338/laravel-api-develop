## Overview

This package provides core Gamebetr API functionality which ties together all non-user facing APIs like bank, paybetr, gamecenter, etc.

## Assets

Image and video assets will be ignored by git and must be stored on a static file server. The static file server is loaded via config and can be set in `.env`:

`STATIC_FILE_URI="https://static.cdn1.io/playerapi/assets"`

CSS/JS/etc. is stored in the `assets` folder in git and then moved into the `public` folder via:

`php artisan vendor:publish --provider="Gamebetr\Api\Providers\ApiServiceProvider" --force`

### NPM

Javascript and CSS needs to be built with NPM.

Run:

`npm install`
`npm run dev`

Optional:

`npm uninstall webpack`
`npm install webpack`

Update api-client-js:

`npm update @gamebetr/api-client-js`
