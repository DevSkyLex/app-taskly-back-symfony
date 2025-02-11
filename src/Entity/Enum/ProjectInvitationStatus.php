<?php

namespace App\Entity\Enum;

enum ProjectInvitationStatus: string
{
  case PENDING = 'PENDING';
  case ACCEPTED = 'ACCEPTED';
  case REFUSED = 'REFUSED';
}