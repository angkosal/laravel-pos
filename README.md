<p align="center">
    <h1 align="center">POS System Using Laravel</h1>
</p>

The project was created while recording video "Create POS System Using Laravel"

## Installation

### Requirements

For system requirements you [Check Laravel Requirement](https://laravel.com/docs/7.x/installation#server-requirements)

### Clone the repository from github.

    git clone https://github.com/angkosal/laravel-pos.git [YourDirectoryName]
    
The command installs the project in a directory named `YourDirectoryName`. You can choose a different
directory name if you want.

### Install dependencies

Laravel utilizes [Composer](https://getcomposer.org/) to manage its dependencies. So, before using Laravel, make sure you have Composer installed on your machine.

    cd YourDirectoryName
    composer install

### Config file
Rename or copy `.env.example` file to `.env` 
1.`php artisan key:generate` to generate app key.
1. Set your database credentials in your `.env` file
1. Set your `APP_URL` in your `.env` file.
### Install Node Dependencies
1. `npm install` to install node dependencies
1. `npm run dev` to build our javascript
### Create Admin Account
1. `php artisan tinker` and than paste 
    ```php
    App\Models\User::create([
        'first_name' => 'Admin', 
        'last_name' => 'admin', 
        'email'=>'admin@gmail.com',
        'password' => bcrypt('admin')
    ]
    ```
   hit enter.
### Run Server
1. `php artisan serve` or Laravel Homestead
1. Visit `localhost:8000` in your browser
1. Visit `/admin` if you want to access the admin. Email: `admin@gmail.com`, Password: `admin`.

