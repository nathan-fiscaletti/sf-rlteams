<?php

include_once 'RocketLeagueTeamGenerator.php';

// Clear the console, since we're running
// this example via CLI.
for ($i = 0; $i < 50; $i++) echo PHP_EOL;

// Array of 12 valid rocket league players
// (
//     Since some of them don't have custom
//     profile url's set up, I have to use
//     their profile url numbers instead.
// )

$testPlayers = [
	'thefiscster',
	'VestolGaming',
	'ChewyTangy',

	'DOGDWARF',
	'Frutz',
	'kev1ne',

	'76561198011484192',
	'reecerl',
	'76561198153554726',

	'76561198156372996',
	'fiveftoffun',
	'freddykgaming'
];

// Create a new team generator.
// The closure in the constructor is 
// how the team generator will generate
// a score for each player. You can customize
// this as you like.
$teamGenerator = new RocketLeagueTeamGenerator();

// Since we have a list of 12 players, we can generate teams
// of any number that 12 is divisible by. So we can generate
// for all three game modes.
// 
// Note:
//     This can also be used to generate
//     teams larger than 4. So long as
//     the player count is divisible by
//     the team size.
//     


// Get an initial time for benchmarking.
$started = time();

// The extra '1' at the end of the call
// tells the system that we want to print
// to cli while it works. 
// 
// You can ommit this parameter all together and 
// it will siply not print to console.
$teams = $teamGenerator->generateTeams($testPlayers, 3, 1); 

// Other Examples.
//$teams = $teamGenerator->generateTeams($testPlayers, RLGameType::Doubles);
//$teams = $teamGenerator->generateTeams($testPlayers, RLGameType::Chaos);

if ($teams == null) {
	echo 'Your player count probably isn\'t divisible by your team size.'.PHP_EOL;
	exit;
}

echo 'Generated Teams' . PHP_EOL;
echo '------------------------------------' . PHP_EOL;

print_r($teams);

echo '------------------------------------' . PHP_EOL;

echo PHP_EOL;
echo 'Completed in (' . (time() - $started) . 's) !'.PHP_EOL;