<h2>Project: Daa<br>Task: Dealer Management Functionality</h2>
<hr>
<h2>Commands to run:</h2>
<ul>
    <li>php artisan migrate --path=/database/migrations/2023_11_21_111634_create_dealers_table.php</li>
    <li>php artisan db:seed --class=DealersSeeder</li>
    <li>php artisan db:seed --class=SidebarMenu</li>
</ul>
<hr>
<h2>ChangeLog:</h2>
<p>8-Apr-2024: V2 (Latest)</p>
<ul>
    <li>added the functionality  of adding a new dealer, editing an existing dealer and deleting a dealer from the database</li>
</ul>
<p>from 1-Apr-2024 to 6-Apr-2024: V1</p>
<ul>
    <li>added dealer management tab on sidebar</li>
    <li>added approved, all, rejected sub-tabs for dealer management tab</li>
    <li>updated routes for all things related to dealers</li>
    <li>updated the registration form to have two options agency or agent</li>
    <li>added functionality for the agency registration form</li>
    <li>added proper validation for all file upload forms and inputs for the agency registration form</li>
    <li>added file upload functionality for the document forms</li>
    <li>added edit dealer page</li>
    <li>added view dealer page</li>
    <li>added delete functionality for dealer</li>
    <li>added create dealer page</li>
    <li>added list and grid view functionality when viewing all, approved and rejected dealers</li>
</ul>