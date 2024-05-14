## for Prodcuts

- 'php artisan migrate --path=/database/migrations/2024_03_27_095109_alter_product_service_table.php'
- 'php artisan db:seed --class=UpdateSidebarTableSeeder'

## for User

- 'php artisan migrate --path=/database/migrations/2024_03_28_072156_alter_users_table.php'

## Purchase

- 'php artisan migrate --path=/database/migrations/2024_04_01_111229_alter_purchase_table.php'

- 'add the following in .env file:'
- 'SELECTED_USER_ID=2'
- 'DEALER_MANAGEMENT_ALLOWED_USERS=1,2'

## run after 17-April 2014

- 'php artisan migrate:rollback --path=/database/migrations/2024_04_01_111229_alter_purchase_table.php'
- 'php artisan migrate: --path=/database/migrations/2024_04_01_111229_alter_purchase_table.php'

- 'php artisan migrate --path=/database/migrations/2024_04_25_092732_alter_user_tabel_add_contract_status.php'

## Dealer Manegment
- 'php artisan migrate:rollback --path=/database/migrations/2023_11_21_111634_create_dealers_table.php'
- 'php artisan migrate --path=/database/migrations/2023_11_21_111634_create_dealers_table.php'
- 'php artisan db:seed --class=SidebarMenu'
- 'php artisan migrate --path=/database/migrations/2024_04_18_051815_alter_dealers_table.php'