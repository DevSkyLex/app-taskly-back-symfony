<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\DTO\UserLoginInput;
use App\DTO\UserLoginOutput;
use App\DTO\UserRegistrationInput;
use App\DTO\UserRegistrationOutput;
use App\State\UserLoginProcessor;
use App\State\UserRefreshProcessor;
use App\State\UserRegistrationProcessor;


#[ApiResource(
  shortName: 'Authentication',
  routePrefix: '/auth',
  operations: [
    new Post(
      uriTemplate: '/register',
      input: UserRegistrationInput::class,
      output: UserRegistrationOutput::class,
      processor: UserRegistrationProcessor::class,
      openapi: new Operation(
        summary: 'Register a new user',
        description: 'Register a new user with email and password',
        security: ['no_auth' => []]
      )
    ),
    new Post(
      uriTemplate: '/login',
      input: UserLoginInput::class,
      output: UserLoginOutput::class,
      processor: UserLoginProcessor::class,
      openapi: new Operation(
        summary: 'Login a user',
        description: 'Login a user with email and password',
        security: ['no_auth' => []]
      )
    ),
    new Post(
      uriTemplate: '/refresh',
      input: false,
      output: UserLoginOutput::class,
      processor: UserRefreshProcessor::class,
      openapi: new Operation(
        summary: 'Refresh the JWT token',
        description: 'Refresh the JWT token with a refresh token',
        security: ['no_auth' => []]
      )
    )
  ]
)]
final class AuthenticationResource {}