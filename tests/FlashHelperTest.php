<?php

use PHPUnit\Framework\TestCase;

final class FlashHelperTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testSetFlashStoresTypeAndMessage(): void
    {
        setFlash('success', 'Expense has been added!');

        $this->assertSame(
            ['type' => 'success', 'message' => 'Expense has been added!'],
            $_SESSION['flash']
        );
    }

    public function testSetFlashOverwritesPreviousFlash(): void
    {
        setFlash('success', 'first');
        setFlash('error', 'second');

        $this->assertSame(['type' => 'error', 'message' => 'second'], $_SESSION['flash']);
    }

    public function testGetFlashReturnsNullWhenNoFlashIsSet(): void
    {
        $this->assertNull(getFlash());
    }

    public function testGetFlashReturnsAndClearsTheFlash(): void
    {
        setFlash('error', 'Something went wrong!');

        $flash = getFlash();

        $this->assertSame(['type' => 'error', 'message' => 'Something went wrong!'], $flash);
        $this->assertArrayNotHasKey('flash', $_SESSION);
        $this->assertNull(getFlash());
    }

    public function testGetFlashTreatsEmptyFlashAsMissing(): void
    {
        $_SESSION['flash'] = [];

        $this->assertNull(getFlash());
    }
}
