<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\Entity\Project;
use App\Entity\ProjectInvitation;
use App\Entity\ProjectMember;
use App\State\Project\ProjectAcceptInvitationProcessor;
use App\State\Project\ProjectInviteProcessor;
use App\State\Project\ProjectLeaveProcessor;
use App\State\Project\ProjectMemberProcessor;
use App\State\Project\ProjectMemberProvider;
use App\State\Project\ProjectRemoveMemberProcessor;

#[ApiResource(
  shortName: 'Project',
  routePrefix: '/projects',
  security: 'is_granted(\'ROLE_USER\')',
  outputFormats: ['jsonld' => ['application/ld+json']],
  inputFormats: [
    'jsonld' => ['application/ld+json'],
    'json' => ['application/json'],
  ],
  normalizationContext: ['groups' => ['project:read', 'project_invitation:read']],
  denormalizationContext: ['groups' => ['project:write', 'project_invitation:write']],
  operations: [
    new GetCollection(
      uriTemplate: '/{id}/members',
      output: ProjectMember::class,
      provider: ProjectMemberProvider::class,
      openapi: new Operation(
        summary: 'List project members',
        description: 'Get the list of members for a specific project'
      )
    ),
    new Post(
      uriTemplate: '/{id}/invite',
      processor: ProjectInviteProcessor::class,
      openapi: new Operation(
        summary: 'Invite a user to a project',
        description: 'Send an invitation to another user to join the project'
      )
    ),
    new Post(
      uriTemplate: '/{id}/join',
      processor: ProjectAcceptInvitationProcessor::class,
      openapi: new Operation(
        summary: 'Join a project',
        description: 'Accept an invitation to join the project'
      )
    ),
    new Delete(
      uriTemplate: '/{id}/leave',
      processor: ProjectLeaveProcessor::class,
      openapi: new Operation(
        summary: 'Leave a project',
        description: 'Allow a user to leave a project'
      )
    ),
    new Delete(
      uriTemplate: '/{id}/remove/{userId}',
      processor: ProjectRemoveMemberProcessor::class,
      openapi: new Operation(
        summary: 'Remove a member from the project',
        description: 'Admin can remove a member from the project'
      )
    )
  ]
)]
final class ProjectResource {}