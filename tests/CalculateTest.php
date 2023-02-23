<?php

namespace RedGreenCode\Discounts\Testing;

use PHPUnit\Framework\TestCase;
use RedGreenCode\Discounts\Calculator;
use RedGreenCode\Discounts\Inputs;

class CalculateTest extends TestCase
{
    public $inputs;
    public $calculator;

    protected function setUp(): void
    {
        $this->inputs = new Inputs;
        $this->calculator = new Calculator;
    }

    public function test1()
    {
        $this->expectOutputString($this->calculator->calculate($this->inputs->exampleData(), 'matematika', array('biológia', 'fizika', 'informatika', 'kémia')));
        print '470 (370 alappont + 100 többletpont)';
    }

    public function test2()
    {
        $this->expectOutputString($this->calculator->calculate($this->inputs->exampleData2(), 'matematika', array('biológia', 'fizika', 'informatika', 'kémia')));
        print '476 (376 alappont + 100 többletpont)';
    }

    public function test3()
    {
        $this->expectOutputString($this->calculator->calculate($this->inputs->exampleData3(), 'matematika', array('biológia', 'fizika', 'informatika', 'kémia')));
        print 'hiba, nem lehetséges a pontszámítás a kötelező érettségi tárgyak hiánya miatt';
    }

    public function test4()
    {
        $this->expectOutputString($this->calculator->calculate($this->inputs->exampleData4(), 'matematika', array('biológia', 'fizika', 'informatika', 'kémia')));
        print 'hiba, nem lehetséges a pontszámítás a magyar nyelv és irodalom tárgyból elért 20% alatti eredmény miatt';
    }
}
