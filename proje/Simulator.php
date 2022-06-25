<?php
    
    include_once("Week.php");

    //$RemainingWeeks;
    $RemainingMatches;
    $CurrentWeek;
    $possibleResults;
    

    $weekNumber= $_POST['week'];

    if($weekNumber=="All"){
        $teams = array("A","B","C","D");
        $championsLeague = new League();
	    $championsLeague->InitializeSeason($teams);
        for($i=0;$i<6;$i++){
                GetCurrentWeek($i,TRUE);    
        }
    }
    else{
        if($weekNumber<6){
            GetCurrentWeek($weekNumber,FALSE);   
            if($weekNumber>=3 && $weekNumber<5)
                GetPossibilities($weekNumber);
        }
        else{
            echo("-1");
        }
        
    }

    function GetCurrentWeek($weekNumber,$isAllWeeks){
        $CurrentWeek=new Week($weekNumber);
        $CurrentWeek->LoadMatches();
        $CurrentWeek->Process();
        $CurrentWeek->UpdateWeek();
        $CurrentWeek->CreateTable();
        $CurrentWeek->GetHtml($isAllWeeks);
    }

    function GetPossibilities($weekNumber){
        //$RemainingWeeks=array();
        $RemainingMatches=array();
        $possibleResults=array();
        $index=0;
        for($i=$weekNumber+1;$i<6;$i++){
            $week=new Week($i);
            $week->LoadMatches();
            $RemainingMatches[$index*2]=$week->Match1;
            $RemainingMatches[($index*2)+1]=$week->Match2;
            //$RemainingWeeks[$index]=$week;    
            $index++;
        }
        $possibleResults=array();
        $numberOfRemainingMatches=count($RemainingMatches);
        $maxStrIn3Base="";
        for($i=0;$i<$numberOfRemainingMatches;$i++){
            $maxStrIn3Base.="2";
        }
        $maxNumberInBase3=(int)$maxStrIn3Base;
        $maxNumberInBase10=base_convert($maxNumberInBase3,3,10);
        $NumberOfPossibleLeagueWins=array("A"=>0,"B"=>0,"C"=>0,"D"=>0);
        $numberOfWinChanges=$maxNumberInBase10+1;
        // 1:home, 0:draw, 2:away
		// if it remains 4 match we have numbers from 0000 to 2222 in base 3. this gives us all possible results for 4 matches
		// if it remains 2 match we have numbers from 00 to 22 in base 3. this gives us all possible results for 2 matches
        for($i=0;$i<$maxNumberInBase10+1;$i++){
            $num=base_convert($i,10,3);
            $len=strlen($num);
            $res="";
            for($k=0;$k<$numberOfRemainingMatches-$len;$k++){
                $res.="0";
            }
            $num=$res.$num;
            $allMatchResultsInAWeek=str_split($num);
            $weeklyPoints=array("A"=>0,"B"=>0,"C"=>0,"D"=>0);
            //initiliaze teams previous total points
            $weeklyPoints[$RemainingMatches[0]->Team1->Name]=$RemainingMatches[0]->Team1->Points;
            $weeklyPoints[$RemainingMatches[0]->Team2->Name]=$RemainingMatches[0]->Team2->Points;
            $weeklyPoints[$RemainingMatches[1]->Team1->Name]=$RemainingMatches[1]->Team1->Points;
            $weeklyPoints[$RemainingMatches[1]->Team2->Name]=$RemainingMatches[1]->Team2->Points;
            for($l=0;$l<$numberOfRemainingMatches;$l++){
                $firstTeam=$RemainingMatches[$l]->Team1;
                $secondTeam=$RemainingMatches[$l]->Team2;
                //initiliaze teams previous total points
                //$weeklyPoints[$firstTeam->Name]=$firstTeam->Points;
                //$weeklyPoints[$secondTeam->Name]=$secondTeam->Points;
                $matchResult=$allMatchResultsInAWeek[$l];
                if($matchResult=="0"){
                    $weeklyPoints[$firstTeam->Name]+=1;
                    $weeklyPoints[$secondTeam->Name]+=1;
                }
                else if($matchResult=="1"){
                    $weeklyPoints[$firstTeam->Name]+=3;
                }
                else {
                    $weeklyPoints[$secondTeam->Name]+=3;
                }
            }
            //sorts teams in descending order according to their weekly points
            arsort($weeklyPoints);
            $keys = array_keys($weeklyPoints);
            $winnerTeam=$keys[0];
            $secondWinner=$keys[1];
            $thirdWinner=$keys[2];
            $fourthWinner=$keys[3];
            $NumberOfPossibleLeagueWins[$winnerTeam]+=1;    
            if($weeklyPoints[$winnerTeam]==$weeklyPoints[$secondWinner]){
                $NumberOfPossibleLeagueWins[$secondWinner]+=1;    
                $numberOfWinChanges+=1;
                if($weeklyPoints[$secondWinner]==$weeklyPoints[$thirdWinner]){
                    $numberOfWinChanges+=1;
                    $NumberOfPossibleLeagueWins[$thirdWinner]+=1;
                    if($weeklyPoints[$thirdWinner]==$weeklyPoints[$fourthWinner]){
                        $numberOfWinChanges+=1;
                        $NumberOfPossibleLeagueWins[$fourthWinner]+=1;    
                    }    
                }
            }
        }
        GetTable($NumberOfPossibleLeagueWins,$weekNumber,$numberOfWinChanges);
    }

    function GetTable($possibleWins,$week,$numberOfWinChanges){
        arsort($possibleWins);
        $keys = array_keys($possibleWins);
        echo("<table style='margin-left:10px;float:left;width:10%;background-color:white;margin-bottom:10px;margin-top:10px' border='1'>");
        echo("<tbody>");
        
        echo("<tr>");
        echo("<td colspan='2' style='text-align:center'>");
        echo(($week+1)." Week Predictions of Championship");
        echo("</td>");
        echo("</tr>");
        for($i=0;$i<count($keys);$i++){
            echo("<tr>");
            echo("<td>".$keys[$i]."</td>");
            echo("<td> %".number_format((100 * $possibleWins[$keys[$i]] / $numberOfWinChanges),2) ."</td>");
            echo("</tr>");    
        }
        echo("</tbody>");
        echo("</table>");
    }

?>

