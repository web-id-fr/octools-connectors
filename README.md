# 🐙🔧 Octools-Connectors

Octools-Connectors is a collection of services for [Octools.io](https://octools.io/). It's a set of connectors that can be used to connect different services.

## ⚙️ Requirements

- PHP >= 8.1
- Laravel >= 10.0
- Laravel Nova >= 4.0
- Octools >= 0.1

## 📦 Installation

1. With Composer :

    ```bash
    composer require webid/octools-connectors
    ```

2. After installation, you must publish the necessary assets using the following command :

    ```php
    php artisan vendor:publish --provider="Webid\OctoolsGithub\OctoolsGithubServiceProvider "
    ```

    ```php
    php artisan vendor:publish --provider="Webid\OctoolsGithub\OctoolsGryzzlyServiceProvider"
    ```
   
    ```php
    php artisan vendor:publish --provider="Webid\OctoolsGithub\OctoolsSlackServiceProvider"
    ```

## 📝 Configuration

For each connector, you must add the necessary configuration in the `.env` file. You can find the variables to add in the `config/octools-<services_name>.php` file.

