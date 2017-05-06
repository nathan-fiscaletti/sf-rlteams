<?php

include_once 'TeamGenerator.php';

class RocketLeagueTeamGenerator extends TeamGenerator {

	function __construct()
	{
		$this->gameName = "Rocket League";
	}

	/**
	 * Override the getSkilRankPlayer in your custom
	 * implementation of TeamGenerator to customize
	 * how the TeamGenerator obtains a players
	 * personal skill ranking.
	 * 
	 * @param  strsing $player
	 * @return float
	 */
	public function getSkillRankForPlayer($player) {
		// Retrieve content from web site hosting score data.
		$content = file_get_contents(
			'https://rocketleague.tracker.network/profile/steam/'.$player
		);

		// Filter out score data from content
		// annd cast it to a float value
		// before returning it.
		return floatval(str_replace(',', '', trim(explode('</', explode('"Score">', $content)[1])[0])));
	}

}