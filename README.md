# How to install the project

1. Clone the repository by typing in your terminal:

-   git clone https://github.com/juswaa101/PhrasioAI.git

2. Make Sure You Have Composer Installed and Php to run this project, if you dont have one click the link below to install.

-   https://getcomposer.org/ - Composer
-   https://www.apachefriends.org/ - XAMPP

3. Open the project in any preferred ide and open terminal within the project.

3. In .env file, find OPENAI_API_KEY and copy your own api key
- where to provide api key? go to https://platform.openai.com/account/api-keys, create your new secret key and copy the provided key and paste it in the env.

5. Run the project by typing php artisan serve and npm run dev.

6. Open browser and type localhost:8000

 - <p>Note: If error occured, try to delete this file under bootstrap/cache/config.php and then go to terminal and type php artisan config:cache, and run again</p>
