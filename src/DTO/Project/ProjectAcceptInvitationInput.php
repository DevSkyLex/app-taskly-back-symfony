<?php

namespace App\DTO\Project;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;

final class ProjectAcceptInvitationInput
{
  //#region Constructeur
  public function __construct(
    #[ApiProperty(
      description: 'The invitation identifier',
      example: '00000000-0000-0000-0000-000000000000',
      required: true,
    )]
    #[Assert\NotBlank(message: 'The invitationId is required')]
    #[Assert\Uuid(versions: [4], strict: true, message: 'The invitationId must be a valid UUIDv4')]
    public string $invitationId,
  ) {}
  //#endregion
}