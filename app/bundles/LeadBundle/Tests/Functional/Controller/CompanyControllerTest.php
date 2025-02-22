<?php

namespace Mautic\LeadBundle\Tests\Functional\Controller;

use Mautic\CoreBundle\Test\MauticMysqlTestCase;
use Mautic\UserBundle\Entity\Role;
use Mautic\UserBundle\Entity\User;

class CompanyControllerTest extends MauticMysqlTestCase
{
    public const USERNAME = 'jhony';

    public function testMergeAction(): void
    {
        $this->client->request('GET', '/s/companies/merge/1');
        $clientResponse         = $this->client->getResponse();
        $this->assertEquals(200, $clientResponse->getStatusCode());
    }

    public function testMergeActionWithoutPermission(): void
    {
        $this->createAndLoginUser();
        $this->client->request('GET', '/s/companies/merge/1');
        $clientResponse         = $this->client->getResponse();
        $this->assertEquals(403, $clientResponse->getStatusCode());
    }

    private function createAndLoginUser(): User
    {
        // Create non-admin role
        $role = $this->createRole();
        // Create non-admin user
        $user = $this->createUser($role);

        $this->em->flush();
        $this->em->detach($role);

        $this->loginUser(self::USERNAME);
        $this->client->setServerParameter('PHP_AUTH_USER', self::USERNAME);
        $this->client->setServerParameter('PHP_AUTH_PW', 'mautic');

        return $user;
    }

    private function createRole(bool $isAdmin = false): Role
    {
        $role = new Role();
        $role->setName('Role');
        $role->setIsAdmin($isAdmin);

        $this->em->persist($role);

        return $role;
    }

    private function createUser(Role $role): User
    {
        $user = new User();
        $user->setFirstName('Jhony');
        $user->setLastName('Doe');
        $user->setUsername(self::USERNAME);
        $user->setEmail('john.doe@email.com');
        $encoder = self::getContainer()->get('security.password_hasher_factory')->getPasswordHasher($user);
        $user->setPassword($encoder->hash('mautic'));
        $user->setRole($role);

        $this->em->persist($user);

        return $user;
    }
}
