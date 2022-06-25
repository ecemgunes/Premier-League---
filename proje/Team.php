<?php 
	
    class Team
	{
		
        //Unique Id of the team
		public $ID=0;
		
		// Name of the team
		public $Name = "";
		
		//Power of the team assigned randomly before the league starts
		public $Power=0;
		
		//Experience of the team in the champions league
		public $Experience=0;
		
		//// Current position in league table
		//public $Position=0;
		
        public $Points=0;
        
        // Number of the matches the the team won 
		public $Won=0;
		
		// Number of the matches the the team draw 
		public $Draw=0;
		
		// Number of the matches the the team lost 
		public $Lost=0;
		
        public $GoalScored=0;

        public $GoalConceded=0;
        
        // 0-100
        public $IsHomeMatch=FALSE;

		// Current happiness of the team. It depends on the last match results 0-100
		public $Happiness=0;
		
        public $GoalsScoredInLastMatch=0;

        public $GoalsConcededInLastMatch=0;

		public $OverallMatchPower=0;

        // Constructor
		public function __construct(){
		}
		
        public function UpdateTeam(){
            $lines = file('Teams.txt');
            $result = '';
            foreach($lines as $line) {
                $line = str_replace(array("\r", "\n"), '', $line);
                list($name,$power,$experience,$happiness,$won,$draw,$lost,$goalScored,$goalconceded)=explode(";",$line);
                if($name=="Name:".$this->Name){
                    $goalconceded = str_replace(array("\r", "\n"), '', $goalconceded);
                    list($t,$happiness)=explode(":",$happiness);
                    list($t,$won)=explode(":",$won);
                    list($t,$draw)=explode(":",$draw);
                    list($t,$lost)=explode(":",$lost);
                    list($t,$goalScored)=explode(":",$goalScored);
                    list($t,$goalconceded)=explode(":",$goalconceded);
                    if($this->GoalsScoredInLastMatch > $this->GoalsConcededInLastMatch){
                        $won++;
                        $happiness+=3;
                    }
                    else if($this->GoalsScoredInLastMatch < $this->GoalsConcededInLastMatch){
                        $lost++;
                        $happiness-=3;
                    }
                    else{
                        $draw++;
                    }
                    if($happiness>20)
                        $happiness=20;
                    if($happiness<0)
                        $happiness=0;
                    
                    $goalScored+=$this->GoalsScoredInLastMatch;
                    $goalconceded+=$this->GoalsConcededInLastMatch;
                    $this->Won=$won;
                    $this->Lost=$lost;
                    $this->Draw=$draw;
                    $this->GoalScored=$goalScored;
                    $this->GoalConceded=$goalconceded;
                    $this->Happiness=$happiness;
                    $result.="Name:".$this->Name.";";
                    $result.="Power:".$this->Power.";";
                    $result.="Experience:".$this->Experience.";";
                    $result.="Happiness:".$this->Happiness.";";
                    $result.="Won:".$this->Won.";";
                    $result.="Draw:".$this->Draw.";";
                    $result.="Lost:".$this->Lost.";";
                    $result.="GoalSored:".$this->GoalScored.";";
                    $result.="GoalConceded:".$this->GoalConceded."\n";
                }
                else{
                    $result.=$line."\n";
                }
            }
            file_put_contents('Teams.txt', $result);
            $this->Points=($this->Won*3)+$this->Draw;
        }

        public function CalculateOverallMatchPower($teamName){
            $this->GetTeamInfoFromFile($teamName);
            $this->OverallMatchPower=$this->Power + $this->Experience + $this->Happiness;
            $totalrandom=0;
            for($i=0;$i<20;$i++){
                $tempRandom=0;
                if($this->IsHomeMatch==TRUE)
                {   
                    $tempRandom+=$this->CreateRandomFactor(0,10);
                }
                $tempRandom+=$this->CreateRandomFactor(-10,0);// for injuries
                $tempRandom+=$this->CreateRandomFactor(-10,10);// for chance        
                $totalrandom+=$tempRandom;
            }
            $totalrandom=ceil($totalrandom/20);
            $this->OverallMatchPower+=$totalrandom;
        }

        public function GetTeamInfoFromFile($teamName){
            $teams = fopen("Teams.txt", "r");
            if ($teams) {
                while (($line = fgets($teams)) !== false) {
                    list($name,$power,$experience,$happiness,$won,$draw,$lost,$goalScored,$goalconceded)=explode(";",$line);
                    if($name=="Name:".$teamName){
                        list($t,$this->Name)=explode(":",$name);
                        list($t,$this->Power)=explode(":",$power);
                        list($t,$this->Experience)=explode(":",$experience);
                        list($t,$this->Happiness)=explode(":",$happiness);
                        list($t,$this->Won)=explode(":",$won);
                        list($t,$this->Draw)=explode(":",$draw);
                        list($t,$this->Lost)=explode(":",$lost);
                        list($t,$this->GoalScored)=explode(":",$goalScored);
                        $goalconceded = str_replace(array("\r", "\n"), '', $goalconceded);
                        list($t,$this->GoalConceded)=explode(":",$goalconceded);
                        break;
                    }
                }
                $this->Points=($this->Won*3)+$this->Draw;
            } 
            else {
                echo("Fixture can not be read");
            } 
            fclose($teams);
        }

		//Initialize Team Before The Season
		public function InitializeTeamBeforeTheSeason($id,$name){
			$this->ID=$id;
			$this->Name=$name;
			$this->Power=$this->CreateRandomFactor(10,20);
            $this->Experience=$this->CreateRandomFactor(10,20);
            $this->Happiness=20;
            $this->WriteTeamToFile();
		}
		
		private function WriteTeamToFile(){
            if($this->ID==0){
                if (is_file("Teams.txt"))
                    unlink("Teams.txt");
            }
            $myfile = fopen("Teams.txt", "a") or die("Unable to open file!");
            fwrite($myfile,"Name:".$this->Name.";");
            fwrite($myfile,"Power:".$this->Power.";");
            fwrite($myfile,"Experience:".$this->Experience.";");
            fwrite($myfile,"Happiness:".$this->Happiness.";");
            fwrite($myfile,"Won:".$this->Won.";");
            fwrite($myfile,"Draw:".$this->Draw.";");
            fwrite($myfile,"Lost:".$this->Lost.";");
            fwrite($myfile,"GoalScored:".$this->GoalScored.";");
            fwrite($myfile,"GoalConceded:".$this->GoalConceded);
            fwrite($myfile,"\n");
            fclose($myfile);
        }
        
        private function CreateRandomFactor($bottom,$top){
			return rand($bottom,$top);
		}
		
		//Destructor
		public function __destruct(){
		
		}
	}
?>
