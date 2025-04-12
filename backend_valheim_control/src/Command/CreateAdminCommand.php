<?php

namespace App\Command;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Créer le premier administrateur du système.'
)]
final class CreateAdminCommand extends Command
{
    public function __construct(
        private DocumentManager $documentManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $questionEmail = new Question("Veuillez entrer l'email de l'administrateur : ");
        $email = $helper->ask($input, $output, $questionEmail);

        $password = 'admin123';

        $existingUser = $this->documentManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            $output->writeln('<error>Un administrateur existe déjà.</error>');
            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail($email)
            ->setRoles(['ROLE_ADMIN'])
            ->setCreatedAt(new \DateTimeImmutable())
            ->setMustChangePassword(true);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $this->documentManager->persist($user);
        $this->documentManager->flush();

        $output->writeln('<info>Administrateur créé avec succès.</info>');
        $output->writeln("Email : $email");
        $output->writeln("Mot de passe : $password");

        return Command::SUCCESS;
    }
}
