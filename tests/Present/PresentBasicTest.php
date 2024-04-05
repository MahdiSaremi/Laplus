<?php

namespace Rapid\Laplus\Tests\Present;

use Illuminate\Database\Eloquent\Model;
use Rapid\Laplus\Present\HasPresent;
use Rapid\Laplus\Present\Present;
use Rapid\Laplus\Tests\TestCase;

class PresentBasicTest extends TestCase
{

    public function make(array $attributes = [])
    {
        return new class($attributes) extends Model
        {
            use HasPresent;

            public function getPresent() : Present
            {
                return new class extends Present
                {
                    protected function present()
                    {
                        $this->id();
                        $this->text('name');
                    }
                };
            }
        };
    }

    public function test_fillable()
    {
        $model = $this->make();

        $this->assertSame(['id', 'name'], $model->getFillable());
    }

    public function test_casts()
    {
        $model = $this->make();

        $this->assertSame(['id' => 'int'], $model->getCasts());
    }

    public function test_casting_default()
    {
        $model = $this->make([
            'id' => '120',
        ]);

        $this->assertSame(120, $model->id);
    }

}