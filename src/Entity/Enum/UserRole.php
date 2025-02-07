<?php

namespace App\Entity\Enum;

enum UserRole: string 
{
  case ADMIN = 'ROLE_ADMIN';

  case USER = 'ROLE_USER';
}