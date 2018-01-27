<?php

namespace TeamGenerator\Generators;

use TeamGenerator\TeamGenerator;

final class RocketLeagueTeamGenerator extends TeamGenerator {

	/**
	 * The content being used.
	 * This content is loaded in the getSkillRankForPlayer function
	 * and later used again in the getPlayerNameFor function.
	 * @var string
	 */
	private $content = null;

	/**
	 * Construct the TeamGenerator with
	 * the gameName "Rocket League".
	 */
	function __construct()
	{
		$this->gameName = "Rocket League";
	}

	/**
	 * Override the getSkillRankPlayer in your custom
	 * implementation of TeamGenerator to customize
	 * how the TeamGenerator obtains a players
	 * personal skill ranking.
	 * 
	 * @param  strsing $player
	 * @return float
	 */
	public final function getSkillRankForPlayer($player) 
	{
		// Retrieve content from web site hosting score data.
		$this->content = file_get_contents(
			'https://rocketleague.tracker.network/profile/steam/'.$player
		);

		// Select which playlists you would like to average the MMR for.
		$ranks_to_average = [
			'Ranked Duel 1v1',
			'Ranked Doubles 2v2',
			'Ranked Solo Standard 3v3',
			'Ranked Standard 3v3',
			'Un-Ranked'
		];

		// Collect the MMR for each playlist.
		$average = [];
		foreach ($ranks_to_average as $rank)
		{
			$arr = explode('>', explode('<div class="season-rank">', explode($rank, $this->content)[1])[0]);
			$average[] = (int)str_replace(',', '', $arr[count($arr) - 1]);
		}
		
		// Return the average MMR for the player.
		return (int)(array_sum($average) / count($average));
	}

	/**
	 * Retrieve the Name for a player.
	 * This can be useful for converting Steam IDs.
	 *
	 * This defaults to the key value in the $players array
	 * passed to the generateTeams function.
	 *
	 * This is called AFTER getSkillRankForPlayer, so you should
	 * request your players data from API in getSkillRankForPlayer
	 * and cache it for use with this function later.
	 *
	 * @param  string $player The player.
	 * @return string The player name.
	 */
	public final function getNameFor($player)
	{
		return trim(explode('<', explode('</i>', explode('<h1 class="name">', $this->content)[1])[1])[0]);
	}

}