<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\DTO\Account\AccountUpdateAvatarInput;
use App\DTO\Account\AccountUpdateInput;
use App\Entity\User;
use App\State\Account\AccountProvider;
use App\State\Account\AccountUpdateAvatarProcessor;
use App\State\Account\AccountUpdateProcessor;

#[ApiResource(
  shortName: 'Account',
  routePrefix: '/account',
  security: 'is_granted(\'ROLE_USER\')',
  outputFormats: ['jsonld' => ['application/ld+json']],
  normalizationContext: ['groups' => ['user:read']],
  denormalizationContext: ['groups' => ['user:write']],
  operations: [
    new Get(
      uriTemplate: '/me',
      input: false,
      output: User::class,
      provider: AccountProvider::class,
      inputFormats: ['jsonld' => ['application/ld+json']],
      outputFormats: ['jsonld' => ['application/ld+json']],
      openapi: new Operation(
        summary: 'Get the current user',
        description: 'Get the current user',
      )
    ),
    new Patch(
      uriTemplate: '/me',
      input: AccountUpdateInput::class,
      output: User::class,
      processor: AccountUpdateProcessor::class,
      provider: AccountProvider::class,
      inputFormats: ['jsonld' => ['application/ld+json']],
      outputFormats: ['jsonld' => ['application/ld+json']],
      openapi: new Operation(
        summary: 'Update the current user',
        description: 'Update the current user',
      )
    ),
    new Post(
      uriTemplate: '/me/avatar',
      input: AccountUpdateAvatarInput::class,
      output: User::class,
      inputFormats: ['multipart' => ['multipart/form-data']],
      outputFormats: ['jsonld' => ['application/ld+json']],
      processor: AccountUpdateAvatarProcessor::class,
      provider: AccountProvider::class,
      openapi: new Operation(
        summary: 'Update the current user avatar',
        description: 'Update the current user avatar',
      )
    ),
  ]
)]
final class AccountResource
{
}