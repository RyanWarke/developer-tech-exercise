## Installation

This Developer Technical exercise has been developed with Laravel, using Sail, and Vue.js.

### Step 1:

Clone this repository:

```
git clone https://github.com/RyanWarke/developer-tech-exercise.git
```

### Step 2:

Install dependencies via composer:

```
composer install
```

### Step 3:

Decrypt the .env.encrypted file to expose the .env file:

```
php artisan env:decrypt --key="{replace with key sent during submission}"
```

### Step 4:

Build and start Sail:

```
./vendor/bin/sail up --build
```

### Step 5:

Run the database migrations:

```
./vendor/bin/sail artisan migrate
```

### Step 6:

Install the frontend dependencies:

```
npm install
```

### Step 7:

Run the frontend:

```
npm run dev
```

### Step 8:

Start the queue worker:

```
./vendor/bin/sail artisan queue:work
```

### Step 9:

Visit the application in your browser:

```
http://localhost/
```