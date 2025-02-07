<?php

namespace App\Entity\Enum;

enum ProjectRole: string
{
  case MANAGER = 'MANAGER';
  
  case CONTRIBUTOR = 'CONTRIBUTOR';

  case VIEWER = 'VIEWER';
}