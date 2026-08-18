<?php

use PHPUnit\Framework\TestCase;

final class SchemaHelperTest extends TestCase
{
    public function testReturnsTrueWhenColumnExists(): void
    {
        $pdo = $this->pdoReturning(1);

        $this->assertTrue(tableHasColumn($pdo, 'categories', 'user_id'));
    }

    public function testReturnsFalseWhenColumnIsMissing(): void
    {
        $pdo = $this->pdoReturning(false);

        $this->assertFalse(tableHasColumn($pdo, 'payment_methods', 'user_id'));
    }

    public function testQueriesInformationSchemaWithTableAndColumn(): void
    {
        $statement = $this->createMock(PDOStatement::class);
        $statement->expects($this->once())
            ->method('execute')
            ->with([':table_name' => 'expenses', ':column_name' => 'status'])
            ->willReturn(true);
        $statement->method('fetchColumn')->willReturn(1);

        $pdo = $this->createMock(PDO::class);
        $pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('INFORMATION_SCHEMA.COLUMNS'))
            ->willReturn($statement);

        $this->assertTrue(tableHasColumn($pdo, 'expenses', 'status'));
    }

    public function testCachesLookupPerTableAndColumn(): void
    {
        $pdo = $this->pdoReturning(1, $this->once());

        $this->assertTrue(tableHasColumn($pdo, 'budgets', 'month_year'));
        $this->assertTrue(tableHasColumn($pdo, 'budgets', 'month_year'));
    }

    public function testDoesNotShareCacheBetweenDifferentColumns(): void
    {
        $existing = $this->pdoReturning(1);
        $missing = $this->pdoReturning(false);

        $this->assertTrue(tableHasColumn($existing, 'savings', 'target_date'));
        $this->assertFalse(tableHasColumn($missing, 'savings', 'archived_at'));
    }

    private function pdoReturning(mixed $fetchColumn, mixed $prepareExpectation = null): PDO
    {
        $statement = $this->createMock(PDOStatement::class);
        $statement->method('execute')->willReturn(true);
        $statement->method('fetchColumn')->willReturn($fetchColumn);

        $pdo = $this->createMock(PDO::class);
        $pdo->expects($prepareExpectation ?? $this->any())
            ->method('prepare')
            ->willReturn($statement);

        return $pdo;
    }
}
