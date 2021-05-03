```code

$student = [
    'id' => '123',
    'name' => 'test',
    'age' => '28'
];

$studentHash = new StudentRedisHash();

$studentHash->init($student);
echo $studentHash->id . PHP_EOL;
echo $studentHash->name . PHP_EOL;
echo $studentHash->age . PHP_EOL;

unset($studentHash->id);

echo $studentHash;

echo $studentHash->toJson();

var_dump($studentHash->toArray());
```