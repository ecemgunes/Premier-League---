<?php 
	
	include_once("Team.php");
	
	class League{
		
		public $AllTeams;
		
        public function __construct(){
		}
		
		public function InitializeSeason($teams){
			
		    $AllTeams=array();
            
            for($x=0;$x<count($teams);$x++) {
                $team=new Team();    
                $team->InitializeTeamBeforeTheSeason($x,$teams[$x]);
                $this->AllTeams[$x]=$team;
            }
            
            $this->CreateRandomLeagueFixture();
		}
		
		private function CreateRandomLeagueFixture(){
    		if (is_file("Fixture.txt"))
                unlink("Fixture.txt");

            $fixtureFile = fopen("Fixture.txt", "a") or die("Unable to open file!");
            $matches=array();
            $i=0;
            while($i<3) {
                $count++;
                $firstTeamIndex = array_rand($this->AllTeams);
                $secondTeamIndex = array_rand($this->AllTeams);
                
                if($firstTeamIndex==$secondTeamIndex)
                    continue;
                
                $thirdTeamIndex=array_rand($this->AllTeams);
                $fourthTeamIndex=array_rand($this->AllTeams);

                if($thirdTeamIndex==$fourthTeamIndex)
                    continue;
                
                if($firstTeamIndex==$thirdTeamIndex ||$firstTeamIndex==$fourthTeamIndex || $secondTeamIndex==$thirdTeamIndex || $secondTeamIndex==$fourthTeamIndex)
                    continue;

                $firstmatch=$this->AllTeams[$firstTeamIndex]->Name."-".$this->AllTeams[$secondTeamIndex]->Name;
                if (in_array($match, $matches)) 
                    continue;
                $firstOtherMatch=$this->AllTeams[$secondTeamIndex]->Name."-".$this->AllTeams[$firstTeamIndex]->Name;
                if (in_array($firstOtherMatch, $matches)) 
                    continue;

                $secondmatch=$this->AllTeams[$thirdTeamIndex]->Name."-".$this->AllTeams[$fourthTeamIndex]->Name;
                if (in_array($secondmatch, $matches)) 
                    continue;
                $secondOtherMatch=$this->AllTeams[$fourthTeamIndex]->Name."-".$this->AllTeams[$thirdTeamIndex]->Name;
                if (in_array($secondOtherMatch, $matches)) 
                    continue;
                
                $matches[($i*2)]=$firstmatch;
                $matches[($i*2)+1]=$secondmatch;
                fwrite($fixtureFile,$firstmatch."\n");
                fwrite($fixtureFile,$secondmatch."\n");
                $i++;
            }
            for($i=3;$i>=1;$i--){
                list($a,$b)=explode("-",$matches[($i*2)-2]);
                list($c,$d)=explode("-",$matches[($i*2)-1]);
                $firstmatch=$b."-".$a;
                $secondmatch=$d."-".$c;
                fwrite($fixtureFile,$secondmatch."\n");
                fwrite($fixtureFile,$firstmatch."\n");
            }
            fclose($fixtureFile);
        }
		
		public function __destruct(){
			
		}
	}
?>
