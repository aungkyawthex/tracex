<?php

use PHPUnit\Framework\TestCase;

final class SavingsHelperTest extends TestCase
{
    public function testReturnsNullWhenSavingIsNotFoundForUser(): void
    {
        $pdo = $this->pdoReturning(false);

        $this->assertNull(getSavingCurrentAmount($pdo, 7, 42));
    }

    /**
     * @dataProvider amountProvider
     */
    public function testCastsAggregatedAmountToFloat(mixed $fetched, float $expected): void
    {
        $pdo = $this->pdoReturning($fetched);

        $this->assertSame($expected, getSavingCurrentAmount($pdo, 7, 42));
    }

    public static function amountProvider(): array
    {
        return [
            'no transactions' => ['0', 0.0],
            'integer string' => ['150000', 150000.0],
            'decimal string' => ['1250.75', 1250.75],
            'negative balance' => ['-500.50', -500.50],
            'numeric type' => [2500, 2500.0],
        ];
    }

    public function testScopesQueryToSavingAndUser(): void
    {
        $statement = $this->createMock(PDOStatement::class);
        $statement->expects($this->once())
            ->method('execute')
            ->with([':saving_id' => 7, ':user_id' => 42])
            ->willReturn(true);
        $statement->method('fetchColumn')->willReturn('0');

        $pdo = $this->createMock(PDO::class);
        $pdo->expects($this->once())
            ->method('prepare')
            ->with($this->logicalAnd(
                $this->stringContains('FROM savings s'),
                $this->stringContains('s.user_id = :user_id')
            ))
            ->willReturn($statement);

        $this->assertSame(0.0, getSavingCurrentAmount($pdo, 7, 42));
    }

    public function testDoesNotCacheResultsBetweenCalls(): void
    {
        $statement = $this->createMock(PDOStatement::class);
        $statement->method('execute')->willReturn(true);
        $statement->method('fetchColumn')->willReturn('100', '250');

        $pdo = $this->createMock(PDO::class);
        $pdo->expects($this->exactly(2))->method('prepare')->willReturn($statement);

        $this->assertSame(100.0, getSavingCurrentAmount($pdo, 7, 42));
        $this->assertSame(250.0, getSavingCurrentAmount($pdo, 7, 42));
    }

    private function pdoReturning(mixed $fetchColumn): PDO
    {
        $statement = $this->createMock(PDOStatement::class);
        $statement->method('execute')->willReturn(true);
        $statement->method('fetchColumn')->willReturn($fetchColumn);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($statement);

        return $pdo;
    }
}
