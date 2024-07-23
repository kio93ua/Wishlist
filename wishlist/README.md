## Features
* Sass for stylesheets
* ES6 with Babel for JavaScript
* [Rollup](https://rollupjs.org/guide/en/) build script for frontend and wp-admin assets
* PSR-4 Autoloader
* ESLint


## Requirements
Make sure all dependencies have been installed before moving on:
* [WordPress](https://wordpress.org/) >= 5.3
* [PHP](http://php.net/manual/en/install.php) >= 7.4
* [Composer](https://getcomposer.org/download/)
* [Node.js](http://nodejs.org/) ^14.17.0 or >=16.0.0
* [Yarn](https://yarnpkg.com/en/docs/install)


### Build commands
* `yarn start` — Start your development process, this will compile and live reload your browser or inject css when possible while in development
* `yarn build` — Compile and optimize the files

#### Additional commands
* `yarn zip` — Compile and optimize the files to WordPress plugin
* `yarn clean` — Remove your `assets/dist` folder, `vendor` and `node_modules`
* `yarn reinit` — Remove your `assets/dist` and `node_modules` folder and reinstall node dependencies and `vendor`
* `yarn lint:scripts` — Run ESLint against your source files and build scripts

#### License
This project is licensed under the [MIT License](https://opensource.org/licenses/MIT).
