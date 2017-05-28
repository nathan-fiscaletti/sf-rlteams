<h2>Team Special Forces - Rocket League Team Generator</h2>

<?php
    set_time_limit(1000);

    include_once 'sf-rlteams/RocketLeagueTeamGenerator.php';

    $con = mysqli_connect('127.0.0.1', 'root', 'e548e6ec52806d3e61fe833f40a76ddaa27f01225f45973d', 'teamsf');
    
    function steamProfileToDisplayName($player, $con) {
            $res = mysqli_query($con, 'select * from player where profile = \''.$player.'\'');
            $arr = mysqli_fetch_assoc($res);
            return $arr['commonName'];
    }

    if (isset($_POST['add'])) {
        if (isset($_POST['player_common_name']) && isset($_POST['player_profile_id'])) {
                if (isset($_POST['password']) && $_POST['password'] == 'Draynaen00!') {
                    mysqli_query($con, 'insert into player (commonName,profile) values (\''.$_POST['player_common_name'].'\', \''.$_POST['player_profile_id'].'\')');
                }
        }
    }
?>


<html>
    <body>
        <b>Add Player</b><br />
        <form action='./' method='post' id='add_player' style='border:1px solid black;padding:5px;'>
            Common Name : <input type='text' name='player_common_name' />&nbsp;&nbsp;
            Profile ID : <input type='text' name='player_profile_id' />&nbsp;&nbsp;
            Password : <input type='password' name='password' />&nbsp;&nbsp;
            <input type='hidden' name='add' value='1' />
            <input type='submit' value='Add Player!' />
        </form>

        <br />

        <b>Select Players</b><br />
        <form action='./' method='POST' id='gForm'>
            <table>
                <tr>
                    <?php
                        $res = mysqli_query($con, 'select * from `player`');
                        
                        $rowCount = mysqli_num_rows($res);
                        $columnCount = $rowCount / 10;

                        if (($rowCount % 10) > 0)
                            $columnCount++;

                        while ($columnCount-- >= 1) {
                            ?>
                                <td valign='top' style='border: 1px solid black;padding:5px;'>
                                    <?php
                                        $i = 10;
                                        while ($i--) {

                                            $player = mysqli_fetch_assoc($res);

                                            if ($player == null)
                                                break;

                                            echo '<input style="margin-bottom:5px;" type="checkbox" name="'.$player['profile'].'" id="'.$player['profile'].'" value="1" '.((!empty($_POST[$player['profile']]))?'checked':'').'>'.$player['commonName'].'</input><br />';
                                        }
                                    ?>
                                </td>
                            <?php
                        }

                    ?>
                </tr>
            </table>

            <br /><br />
            <b>Generate Teams</b><br />
            Team Size: <input type='text' name='team_size' value='<?php if (isset($_POST["team_size"])) echo $_POST["team_size"]; else echo '0'; ?>' /><br /><br />
            <input type='hidden' value='1' name='generate' />
            <input id='gButton' type='submit' style='font-size:18px;' value='Generate Teams!' onClick='document.getElementById("gButton").disabled = true;document.getElementById("gButton").value="Generating, Please Wait... (this will take about 30s normally)";document.getElementById("gForm").submit();' />

        </form>

        <?php
            if (isset($_POST['generate'])) {

                $teamSize = $_POST['team_size'];
                unset($_POST['team_size']);
                unset($_POST['generate']);

                $players = array_keys($_POST);

                $teamGenerator = new RocketLeagueTeamGenerator();

                $teams = $teamGenerator->generateTeams($players, $teamSize, 0);

                if ($teams == null) {
                        echo "Error: Number of players is probably not divisible by team size.";
                } else {
                        header('Content-Type: application/json');

                        $newTeams = [];

                        foreach ($teams as $key => $team) {
                                foreach($team as $player => $score) {
                                        $newTeams[$key][steamProfileToDisplayName($player, $con)] = $score;
                                }
                        }

                        ?>
                        <b>Generated Teams</b><br />
                        <code style='white-space:pre;'><?php
                            echo json_encode($newTeams, JSON_PRETTY_PRINT);
                        ?></code><?php
                }

            }
        ?>

    </body>

</html>