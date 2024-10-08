<?php

namespace App\TestMethod;

class Alias{
    
    public function getMethod(){
        return [
            'testMethod' => [$this , 'getBasicHellowWorld'],
            // 'example' => [new ExampleCon , '']

        ];
    }

    public function getBasicHellowWorld(){
        return 'Hello World!';
    }
}