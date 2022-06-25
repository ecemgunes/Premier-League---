<?php 
	include_once("Match.php");
    include_once("League.php");
    class Week{
		
        public $Match1;
        public $Match2;
        private $WeekNumber=0;
        private $WeeklyTable;

        public function LoadMatches(){
            $lineIndex=0;
            $fixture = fopen("Fixture.txt", "r");
            if ($fixture) {
                while (($line = fgets($fixture)) !== false) {
                    if($lineIndex==$this->WeekNumber*2){
                        list($team1,$team2)=explode("-",$line);
                        $team2 = trim(preg_replace('/\s\s+/', ' ', $team2));
                        $this->Match1=new Match();
                        $this->Match1->PrepareMatch($team1,$team2);
                    }
                    if($lineIndex==($this->WeekNumber*2)+1){
                        list($team3,$team4)=explode("-",$line);
                        $team4 = trim(preg_replace('/\s\s+/', ' ', $team4));
                        $this->Match2=new Match();
                        $this->Match2->PrepareMatch($team3,$team4);
                    }
                    $lineIndex++;
                }
            } 
            else {
                echo("Fixture can not be read");
            } 
            fclose($fixture);
        }
        
        public function Process(){
            $this->Match1->Play();
            $this->Match2->Play();
        }

        public function UpdateWeek(){
            $this->Match1->UpdateTeams();
            $this->Match2->UpdateTeams();
        }
        
        public function CreateTable(){
            $this->WeeklyTable=array();
            $this->WeeklyTable[0]=$this->Match1->Team1;
            $this->WeeklyTable[1]=$this->Match1->Team2;
            $this->WeeklyTable[2]=$this->Match2->Team1;
            $this->WeeklyTable[3]=$this->Match2->Team2;
            
            $i=0;
            while($i<4){
                if($this->WeeklyTable[$i]->Points < $this->WeeklyTable[$i+1]->Points){
                    $temp=$this->WeeklyTable[$i];
                    $this->WeeklyTable[$i]=$this->WeeklyTable[$i+1];
                    $this->WeeklyTable[$i+1]=$temp;
                    $i=0;
                    continue;
                }
                $i++;
            }
        }

        public function GetHtml($isAll){
            echo("<table style='float:left;width:60%;background-color:white;margin-bottom:10px;margin-top:10px' border='1'>");
            echo("<tbody>");
            
            echo("<tr>");
            echo("<td colspan='7' style='text-align:center'>");
            echo("Points");
            echo("</td>");
            echo("<td colspan='3' style='text-align:center'>");
            echo("Match Results");
            echo("</td>");
            echo("</tr>");
            
            echo("<tr>");
            echo("<td>Teams"."</td>");
            echo("<td style='text-align:center'>P"."</td>");
            echo("<td style='text-align:center'>O"."</td>");
            echo("<td style='text-align:center'>G"."</td>");
            echo("<td style='text-align:center'>B"."</td>");
            echo("<td style='text-align:center'>M"."</td>");
            echo("<td style='text-align:center'>Av"."</td>");
            echo("<td colspan='3' style='text-align:center'>Premier League ".($this->WeekNumber+1).". Week Match Results</td>");
            echo("</tr>");
            
            for($i=0;$i<count($this->WeeklyTable);$i++){
                echo("<tr>");
                echo("<td>".$this->WeeklyTable[$i]->Name."</td>");
                echo("<td style='text-align:center'>".$this->WeeklyTable[$i]->Points."</td>");
                echo("<td style='text-align:center'>".($this->WeeklyTable[$i]->Won + $this->WeeklyTable[$i]->Draw + $this->WeeklyTable[$i]->Lost)."</td>");
                echo("<td style='text-align:center'>".$this->WeeklyTable[$i]->Won."</td>");
                echo("<td style='text-align:center'>".$this->WeeklyTable[$i]->Draw."</td>");
                echo("<td style='text-align:center'>".$this->WeeklyTable[$i]->Lost."</td>");
                echo("<td style='text-align:center'>".($this->WeeklyTable[$i]->GoalScored - $this->WeeklyTable[$i]->GoalConceded)."</td>");
                if($i==0){
                    echo("<td style='text-align:center'>".$this->Match1->Team1->Name."</td>");
                    echo("<td style='text-align:center'>".$this->Match1->Result."</td>");
                    echo("<td style='text-align:center'>".$this->Match1->Team2->Name."</td>");    
                }
                if($i==1){
                    echo("<td style='text-align:center'>".$this->Match2->Team1->Name."</td>");
                    echo("<td style='text-align:center'>".$this->Match2->Result."</td>");
                    echo("<td style='text-align:center'>".$this->Match2->Team2->Name."</td>");    
                }
                echo("</tr>");
            }
            if(!$isAll){
                echo("<tr><td><input type='button' id='btnPlayNextWeek' next-week='".($this->WeekNumber+1)."' value='Play Next Week'></td></tr>");    
            }
            echo("<tbody>");
            echo("</table>");
            
        }
        
        public function __construct($weekNumber){
            $this->WeekNumber=$weekNumber;
        }
		
		public function __destruct(){
		
		}
	}
?>
