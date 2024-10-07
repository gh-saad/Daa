# Daa ERP

## Important Commands
- `php artisan installer:run`

## for Prodcuts

- `php artisan migrate --path=/database/migrations/2024_03_27_095109_alter_product_service_table.php`
- `php artisan db:seed --class=UpdateSidebarTableSeeder`

## for User

- `php artisan migrate --path=/database/migrations/2024_03_28_072156_alter_users_table.php`

## Purchase

- `php artisan migrate --path=/database/migrations/2024_04_01_111229_alter_purchase_table.php`

- `add the following in .env file:`
- `SELECTED_USER_ID=2`
- `DEALER_MANAGEMENT_ALLOWED_USERS=1,2`

## run after 17-April 2014

- `php artisan migrate:rollback --path=/database/migrations/2024_04_01_111229_alter_purchase_table.php`
- `php artisan migrate --path=/database/migrations/2024_04_01_111229_alter_purchase_table.php`

- `php artisan migrate --path=/database/migrations/2024_04_25_092732_alter_user_tabel_add_contract_status.php`

## Dealer Manegment
- `php artisan migrate:rollback --path=/database/migrations/2023_11_21_111634_create_dealers_table.php`
- `php artisan migrate --path=/database/migrations/2023_11_21_111634_create_dealers_table.php`
- `php artisan db:seed --class=SidebarMenu`
- `php artisan migrate --path=/database/migrations/2024_04_18_051815_alter_dealers_table.php`

## for Account
- `php artisan module:seed Pos`

## after 16-May-2024
remove all roles first
- `php artisan db:seed --class=RolesTableSeeder`
- `php artisan module:seed ProductService`

# after changes in the Hrm module for tax deduction and tax relief
- `php artisan module:migrate Hrm`

# after 15-July-2024
- `php artisan fetch:currencies`
- assign KES currency in the system settings for the application.
- `php artisan migrate --path=/database/migrations/2024_07_18_111042_alter_bank_accounts_table.php`
- manually assign KES or USD currency in the database, to avoid deleting and re-seeding the database bank_accounts table
- `php artisan migrate --path=/database/migrations/2024_07_22_055437_alter_currency_table.php`
- `php artisan migrate --path=/database/migrations/2024_07_22_095532_alter_bank_transfers_table.php`

