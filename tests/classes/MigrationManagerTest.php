<?php

declare(strict_types=1);

namespace tests\classes;

use PHPUnit\Framework\TestCase;
use App\classes\MigrationManager;
use ReflectionClass;
use PDO;

class MigrationManagerTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
    }

    public function testGetMigrations(): void
    {
        $migrationFiles = [
            '/app/migration/CreateUsersMigration.php',
            '/app/migration/CreateProductsMigration.php',
        ];

        $manager = $this->getMockBuilder(MigrationManager::class)
            ->setConstructorArgs([$this->pdo, $migrationFiles])
            ->onlyMethods(['getMigrationClassFromFile'])
            ->getMock();

        $manager->expects($this->exactly(count($migrationFiles)))
            ->method('getMigrationClassFromFile')
            ->willReturnOnConsecutiveCalls(
                'App\migrations\CreateUsersMigration',
                'App\migrations\CreateProductsMigration'
            );

        $reflection = new ReflectionClass(MigrationManager::class);
        $method = $reflection->getMethod('getMigrations');
        $method->setAccessible(true);

        $migrations = $method->invoke($manager);

        $this->assertEquals(
            ['App\migrations\CreateUsersMigration', 'App\migrations\CreateProductsMigration'],
            $migrations
        );
    }

    public function testGetPendingMigrations(): void
    {
        $migrationFiles = [
            '/app/migration/CreateUsersMigration.php',
            '/app/migration/CreateProductsMigration.php',
        ];

        $manager = $this->getMockBuilder(MigrationManager::class)
            ->setConstructorArgs([$this->pdo, $migrationFiles])
            ->onlyMethods(['getMigrationClassFromFile', 'getExecutedMigrations'])
            ->getMock();

        $manager->expects($this->exactly(count($migrationFiles)))
            ->method('getMigrationClassFromFile')
            ->willReturnOnConsecutiveCalls(
                'App\migrations\CreateUsersMigration',
                'App\migrations\CreateProductsMigration',
            );

        $manager->expects($this->once())
            ->method('getExecutedMigrations')
            ->willReturn(['App\migrations\CreateUsersMigration']);

        $reflection = new ReflectionClass(MigrationManager::class);
        $method = $reflection->getMethod('getPendingMigrations');
        $method->setAccessible(true);

        $pendingMigrations = $method->invoke($manager);

        $this->assertEquals(
            array_values(['App\migrations\CreateProductsMigration']),
            array_values($pendingMigrations)
        );
    }
}
