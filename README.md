<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://ajssarimg2.mediatriple.net/936788.jpg.webp?w=1200&h=675" width="400"></a></p>

## Real time NBA Simulation (Laravel, Vuejs, Laravel Echo, Docker)

## Installation Steps

1. Clone the repository

```bash
git clone https://github.com/rahimkomac/nba-simulation.git
```

2. Change the working directory

```bash
cd nba-simulation
```

3. set env

```bash
cp .env.example .env
```

4. Composer Install

```bash
composer install
```

5. Install dependencies

```bash
./vendor/bin/sail up -d
./vendor/bin/sail composer install
./vendor/bin/sail migration
./vendor/bin/sail artisan db:seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

6. Call URL
```bash
http://laravel.test/
```
