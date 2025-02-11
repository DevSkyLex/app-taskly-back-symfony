<?php

namespace App\State\Project;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Project;
use App\Entity\ProjectInvitation;
use App\Entity\User;
use App\Repository\ProjectInvitationRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use LogicException;
use Symfony\Bundle\SecurityBundle\Security;

final class ProjectInviteProcessor implements ProcessorInterface
{
  public function __construct(
    private readonly ProjectRepository $projectRepository,
    private readonly UserRepository $userRepository,
    private readonly ProjectInvitationRepository $projectInvitationRepository,
    private readonly Security $security
  ) {}

  public function process(
    mixed $data,
    Operation $operation,
    array $uriVariables = [],
    array $context = []
  ): ProjectInvitation {
    $user = $this->security->getUser();

    if (!$user instanceof User) {
      throw new LogicException(message: 'The user must be authenticated');
    }

    $project = $this->projectRepository->find(id: $uriVariables['id']);

    if (!$project instanceof Project) {
      throw new LogicException(message: 'The project does not exist');
    }

    $invitedUser = $this->userRepository->find(id: $data['userId']);

    if (!$invitedUser instanceof User) {
      throw new LogicException(message: 'The invited user does not exist');
    }

    $existingInvitation = $this->projectInvitationRepository->findOneBy(criteria: [
      'project' => $project,
      'invited' => $invitedUser
    ]);

    if ($existingInvitation instanceof ProjectInvitation) {
      throw new LogicException(message: 'The invitation already exists');
    }

    $invitation = new ProjectInvitation();
    $invitation->setProject(project: $project)
      ->setSender(sender: $user)
      ->setInvited(invited: $invitedUser)
      ->setExpiresAt(expiresAt: new DateTimeImmutable(datetime: '+7 days'));
    
    $this->projectInvitationRepository->save(projectInvitation: $invitation);

    return $invitation;
  }
}