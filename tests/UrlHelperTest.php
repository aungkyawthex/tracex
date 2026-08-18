<?php

use PHPUnit\Framework\TestCase;

final class UrlHelperTest extends TestCase
{
    private array $server;

    protected function setUp(): void
    {
        $this->server = $_SERVER;
        unset($_SERVER['HTTPS']);
        $_SERVER['HTTP_HOST'] = 'localhost:8000';
        $_SERVER['REQUEST_URI'] = '/public/index.php';
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->server;
    }

    public function testUrlBuildsHttpAbsoluteUrl(): void
    {
        $this->assertSame('http://localhost:8000/public/index.php', url('public/index.php'));
    }

    public function testUrlUsesHttpsWhenEnabled(): void
    {
        $_SERVER['HTTPS'] = 'on';

        $this->assertSame('https://localhost:8000/public/index.php', url('public/index.php'));
    }

    public function testUrlKeepsHttpForNonOnHttpsValue(): void
    {
        $_SERVER['HTTPS'] = 'off';

        $this->assertSame('http://localhost:8000/login', url('login'));
    }

    /**
     * @dataProvider pathProvider
     */
    public function testUrlTrimsSurroundingSlashes(string $path, string $expected): void
    {
        $this->assertSame($expected, url($path));
    }

    public static function pathProvider(): array
    {
        return [
            'leading slash' => ['/login', 'http://localhost:8000/login'],
            'trailing slash' => ['login/', 'http://localhost:8000/login'],
            'both slashes' => ['/login/', 'http://localhost:8000/login'],
            'nested path' => ['public/assets/js/app.main.js', 'http://localhost:8000/public/assets/js/app.main.js'],
            'empty path' => ['', 'http://localhost:8000/'],
            'root path' => ['/', 'http://localhost:8000/'],
        ];
    }

    public function testCurrentUrlUsesRequestUriVerbatim(): void
    {
        $_SERVER['REQUEST_URI'] = '/public/expenses.php?category_id=3';

        $this->assertSame('http://localhost:8000/public/expenses.php?category_id=3', currentUrl());
    }

    public function testIsActiveMatchesCurrentPath(): void
    {
        $this->assertTrue(isActive('public/index.php'));
        $this->assertTrue(isActive('/public/index.php/'));
    }

    public function testIsActiveDoesNotMatchOtherPaths(): void
    {
        $this->assertFalse(isActive('public/expenses.php'));
    }

    public function testIsActiveIgnoresPathsWithQueryStringMismatch(): void
    {
        $_SERVER['REQUEST_URI'] = '/public/expenses.php?category_id=3';

        $this->assertFalse(isActive('public/expenses.php'));
    }
}
