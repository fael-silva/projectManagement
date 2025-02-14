<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Rules\ValidCep;

class ValidCepTest extends TestCase
{
    /** @test */
    public function it_validates_a_valid_cep()
    {
        $rule = new ValidCep();
        $this->assertTrue($rule->passes('cep', '01001-000'));
    }

    /** @test */
    public function it_rejects_invalid_ceps()
    {
        $rule = new ValidCep();

        $this->assertFalse($rule->passes('cep', '12345678'));
        $this->assertFalse($rule->passes('cep', '1234-567'));
        $this->assertFalse($rule->passes('cep', 'abcde-fgh'));
        $this->assertFalse($rule->passes('cep', '12345_678'));
    }

    /** @test */
    public function it_returns_correct_error_message()
    {
        $rule = new ValidCep();

        $this->assertEquals('O CEP fornecido é inválido.', $rule->message());
    }
}
