<?php

// Інтерфейс Відвідувача (Visitor)
interface OrganizationVisitor
{
    public function visitCompany(Company $company): string;
    public function visitDepartment(Department $department): string;
    public function visitEmployee(Employee $employee): string;
}

// Інтерфейс сутності (Element)
interface OrganizationUnit
{
    public function accept(OrganizationVisitor $visitor): string;
}

// Конкретні сутності: Співробітник, Департамент, Компанія
class Employee implements OrganizationUnit
{
    // Приймає назву позиції та розмір зарплати
    public function __construct(
        private string $roleTitle,
        private float $wage
    ) {}

    public function getRoleTitle(): string
    {
        return $this->roleTitle;
    }
    public function getWage(): float
    {
        return $this->wage;
    }

    public function accept(OrganizationVisitor $visitor): string
    {
        return $visitor->visitEmployee($this);
    }
}

class Department implements OrganizationUnit
{
    /** @param Employee[] $staff */
    public function __construct(
        private string $departmentTitle,
        private array $staff
    ) {}

    public function getDepartmentTitle(): string
    {
        return $this->departmentTitle;
    }

    /** @return Employee[] */
    public function getStaff(): array
    {
        return $this->staff;
    }

    public function accept(OrganizationVisitor $visitor): string
    {
        return $visitor->visitDepartment($this);
    }
}

class Company implements OrganizationUnit
{
    /** @param Department[] $units */
    public function __construct(
        private string $companyName,
        private array $units
    ) {}

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    /** @return Department[] */
    public function getUnits(): array
    {
        return $this->units;
    }

    public function accept(OrganizationVisitor $visitor): string
    {
        return $visitor->visitCompany($this);
    }
}

// Конкретний Відвідувач: Зарплатна відомість
class PayrollReportVisitor implements OrganizationVisitor
{
    public function visitCompany(Company $company): string
    {
        // Схематичний збір даних по всіх департаментах компанії
        $output = "ЗВІТ ПО КОМПАНІЇ: {$company->getCompanyName()}\n";
        foreach ($company->getUnits() as $department) {
            $output .= $department->accept($this);
        }
        return $output;
    }

    public function visitDepartment(Department $department): string
    {
        // Схематичний збір даних по співробітниках відділу
        $output = "  Департамент: {$department->getDepartmentTitle()}\n";
        foreach ($department->getStaff() as $employee) {
            $output .= $employee->accept($this);
        }
        return $output;
    }

    public function visitEmployee(Employee $employee): string
    {
        // Схематичне форматування рядка зарплати
        return "    * Позиція: {$employee->getRoleTitle()} | Зарплата: {$employee->getWage()} грн\n";
    }
}

// Клієнтський код
$backendDev = new Employee("PHP Backend Developer", 85000.00);
$frontendDev = new Employee("Vue.js Frontend Developer", 75000.00);
$marketer = new Employee("SMM Specialist", 32000.00);

$devDepartment = new Department("Розробка", [$backendDev, $frontendDev]);
$marketingDepartment = new Department("Маркетинг", [$marketer]);

$enterprise = new Company("SoftGroup", [$devDepartment, $marketingDepartment]);

$reportGenerator = new PayrollReportVisitor();

// 1. Отримання репорту для всієї компанії
echo "--- Зарплатна відомість компанії ---\n";
echo $enterprise->accept($reportGenerator) . "\n";

// 2. Отримання репорту окремо для конкретного департаменту
echo "--- Зарплатна відомість окремого департаменту ---\n";
echo $marketingDepartment->accept($reportGenerator);