```
def calculateIncomeTax(amount):
    incomeTax = 0
    if amount <= 24000:
        incomeTax = amount * 0.10
    else:
        incomeTax += 24000 * 0.10

        if amount <= 24000 + 8333:
            incomeTax += (amount - 24000) * 0.25
        else:
            incomeTax += 8333 * 0.25

            if amount <= 24000 + 8333 + 467667:
                incomeTax += (amount - 24000 - 8333) * 0.30
            else:
                incomeTax += 467667 * 0.30

                if amount <= 24000 + 8333 + 467667 + 300000:
                    incomeTax += (amount - 24000 - 8333 - 467667) * 0.325
                else:
                    incomeTax += 300000 * 0.325
                    incomeTax += (amount - 800000) * 0.35

    return incomeTax

def calculateNHIF(amount):
    if amount <= 5999:
        return 150
    elif amount <= 7999:
        return 300
    elif amount <= 11999:
        return 400
    elif amount <= 14999:
        return 500
    elif amount <= 19999:
        return 600
    elif amount <= 24999:
        return 750
    elif amount <= 29999:
        return 850
    elif amount <= 34999:
        return 900
    elif amount <= 39999:
        return 950
    elif amount <= 44999:
        return 1000
    elif amount <= 49999:
        return 1100
    elif amount <= 59999:
        return 1200
    elif amount <= 69999:
        return 1300
    elif amount <= 79999:
        return 1400
    elif amount <= 89999:
        return 1500
    elif amount <= 99999:
        return 1600
    else:
        return 1700

def calculateNSSF(income):
    TIER_1_RATE = 420
    TIER_2_RATE = 1740

    TIER_1_LIMIT = 7000
    TIER_2_LIMIT = 36000

    TIER_1_income = min(income, TIER_1_LIMIT)
    TIER_2_income = max(0, min(income - TIER_1_LIMIT, TIER_2_LIMIT))

    Tier_1_contribution = TIER_1_RATE
    Tier_2_contribution = TIER_2_RATE if income > TIER_1_LIMIT else 0

    total_employee_contribution = Tier_1_contribution + Tier_2_contribution
    total_employer_contribution = total_employee_contribution
    total_contribution = total_employee_contribution + total_employer_contribution

    return {
        "employee": total_employee_contribution,
        "employer": total_employer_contribution,
        "total": total_contribution
    }

def calculatePaye(incomeTax, taxRelief):
    return max(0, incomeTax - taxRelief)
    
def format_with_kes(amount):
    # Check if the amount is a float and format accordingly
    if isinstance(amount, float):
        formatted_amount = "{:,.2f} KES".format(amount)
    else:
        formatted_amount = "{:,} KES".format(amount)
    
    return formatted_amount
    
# Test cases
def run_tests():
    taxRelief = 2400

    for gross_salary in [1000, 24000, 24001 , 36000, 50000, 720000]:
        NSSF = calculateNSSF(gross_salary)
        taxAbleAmount = gross_salary - NSSF['employee']
        incomeTax = calculateIncomeTax(taxAbleAmount)
        PAYE = calculatePaye(incomeTax, taxRelief)
        net_salary = taxAbleAmount - PAYE
        NHIF = calculateNHIF(gross_salary)

        print(f"### Test with Basic Salary {format_with_kes(gross_salary)} ###")
        print("NSSF:", format_with_kes(NSSF['employee']))
        print("Taxable Pay:", format_with_kes(taxAbleAmount))
        print("Tax Relief:", format_with_kes(taxRelief))
        print("Income TAX:", format_with_kes(incomeTax))
        print("P.A.Y.E:", format_with_kes(PAYE))
        print("Pay After TAX:", format_with_kes(net_salary))
        print("NHIF:", format_with_kes(NHIF))
        print("")

run_tests()




# Write Python 3 code in this online editor and run it.
def calculateIncomeTax(amount): 
    incomeTax = 0
    if amount <= 24000:
        incomeTax = amount * 0.10
        return incomeTax
    else:
        incomeTax += 24000 * 0.10

    # Tax band on the next 8,333 at 25%
    if amount <= 24000 + 8333:
        incomeTax += (amount - 24000) * 0.25
        return incomeTax
    else:
        incomeTax += 8333 * 0.25

    # Tax band on the next 467,667 at 30%
    if amount <= 24000 + 8333 + 467667:
        incomeTax += (amount - 24000 - 8333) * 0.30
        return incomeTax
    else:
        incomeTax += 467667 * 0.30

    # Tax band on the next 300,000 at 32.5%
    if amount <= 24000 + 8333 + 467667 + 300000:
        incomeTax += (amount - 24000 - 8333 - 467667) * 0.325
        return incomeTax
    else:
        incomeTax += 300000 * 0.325

    # Tax on amounts over 800,000 at 35%
    incomeTax += (amount - 800000) * 0.35

    return incomeTax
    
def calculateNHIF(amount):
    if amount <= 5999:
        return 150
    elif amount <= 7999:
        return 300
    elif amount <= 11999:
        return 400
    elif amount <= 14999:
        return 500
    elif amount <= 19999:
        return 600
    elif amount <= 24999:
        return 750
    elif amount <= 29999:
        return 850
    elif amount <= 34999:
        return 900
    elif amount <= 39999:
        return 950
    elif amount <= 44999:
        return 1000
    elif amount <= 49999:
        return 1100
    elif amount <= 59999:
        return 1200
    elif amount <= 69999:
        return 1300
    elif amount <= 79999:
        return 1400
    elif amount <= 89999:
        return 1500
    elif amount <= 99999:
        return 1600
    else:  # For basic_salary 100,000 and above
        return 1700
        
def calculateNSSF(income):
    # Define NSSF rates and limits
    TIER_1_RATE = 420  # 6%
    TIER_2_RATE = 1740  # 6%
    
    TIER_1_LIMIT = 7000  # First KES 6,000
    TIER_2_LIMIT = 36000  # Next KES 12,000 (up to 18,000 total)

    TIER_1_income = TIER_1_LIMIT
    if income < TIER_2_LIMIT:
        TIER_2_income = income - TIER_1_LIMIT
    else:
        TIER_2_income = TIER_2_LIMIT
    Tier_1_contribution = TIER_1_RATE
    Tier_2_contribution = TIER_2_RATE

    total_employee_contribution = Tier_1_contribution + Tier_2_contribution
    total_employer_contribution = total_employee_contribution
    total_contribution = total_employee_contribution + total_employee_contribution
    return {
        "employee": total_employee_contribution,
        "employer": total_employer_contribution,
        "total": total_contribution
    }
def calculatePaye(incomeTax, taxRelief):
    if incomeTax > taxRelief:
        return incomeTax - taxRelief
    else:
        return 0
print("### test 1 ###")    
taxRelief = 2400
gross_salary = 1000  # Example gross salary
NSSF = calculateNSSF(gross_salary)
taxAbleAmount = gross_salary - NSSF['employee']
incomeTax = calculate_tax(taxAbleAmount)
PAYE = calculatePaye(incomeTax, taxRelief)
net_salary = taxAbleAmount - PAYE 
NHIF = calculate_nhif(gross_salary)
print("Basic Salary", gross_salary)
print("NSSF", NSSF['employee'])
print("Taxable Pay", taxAbleAmount)
print("Tax Relief", taxRelief)
print("Income TAX", incomeTax)
print("P.A.Y.E ", PAYE)
print("Pay After TAX ", net_salary)
print("NHIF ", NHIF)
print("")

print("### test 2 ###") 
# 50000
gross_salary = 50000  # Example gross salary
NSSF = calculateNSSF(gross_salary)
taxAbleAmount = gross_salary - NSSF['employee']
incomeTax = calculate_tax(taxAbleAmount)
PAYE = calculatePaye(incomeTax, taxRelief)
net_salary = taxAbleAmount - PAYE 
NHIF = calculate_nhif(gross_salary)
print("Basic Salary", gross_salary)
print("NSSF", NSSF['employee'])
print("Taxable Pay", taxAbleAmount)
print("Tax Relief", taxRelief)
print("Income TAX", incomeTax)
print("P.A.Y.E ", PAYE)
print("Pay After TAX ", net_salary)
print("NHIF ", NHIF)
print("")

print("### test 3 ###") 
# 100000
gross_salary = 100000  # Example gross salary
NSSF = calculateNSSF(gross_salary)
taxAbleAmount = gross_salary - NSSF['employee']
incomeTax = calculate_tax(taxAbleAmount)
PAYE = calculatePaye(incomeTax, taxRelief)
net_salary = taxAbleAmount - PAYE 
NHIF = calculate_nhif(gross_salary)
print("Basic Salary", gross_salary)
print("NSSF", NSSF['employee'])
print("Taxable Pay", taxAbleAmount)
print("Tax Relief", taxRelief)
print("Income TAX", incomeTax)
print("P.A.Y.E ", PAYE)
print("Pay After TAX ", net_salary)
print("NHIF ", NHIF)
print("")
```