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
		$ranks = [];
		foreach ($ranks_to_average as $rank)
		{
			$arr = explode('>', explode('<div class="season-rank">', explode($rank, $this->content)[1])[0]);
			$ranks[] = (int)str_replace(',', '', $arr[count($arr) - 1]);
		}

		// Sort the ranks by MMR
		usort($ranks, function ($rank1, $rank2) {
			if ($rank1 == $rank2)
				return 0;

			return ($rank1 > $rank2) ? -1 : 1;
		});

		// Return the average of the two best ranks for a player.
		return (int)(($ranks[0] + $ranks[1]) / 2);
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