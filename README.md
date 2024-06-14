### About This Project
This PHP project allows you to upload videos to Vimeo using the Vimeo API. The application provides a simple interface to upload videos and obtain direct links to manage them on Vimeo.

**Requirements**
- PHP 7.4 or higher
- Composer
- Vimeo account with developer credentials (Client ID, Client Secret, and Access Token)

**Installation**

1. Clone the repository:
   ```sh
   git clone https://github.com/your-username/repository-name.git
   cd repository-name
   ```

2. Install Composer dependencies:
   ```sh
   composer install
   ```

3. Rename the file `config.sample.php` to `config.php` and fill in the Vimeo credentials:
   ```php
   <?php
   return [
       'clientId' => 'your_client_id',
       'clientSecret' => 'your_client_secret',
       'accessToken' => 'your_access_token'
   ];
   ?>
   ```

4. Make sure you have a video file to test the upload (e.g., `testvideo.mp4`).

**Usage**

### Video Upload
1. Include the Composer autoload and the `VideoService` class in your PHP script:
   ```php
   <?php
   require __DIR__ . '/vendor/autoload.php';
   require __DIR__ . '/VideoService.php';

   use Phppot\VideoService;

   // Load configurations
This project is licensed under the MIT license. Refer to the `LICENSE` file for more details.
