<?php

namespace App\Enums;

/**
 * Class UserRoleEnum
 *
 * Enumeration class for user role values
 */
enum BookStatusEnum: String
{
    case AVAILABLE = '1';
    case NOT_AVAILABLE = '0';
}
