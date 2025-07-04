<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Console\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Owl\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use SyliusLabs\Polyfill\Symfony\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class AdminUserFakerCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'owl:admin-user-faker';

    public function __construct(
        private EntityManagerInterface $adminUserManager,
        private RepositoryInterface $roleRepository,
        private ExampleFactoryInterface $userFactory,
        private UserPasswordHasherInterface $hasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(
                'Create fake admin users with random data.',
            )
            ->setDefinition(
                new InputDefinition([
                    new InputOption('count', null, InputOption::VALUE_REQUIRED),
                ]),
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $roles = $this->roleRepository->findAll();
        $count = (int) $input->getOption('count');
        $faker = Factory::create();
        $batchSize = 500;

        /** @var Connection $connection */
        $connection = $this->adminUserManager->getConnection();
        $connection->getConfiguration()->setMiddlewares([]);
        $connection->beginTransaction();

        for ($i = 1; $i <= $count; ++$i) {
            $firstName = $faker->firstName();
            $lastName = $faker->lastName();
            shuffle($roles);
            /** @var ResourceInterface $role */
            $role = $roles[0];

            $connection->insert('owl_admin_user', [
                'display_name' => $firstName . ' ' . $lastName,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $faker->phoneNumber(),
                'email' => $faker->email(),
                'enabled' => rand(0, 1),
                'locked' => 0,
                'password_hash' => '',
                'hasher_name' => 'default',
                'note' => $faker->sentence(),
                'role_id' => $role->getId(),
                'roles' => serialize([$role->getId()]),
                'created_at' => date('Y-m-d H:i:s'),
                'locale_code' => 'en',
            ]);

            if (($i % $batchSize) === 0) {
                $connection->commit();
                $connection->beginTransaction();
            }
        }

        $connection->commit();

        return 0;
    }
}