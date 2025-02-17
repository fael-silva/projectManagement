<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Rules\ValidStatus;

class ValidStatusTest extends TestCase
{
    /** @test */
    public function it_validates_valid_statuses()
    {
        $rule = new ValidStatus();

        $this->assertTrue($rule->passes('status', 'planejado'));
        $this->assertTrue($rule->passes('status', 'em andamento'));
        $this->assertTrue($rule->passes('status', 'concluído'));
    }

    /** @test */
    public function it_rejects_invalid_statuses()
    {
        $rule = new ValidStatus();

        $this->assertFalse($rule->passes('status', 'invalido'));
        $this->assertFalse($rule->passes('status', 'completo'));
        $this->assertFalse($rule->passes('status', ''));
    }

    /** @test */
    public function it_returns_the_correct_error_message()
    {
        $rule = new ValidStatus();

        $this->assertEquals('O status fornecido é inválido.', $rule->message());
    }
}
