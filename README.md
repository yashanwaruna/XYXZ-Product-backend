## XYZ Product

#### Step 1: Clone Project
 
``git clone repourl project-name``
 
``composer install``

#### Step 2: Database Configuration

Create a database and configure the env file.  

``cp .ent.example .env``

#### Step 3: Add Database migrations

``php artisan migrate``

#### Step 4: Add Passport

``php artisan passport:install``

#### Step 5: Seed data

`` php artisan db:seed``

#### Step 6: Run project

`` php artisan serve``