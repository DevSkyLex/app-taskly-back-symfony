<?php

namespace App\DTO\Account;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * Classe AccountUpdateAvatarInput
 * @final
 * 
 * Cette classe permet de représenter les données
 * envoyées lors de la mise à jour de l'avatar 
 * d'un compte
 * 
 * @version 1.0.0
 * 
 * @author Valentin FORTIN <contact@valentin-fortin.pro>
 */
final class AccountUpdateAvatarInput
{
  //#region Constructeur
  /**
   * Constructeur
   * 
   * Initialise les données de mise à jour 
   * de l'avatar
   * 
   * @access public
   * @since 1.0.0
   * 
   * @param UploadedFile|null $avatarFile Fichier de l'avatar
   */
  public function __construct(
    /**
     * Propriété avatarFile
     * 
     * Fichier de l'avatar
     * 
     * @access public
     * @since 1.0.0
     * 
     * @var UploadedFile $avatarFile Fichier de l'avatar
     */
    #[Assert\NotNull(message: 'The avatar file is required.')]
    #[Assert\File(
      mimeTypes: ['image/jpeg', 'image/png'],
      mimeTypesMessage: 'The file must be a valid image (JPEG or PNG).',
      maxSize: '2M',
      maxSizeMessage: 'The file is too large ({{ size }} {{ suffix }}). Allowed maximum size is {{ limit }} {{ suffix }}.'
    )]
    #[ApiProperty(
      description: 'The avatar file',
      required: true,
      openapiContext: [
        'type' => 'string',
        'format' => 'binary',
        'example' => 'avatar.jpg'
      ]
    )]
    #[Groups(groups: ['user:write'])]
    public ?UploadedFile $avatarFile = null
  ) {}
  //#endregion
}