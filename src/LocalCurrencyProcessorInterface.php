<?php

namespace App;

interface LocalCurrencyProcessorInterface
{

    public function process();

    public function printResult();
}