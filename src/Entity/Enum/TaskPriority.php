<?php

namespace App\Entity\Enum;

enum TaskPriority: string
{
  case LOW = 'LOW';
  case MEDIUM = 'MEDIUM';
  case HIGH = 'HIGH';
  case CRITICAL = 'CRITICAL';
}