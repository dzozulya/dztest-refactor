# dztest-refactor

This is refactoring solution for [task] (https://gist.github.com/PayseraGithub/634074b26e1a2a5e4b8d39b8eb050f9f)

##### run and test notes
* application created under PHP8.2  using Composer and PHPunit as composer dependency. Also created skeleton for application:
* src - folder for refactored solution
* test - folder for unit test
* app.php- console entrypoint to application
* codeToRefactor.php -old solution before refactoring
* input.txt - file with required data for solution
* invalid_input.txt - empty file for test purposes
* test_input.txt - file for test purpose (Exception Test)
* config.ini- configuration file for solution(only with api Url to save time) we can store there everything with small changes in code
* run:
You can run solution from console: **php app.php old input.txt** - for solution before refactoring
  **php app.php new input.txt** -for solution after refactoring
* run test: **php vendor/bin phpunit tests/LocalCurrencyProcessorTest.php**

##### issues: 
unstable  connect to (https://lookup.binlist.net/invalid) 429 error.This issue didn't allow me make final test of solution.