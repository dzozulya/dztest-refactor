<?php

namespace App;

class LocalCurrencyProcessor implements LocalCurrencyProcessorInterface
{
    private const EU_COUNTRIES = ['AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI',
        'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT',
        'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK'];
    private const BIN_FETCHING_ERROR_MESSAGE = 'error fetching BIN results!';
    private const EXCHANGE_RATE_FETCHING_ERROR_MESSAGE = 'error fetching exchange rates!';
    private const INPUT_FILE_ERROR_MESSAGE = 'Invalid input File';


    private string $input;
    private array $amntsFixed = [];
    private string $binlistUrl;
    private string $exchangerateUrl;



    /**
     * @param array $amntsFixed
     */
    public function __construct(string $input, string $config='config.ini')
    {
        $this->input = $input;
        $this->loadConfig($config);
    }

    private function loadConfig(string $config)
    {
        if (!file_exists($config)) {
            throw new ProcessorException('Configuration file not found');
        }

        $configuration = parse_ini_file($config);
        if (!$configuration) {
            throw new ProcessorException('Error parsing configuration file');
        }

        $this->binlistUrl = $configuration['binlist_url'] ?? '';
        $this->exchangerateUrl = $configuration['exchangerate_url'] ?? '';

        if (empty($this->binlistUrl) || empty($this->exchangerateUrl)) {
            throw new ProcessorException('Invalid configuration file');
        }
    }


    private function isEu($countryCode)
    {
        return in_array($countryCode, self::EU_COUNTRIES) ? 'yes' : 'no';;
    }

    public function fetchBinResults($bin)
    {
        $binResults = file_get_contents('https://lookup.binlist.net/' . $bin);
        if (!$binResults) {
            throw new ProcessorException(self::BIN_FETCHING_ERROR_MESSAGE);
        }
        return json_decode($binResults);
    }

    public function fetchExchangeRate($currency)
    {
        $rates = @json_decode(file_get_contents('https://api.exchangeratesapi.io/latest'), true);

        if (!$rates) {
            throw new ProcessorException(self::EXCHANGE_RATE_FETCHING_ERROR_MESSAGE);
        }
        return $rates[$currency] ?? 0;
    }

    private function processRow($row)
    {
        $payment = explode(",", $row);
        $bin = trim(explode(':', $payment[0])[1], '"');
        $amount = trim(explode(':', $payment[1])[1], '"');
        $currency = trim(explode(':', $payment[2])[1], '"}');

        $binResults = $this->fetchBinResults($bin);
        $isEu = $this->isEu($binResults->country->alpha2);

        $rate = $this->fetchExchangeRate($currency);
        if ($currency == 'EUR' || $rate == 0) {
            $amntFixed = $amount;
        } else {
            $amntFixed = $amount / $rate;
        }
        $this->amntsFixed[] = $amntFixed * ($isEu='yes') ? 0.01 : 0.02;
    }

    public function printResult()
    {

        foreach ($this->amntsFixed as $amntFixed)

            echo $amntFixed . PHP_EOL;

    }

    public function process()
    {
        foreach (explode(PHP_EOL, file_get_contents($this->input)) as $row) {
            if (empty($row)) throw new ProcessorException(self::INPUT_FILE_ERROR_MESSAGE);
            $this->processRow($row);
        }

    }
}