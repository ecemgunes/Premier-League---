<?php 
	include_once("Team.php");
    class Match{
		
        public $Team1;
        public $Team2;
        
        public $Result="";

        public function PrepareMatch($team1Name,$team2Name){
            $this->Team1=new Team();
            $this->Team1->IsHomeMatch=TRUE;
            $this->Team1->CalculateOverallMatchPower($team1Name);
            $this->Team2=new Team();
            $this->Team2->CalculateOverallMatchPower($team2Name);
        }
        
        public function Play(){
            $diff=abs($this->Team1->OverallMatchPower-$this->Team2->OverallMatchPower);
            $rnd=abs(rand(-$diff,$diff));
			if($rnd==0)
				$rnd=1;
            $this->Team1->GoalsScoredInLastMatch=ceil($this->Team1->OverallMatchPower % $rnd);
            $this->Team2->GoalsScoredInLastMatch=ceil($this->Team2->OverallMatchPower % $rnd);
            $this->Team1->GoalsConcededInLastMatch=$this->Team2->GoalsScoredInLastMatch;
            $this->Team2->GoalsConcededInLastMatch=$this->Team1->GoalsScoredInLastMatch;
            $this->Result=$this->Team1->GoalsScoredInLastMatch."-".$this->Team2->GoalsScoredInLastMatch;
            
        }
        public function UpdateTeams(){
            $this->Team1->UpdateTeam();
            $this->Team2->UpdateTeam();
        }

        public function __construct(){
		    
            
        }
		
		//Destructor
		public function __destruct(){
		
		}
	}
?>
