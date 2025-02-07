<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\State\Auth\AuthLoginProcessor;
use App\State\Auth\AuthRefreshProcessor;
use App\State\Auth\AuthRegistrationProcessor;
use App\DTO\Auth\AuthLoginInput;
use App\DTO\Auth\AuthLoginOutput;
use App\DTO\Auth\AuthRegistrationInput;
use App\DTO\Auth\AuthRegistrationOutput;


#[ApiResource(
  shortName: 'Authentication',
  routePrefix: '/auth',
  operations: [
    new Post(
      uriTemplate: '/register',
      input: AuthRegistrationInput::class,
      output: AuthRegistrationOutput::class,
      processor: AuthRegistrationProcessor::class,
      openapi: new Operation(
        summary: 'Register a new user',
        description: 'Register a new user with email and password',
        security: ['no_auth' => []]
      )
    ),
    new Post(
      uriTemplate: '/login',
      input: AuthLoginInput::class,
      output: AuthLoginOutput::class,
      processor: AuthLoginProcessor::class,
      openapi: new Operation(
        summary: 'Login a user',
        description: 'Login a user with email and password',
        security: ['no_auth' => []]
      )
    ),
    new Post(
      uriTemplate: '/refresh',
      input: false,
      output: AuthLoginOutput::class,
      processor: AuthRefreshProcessor::class,
      openapi: new Operation(
        summary: 'Refresh the JWT token',
        description: 'Refresh the JWT token with a refresh token',
        security: ['no_auth' => []]
      )
    )
  ]
)]
final class AuthenticationResource {}