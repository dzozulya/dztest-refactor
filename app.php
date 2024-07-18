<?php

if($argv[1]=='old'){
    require 'codeToRefactor.php';
} elseif ($argv[1]='new'){

    require 'vendor/autoload.php';

    try {
        $localCurrencyProcessor = new \App\LocalCurrencyProcessor($argv[2]);
        $localCurrencyProcessor->process();
        $localCurrencyProcessor->printResult();
    } catch (\App\ProcessorException $e) {
        echo $e->getMessage().PHP_EOL;
    } catch (Throwable $e) {
        echo $e->getMessage() . PHP_EOL;
    }
    }
//not refactored



//changed
/*use App\LocalCurrencyProcessor;

}*/
