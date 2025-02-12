<?php

namespace App\Entity\Enum;

enum TaskStatus: string
{
  case TODO = 'TODO';
  case DOING = 'DOING';
  case DONE = 'DONE';
}