# Promocode System

## Project Description

The project is a virtual wallet system built with [Laravel 8](https://laravel.com) and [Google Geocoding API](https://developers.google.com/maps/documentation/geocoding/start). The features of this project include

1. Admin Authentication
2. Generation of promo codes
3. Management of promo codes

## Project Setup

### Cloning the GitHub Repository.

Clone the repository to your local machine by running the terminal command below.

```bash
git clone https://github.com/Ojsholly/safeboda-promocode
```

### Setup Database

Create your a MySQL database and note down the required connection parameters. (DB Host, Username, Password, Name)

### Install Composer Dependencies

Navigate to the project root directory via terminal and run the following command.

```bash
composer install
```

### Create a copy of your .env file

Run the following command

```bash
cp .env.example .env
```

This should create an exact copy of the .env.example file. Name the newly created file .env and update it with your local environment variables (database connection info and others).

### Generate an app encryption key

```bash
php artisan key:generate
```

### Migrate the database

```bash
php artisan migrate
```

### Add the required environment variables.

GOOGLE_MAPS_API_KEY

Examples of requests and the response for each endpoint can be found [here](https://documenter.getpostman.com/view/7024254/TVep98T3)

### License

[MIT](https://choosealicense.com/licenses/mit/)
