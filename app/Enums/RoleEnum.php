<?php

namespace App\Enums;

enum RoleEnum: string
{
    // Partner Management System
    case PARTNER_MANAGEMENT_SYSTEM_VP = 'partner_management_system_vp';
    case PARTNER_MANAGEMENT_SYSTEM_MANAGER = 'partner_management_system_manager';
    case PARTNER_MANAGEMENT_SYSTEM_DIRECTOR = 'partner_management_system_director';
    case PARTNER_MANAGEMENT_SYSTEM_EXECUTIVE = 'partner_management_system_executive';

    // Customer Management System
    case CUSTOMER_MANAGEMENT_SYSTEM_VP = 'customer_management_system_vp';
    case CUSTOMER_MANAGEMENT_SYSTEM_MANAGER = 'customer_management_system_manager';
    case CUSTOMER_MANAGEMENT_SYSTEM_DIRECTOR = 'customer_management_system_director';
    case CUSTOMER_MANAGEMENT_SYSTEM_EXECUTIVE = 'customer_management_system_executive';
    // Customer Succerss Management System
    case CUSTOMER_SUCCESS_MANAGEMENT_MANAGER = 'customer_success_management_manager';
    case CUSTOMER_SUCCESS_MANAGEMENT_EXECUTIVE = 'customer_success_management_executive';
    case CUSTOMER_SUCCESS_MANAGEMENT_DIRECTOR = 'customer_success_management_director';

    // Website Management System
    case WEBSITE_MANAGEMENT_SYSTEM_VP = 'website_management_system_vp';
    case WEBSITE_MANAGEMENT_SYSTEM_MANAGER = 'website_management_system_manager';
    case WEBSITE_MANAGEMENT_SYSTEM_DIRECTOR = 'website_management_system_director';
    case WEBSITE_MANAGEMENT_SYSTEM_EXECUTIVE = 'website_management_system_executive';

    // HR Management System
    case HR_MANAGEMENT_SYSTEM_VP = 'hr_management_system_vp';
    case HR_MANAGEMENT_SYSTEM_MANAGER = 'hr_management_system_manager';
    case HR_MANAGEMENT_SYSTEM_DIRECTOR = 'hr_management_system_director';
    case HR_MANAGEMENT_SYSTEM_EXECUTIVE = 'hr_management_system_executive';

    // Accounts Management System
    case ACCOUNTS_MANAGEMENT_SYSTEM_VP = 'accounts_management_system_vp';
    case ACCOUNTS_MANAGEMENT_SYSTEM_MANAGER = 'accounts_management_system_manager';
    case ACCOUNTS_MANAGEMENT_SYSTEM_DIRECTOR = 'accounts_management_system_director';
    case ACCOUNTS_MANAGEMENT_SYSTEM_EXECUTIVE = 'accounts_management_system_executive';

    // Operation Management System
    case OPERATION_MANAGEMENT_SYSTEM_VP = 'operation_management_system_vp';
    case OPERATION_MANAGEMENT_SYSTEM_MANAGER = 'operation_management_system_manager';
    case OPERATION_MANAGEMENT_SYSTEM_DIRECTOR = 'operation_management_system_director';
    case OPERATION_MANAGEMENT_SYSTEM_EXECUTIVE = 'operation_management_system_executive';

    // Supply Management System
    case SUPPLY_MANAGEMENT_SYSTEM_VP = 'supply_management_system_vp';
    case SUPPLY_MANAGEMENT_SYSTEM_MANAGER = 'supply_management_system_manager';
    case SUPPLY_MANAGEMENT_SYSTEM_DIRECTOR = 'supply_management_system_director';
    case SUPPLY_MANAGEMENT_SYSTEM_EXECUTIVE = 'supply_management_system_executive';

    // Media & Communication Management System
    case MEDIA_MANAGEMENT_SYSTEM_VP = 'media_management_system_vp';
    case MEDIA_MANAGEMENT_SYSTEM_MANAGER = 'media_management_system_manager';
    case MEDIA_MANAGEMENT_SYSTEM_DIRECTOR = 'media_management_system_director';
    case MEDIA_MANAGEMENT_SYSTEM_EXECUTIVE = 'media_management_system_executive';

    // CRM Management System
    case CRM_MANAGEMENT_SYSTEM_VP = 'crm_management_system_vp';
    case CRM_MANAGEMENT_SYSTEM_MANAGER = 'crm_management_system_manager';
    case CRM_MANAGEMENT_SYSTEM_DIRECTOR = 'crm_management_system_director';
    case CRM_MANAGEMENT_SYSTEM_EXECUTIVE = 'crm_management_system_executive';

    // Sales Management System
    case SALES_MANAGEMENT_SYSTEM_VP = 'sales-vp';
    case SALES_MANAGEMENT_SYSTEM_MANAGER = 'sales-manager';
    case SALES_MANAGEMENT_SYSTEM_DIRECTOR = 'sales-director';
    case SALES_MANAGEMENT_SYSTEM_EXECUTIVE = 'sales-executive';

    // Training Management System
    case TRAINING_MANAGEMENT_SYSTEM_VP = 'training_management_system_vp';
    case TRAINING_MANAGEMENT_SYSTEM_MANAGER = 'training_management_system_manager';
    case TRAINING_MANAGEMENT_SYSTEM_DIRECTOR = 'training_management_system_director';
    case TRAINING_MANAGEMENT_SYSTEM_EXECUTIVE = 'training_management_system_executive';

    // Analyses Management System
    case ANALYSES_MANAGEMENT_SYSTEM_VP = 'analyses_management_system_vp';
    case ANALYSES_MANAGEMENT_SYSTEM_MANAGER = 'analyses_management_system_manager';
    case ANALYSES_MANAGEMENT_SYSTEM_DIRECTOR = 'analyses_management_system_director';
    case ANALYSES_MANAGEMENT_SYSTEM_EXECUTIVE = 'analyses_management_system_executive';

    // Software Management System
    case SOFTWARE_MANAGEMENT_SYSTEM_VP = 'software_management_system_vp';
    case SOFTWARE_MANAGEMENT_SYSTEM_MANAGER = 'software_management_system_manager';
    case SOFTWARE_MANAGEMENT_SYSTEM_DIRECTOR = 'software_management_system_director';
    case SOFTWARE_MANAGEMENT_SYSTEM_EXECUTIVE = 'software_management_system_executive';

    // User Management System
    case USER_MANAGEMENT_SYSTEM_VP = 'user_management_system_vp';
    case USER_MANAGEMENT_SYSTEM_MANAGER = 'user_management_system_manager';
    case USER_MANAGEMENT_SYSTEM_DIRECTOR = 'user_management_system_director';
    case USER_MANAGEMENT_SYSTEM_EXECUTIVE = 'user_management_system_executive';
}
