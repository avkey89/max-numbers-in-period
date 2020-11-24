<?php

declare(strict_types=1);

namespace App\Tests\Feature\Services\MaxNumberOfSurfersInPeriod;

use App\Services\MaxNumberOfSurfersInPeriodService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MaxNumberOfSurfersInPeriodTest extends KernelTestCase
{
    protected ?object $service;

    protected function setUp(): void
    {
        parent::bootKernel();
        $this->service = self::$container->get(MaxNumberOfSurfersInPeriodService::class);
    }

    protected function tearDown(): void
    {
        $this->service = null;
        parent::tearDown();
    }

    public function testHandlerFailureIncorrectMaxDate()
    {
        $result = $this->service->handler('2020-11-24', 'fake date');

        $this->assertEquals("error", $result["status"]);
        $this->assertEquals("Неверный формат конечной даты", $result["message"]);
    }

    public function testHandlerFailureIncorrectMinDate()
    {
        $result = $this->service->handler('fake date', '2020-11-24');

        $this->assertEquals("error", $result["status"]);
        $this->assertEquals("Неверный формат начальной даты", $result["message"]);
    }

    public function testHandlerFailureNegativePeriod()
    {
        $result = $this->service->handler('2020-11-26 10:00:00', '2020-11-24');

        $this->assertEquals("error", $result["status"]);
        $this->assertEquals("Начальная дата не может быть больше конечной даты", $result["message"]);
    }

    public function testHandlerFailureIncorrectMaxPeriod()
    {
        $result = $this->service->handler('2020-11-24 10:10:10', '2020-11-25 10:10:11');

        $this->assertEquals("error", $result["status"]);
        $this->assertEquals("Максимальный период равен 1 день", $result["message"]);
    }

    public function testHandlerFailureIncorrectMinPeriod()
    {
        $result = $this->service->handler('2020-11-24 10:10:10', '2020-11-24 10:10:10');

        $this->assertEquals("error", $result["status"]);
        $this->assertEquals("Минимальный период равен 1 секунда", $result["message"]);
    }

    public function testHandlerSuccess()
    {
        $result = $this->service->handler('2020-11-24 11:30:00', '2020-11-24 12:29:59');

        $this->assertEquals("success", $result["status"]);
    }
}