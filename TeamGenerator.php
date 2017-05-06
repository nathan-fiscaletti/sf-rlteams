<?php

abstract class RLGameType {
	const Chaos    = 4;
	const Standard = 3;
	const Doubles  = 2;
}

class TeamGenerator {
	public function generateTeams($players, $gameType, $verb = 0)
	{
		$size = $gameType;

		if ($verb > 0) {
			echo 'Generating ' . $this->gameTypeToString($size) . ' Teams' . PHP_EOL;
			echo '------------------------------------' . PHP_EOL;
			echo 'Player Count: ' . count($players) . PHP_EOL;
			echo 'Team Size: ' . $size . PHP_EOL;
			echo '------------------------------------' . PHP_EOL;
			echo PHP_EOL;
		}

		if (count($players) % $size !== 0) {
			if ($verb > 0) echo "Error: Number of players must be divisible by 3 to generate Standard teams." . PHP_EOL;
			return;
		}

		if ($verb > 0) {
			echo 'Processing Players' . PHP_EOL;
			echo '------------------------------------' . PHP_EOL;
		}

		$scores = [];
		
		// Retrieve the scores for each player
		foreach ($players as $player) {
			$pScore = $this->getScoreForPlayer($player);
			if ($verb > 0) 
				echo 'Processed: (' . $pScore . ') ' . $player . PHP_EOL;
		    $scores[$player] = $pScore;
		}

		if ($verb > 0) {
			echo '------------------------------------' . PHP_EOL;
			echo PHP_EOL;
		}

		
		// Sort the players by score
		uasort($scores, function ($player1, $player2) {
			if ($player1 == $player2)
				return 0;

			return ($player1 > $player2) ? -1 : 1;
		});


		// Split the players into multiple skill tiers
		// After they are sorted into the skill tiers,
		// shuffle each of these teirs to add randomization.
		// 
		// There are the same number of skill tiers as there
		// are people on each team.
		// 
		// Standard = 3 tiers of skill
		// Doubles = 2 tiers of skill
		// Chaos = 4 tiers of skill
		// etc.
		$tiers = [];
		$offset = 0;
		$decrement = $size;

		while ($decrement--) {
			$tiers[] = $this->shuffle_assoc(array_slice($scores, $offset, count($scores) / $size, true));
			$offset += (count($scores) / $size);
		}

		
		// Sort the players into teams with balance
		// by taking one player from each skill tier.
		// 
		// These teirs were randomized, so teams won't 
		// always be 100% balanced, but this way we can
		// ensure at least some balance and not rely
		// totally on randomization.
		// 
		// One player from each of the skill tiers
		// is assigned to a team.
		$teams = [];
		for ($i=0;$i<count($tiers[0]);$i++){
			foreach ($tiers as $tier) {
				$tier_keys = array_keys($tier);
				$teams['team'.($i + 1)][$tier_keys[$i]] = $tier[$tier_keys[$i]];
			}
		}
		

		return $teams;
	}

	private function getScoreForPlayer($player) {

		// Retrieve content from web site hosting score data.
		$content = file_get_contents(
			'https://rocketleague.tracker.network/profile/steam/'.$player
		);

		// Filter out score data from content
		// annd cast it to a float value
		// before returning it.
		return floatval(
			str_replace(
				',', 
				'', 
				trim(
					explode(
						'</', 
						explode(
							'"Score">', 
							$content
						)[1]
					)[0]
				)
			)
		);

	}

	private function gameTypeToString($gameType)
	{
		switch($gameType) {
			case RLGameType::Chaos    : return 'Chaos';
			case RLGameType::Standard : return 'Standard';
			case RLGameType::Doubles  : return 'Doubles';

			default : return "Unknown";
		}
	}

	private function shuffle_assoc($my_array)  
	{  
	    $keys = array_keys($my_array);  
	    shuffle($keys);  
	    foreach($keys as $key) 
	        $new[$key] = $my_array[$key];  
  		$my_array = $new;  

	    return $my_array;  
	} 
}