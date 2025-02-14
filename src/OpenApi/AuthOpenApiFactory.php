<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\OpenApi;
use ArrayObject;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator(decorates: 'api_platform.openapi.factory')]
final class AuthOpenApiFactory implements OpenApiFactoryInterface
{
  public function __construct(
    private readonly OpenApiFactoryInterface $decorated)
  {}

  public function __invoke(array $context = []): OpenApi
  {
    $openApi = $this->decorated->__invoke(context: $context);

    // 🔹 Définition des opérations
    $login = new Operation(
      operationId: 'postAuthLogin',
      tags: ['Authentification'],
      summary: 'Connexion utilisateur',
      description: 'Permet à un utilisateur de se connecter et d’obtenir un JWT.',
      requestBody: new RequestBody(
        content: new ArrayObject([
          'application/json' => [
            'schema' => [
              'type' => 'object',
              'properties' => [
                'username' => ['type' => 'string'],
                'password' => ['type' => 'string'],
              ],
            ],
          ],
        ]),
        required: true,
      ),
      responses: [
        '200' => [
          'description' => 'JWT renvoyé dans un cookie sécurisé',
          'headers' => [
            'Set-Cookie' => [
              'schema' => [
                'type' => 'string',
                'example' => 'BEARER=jwt_token; HttpOnly; Secure',
              ],
            ],
          ],
        ],
        '401' => [
          'description' => 'Identifiants invalides',
        ],
      ]
    );

    $register = new Operation(
      operationId: 'postAuthRegister',
      tags: ['Authentification'],
      summary: 'Inscription utilisateur',
      description: 'Permet à un utilisateur de s’inscrire et de créer un compte.',
      requestBody: new RequestBody(
        content: new ArrayObject([
          'application/json' => [
            'schema' => [
              'type' => 'object',
              'properties' => [
                'email' => ['type' => 'string'],
                'password' => ['type' => 'string'],
                'firstName' => ['type' => 'string'],
                'lastName' => ['type' => 'string']
              ],
            ],
          ],
        ]),
        required: true,
      ),
      responses: [
        '201' => [
          'description' => 'Utilisateur créé avec succès',
        ],
        '400' => [
          'description' => 'Erreur de validation des données',
        ],
      ]
    );

    $refresh = new Operation(
      operationId: 'postAuthRefresh',
      tags: ['Authentification'],
      summary: 'Rafraîchir le token JWT',
      description: 'Renvoie un nouveau token JWT si le refresh token est valide.',
      responses: [
        '200' => [
          'description' => 'Nouveau JWT renvoyé dans un cookie sécurisé',
          'headers' => [
            'Set-Cookie' => [
              'schema' => [
                'type' => 'string',
                'example' => 'BEARER=new_jwt_token; HttpOnly; Secure',
              ],
            ],
          ],
        ],
        '401' => [
          'description' => 'Refresh token invalide ou expiré',
        ],
      ]
    );

    // 🔹 Ajout des opérations aux routes
    $openApi->getPaths()->addPath('/api/auth/login', new PathItem(post: $login));
    $openApi->getPaths()->addPath('/api/auth/register', new PathItem(post: $register));
    $openApi->getPaths()->addPath('/api/auth/refresh', new PathItem(post: $refresh));

    return $openApi;
  }
}
