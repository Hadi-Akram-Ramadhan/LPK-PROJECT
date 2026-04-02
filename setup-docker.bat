@echo off
echo ===================================================
echo   SETUP LPK CBT - DOCKER ENVIRONMENT (WINDOWS)
echo ===================================================
echo.

IF NOT EXIST ".env" (
    echo [1/5] Menyalin konfigurasi .env...
    copy .env.docker.example .env >nul
) ELSE (
    echo [1/5] File .env sudah ada, di-skip.
)

echo [2/5] Menginstal dependensi Composer via Docker...
docker run --rm -v "%cd%":/var/www/html -w /var/www/html laravelsail/php82-composer:latest composer install --ignore-platform-reqs

echo [3/5] Menjalankan Container (Web, Database, Redis, Reverb)...
call vendor\bin\sail up -d

echo [4/5] Menyiapkan Database dan Key Laravel...
call vendor\bin\sail artisan key:generate
call vendor\bin\sail artisan migrate

echo [5/5] Menginstal dependensi Frontend (Node.js)...
call vendor\bin\sail npm install

echo.
echo ===================================================
echo   SETUP SELESAI!
echo ===================================================
echo.
echo Web bisa diakses di: http://localhost
echo.
echo Untuk mematikan server, jalankan perintah: 
echo vendor\bin\sail stop
echo.
echo Untuk menjalankan auto-reload tampilan (Vite):
echo vendor\bin\sail npm run dev
echo.
pause
