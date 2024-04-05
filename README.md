## for Prodcuts

php artisan migrate --path=/database/migrations/2024_03_27_095109_alter_product_service_table.php
php artisan db:seed --class=UpdateSidebarTableSeeder

## for User

php artisan migrate --path=/database/migrations/2024_03_28_072156_alter_users_table.php

## Purchase

php artisan migrate --path=/database/migrations/2024_04_01_111229_alter_purchase_table.php


## Dealer Manegment

php artisan migrate --path=/database/migrations/2023_11_21_111634_create_dealers_table.php
php artisan db:seed --class=SidebarMenu
