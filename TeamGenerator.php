<?php

abstract class TeamGenerator {

	/**
	 * The name of the game we're generating 
	 * teams for.
	 * 
	 * @var string
	 */
	public $gameName = "";

	/**
	 * Generate the teams based on the players passed
	 * and the number of players per team.
	 * 
	 * @param  array   $players The players to sort.
	 * @param  int     $size    The size of a team.
	 * @param  int     $verb    Set to 1 to show log messages.
	 * @return array
	 */
	public final function generateTeams($players, $size, $verb = 0)
	{

		// Remove duplicate player entries
		$players = array_unique($players, SORT_REGULAR);

		if ($verb > 0) {
			echo 'Generating '.(($this->gameName == '')?'Balanced':$this->gameName).' Teams' . PHP_EOL;
			echo '------------------------------------' . PHP_EOL;
			echo 'Player Count: ' . count($players) . PHP_EOL;
			echo 'Team Size: ' . $size . PHP_EOL;
			echo '------------------------------------' . PHP_EOL;
			echo PHP_EOL;
		}

		// Check for invalid player number / team size.
		if (count($players) % $size !== 0) {
			if ($verb > 0) echo "Error: Number of players must be divisible by 3 to generate Standard teams." . PHP_EOL;
			return null;
		}

		if ($verb > 0) {
			echo 'Processing Players' . PHP_EOL;
			echo '------------------------------------' . PHP_EOL;
		}

		// Retrieve the scores for each player using
		// the predefined closure to retrieve stats.
		$scores = [];
		foreach ($players as $player) {
			$pScore = $this->getSkillRankForPlayer($player);
			if ($verb > 0) 
				echo 'Processed: ' . $player . ' (' . $pScore . ') '. PHP_EOL;
		    $scores[$player] = $pScore;
		}

		if ($verb > 0) {
			echo '------------------------------------' . PHP_EOL;
			echo PHP_EOL;
		}

		
		// Sort the players based on the results
		// from the skill level retrieval closure.
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

	/**
	 * Override this function in your implementation
	 * of the class to get the skill ranking for a player.
	 * 
	 * @param  string $player The player
	 * 
	 * @return float
	 */
	public abstract function getSkillRankForPlayer($player);

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