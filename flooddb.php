<?php

/**
 * @param $minLength
 * @param $maxLength
 * @return string
 * @throws Exception
 */
function randString($minLength, $maxLength)
{
    return bin2hex(random_bytes(rand($minLength, $maxLength)));
}

/**
 * @param $sequence
 * @param $lastTime
 * @return float|int
 */
function generateSnowflake(&$sequence, &$lastTime)
{
    $nowTime = floor( microtime( true ) * 1000 );
    if ($nowTime === $lastTime)
    {
        $sequence += 1;
    }
    else
    {
        $sequence = 0;
    }

    $lastTime = $nowTime;

    return ($nowTime << 8) | $sequence;
}

$db = null;
$count = 10;
foreach ($argv as $strArg)
{
    $arg = explode('=', $strArg);
    if (count($arg) != 2) {
        continue;
    }
    if ($arg[0] === 'db') {
        $db = $arg[1];
    }

    if ($arg[0] === 'count') {
        $count = (int)$arg[1];
    }
}

if (!$db) {
    echo "Please, provide db argument (db=...)";
    exit;
}

$dumpHandle = \fopen('dump.sql', 'w+');
fwrite($dumpHandle, "use `{$db}`;" . PHP_EOL);
$lastTime = 0;
$sequence = 0;

for( $userId = 0; $userId < $count; $userId++)
{
    $email = randString(15, 30) . '@' . randString(5,10) . '.' . randString(2,5);
    $password = randString(30, 40);
    $role = randString(5, 5);
    $regDate = (new DateTime())->sub((new DateInterval('P5D')))->format('Y-m-d H:i:s');
    $lastVisit = (new DateTime())->format('Y-m-d H:i:s');
    $lineUser = "INSERT INTO `{$db}`.`users` (`id`, `email`, `password`, `role`, `reg_date`, `last_visit`)" . PHP_EOL .
        "values ({$userId}, '{$email}', '{$password}', '{$role}', '{$regDate}', '{$lastVisit}');" . PHP_EOL;
    fwrite($dumpHandle, $lineUser);

    $numProps = rand(5, 10);
    $propHead = "INSERT INTO `{$db}`.`users_about` (`id`, `user`, `item`, `value`) VALUES" . PHP_EOL;
    fwrite($dumpHandle, $propHead);
    $lineProps = [];
    for ($j = 0; $j < $numProps; $j++)
    {
        foreach (['country', 'firstname', 'state'] as $item)
        {
            $id = generateSnowflake($sequence, $lastTime);
            if ($item == 'country')
            {
                $countries = ['Russia', 'USA', 'Ukraine', 'France', 'Italy', 'Spain', 'China', 'Japan', 'Korea', 'Brasil', 'Mexico'];
                $value = $countries[array_rand($countries)];
            }
            elseif ( $item == 'state')
            {
                $states = ['NY', 'CA', 'CS', 'FL', 'IL', 'AB', 'OH', 'MA', 'MI', 'KY'];
                $value = $states[array_rand($states)];
            }
            elseif ($item == 'firstname')
            {
                $names = ['Kirill', 'Alex', 'Vova', 'Dmitry', 'Igor', 'Michael', 'Alena', 'Max', 'Jimbo'];
                $value = $names[array_rand($names)];
            }
            else
            {
                $value = randString( 70, 90 );
            }
            $lineProps[] = "({$id}, {$userId}, '{$item}', '{$value}')";
        }
    }
    $lineProps = implode(',' . PHP_EOL, $lineProps) . ';' . PHP_EOL;
    fwrite($dumpHandle, $lineProps);
}

fclose($dumpHandle);