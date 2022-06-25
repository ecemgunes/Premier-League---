<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Şampiyonlar Ligi</title>
    <link rel="stylesheet" type="text/css" media="all" href="style.css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
</head>
<body>

<?php
    include_once("League.php");
	$teams = array("A","B","C","D");
    $championsLeague = new League();
	$championsLeague->InitializeSeason($teams);
?>

<div class="header">
    <h2>Premier League</h2>
</div>

<div class="container">
    <input type="button" id="btnPlayNextMatch" next-week="0" value="Play Next Week">

    <input type="button" id="btnPlayAllSeason" value="Play All">
</div>

<div id="weeks" class="container">
    
</div>
    
    <script type="text/javascript">
        $(document).ready(function () {

            $( document ).on( 'click', '#btnPlayNextWeek', function () {
                $(this).attr("disabled", "disabled");
                $("#btnPlayNextMatch").attr('next-week', $(this).attr('next-week'));
                $("#btnPlayNextMatch").click();
            });
            
            $("#btnPlayNextMatch").click(function () {
                var currentWeek = $(this).attr('next-week');
                GetWeek(currentWeek);
                $(this).attr("disabled", "disabled");
                $('#btnPlayAllSeason').attr("disabled", "disabled");
			});

            $("#btnPlayAllSeason").click(function () {
                $(this).val('Play week again');
                $('#weeks').empty();
                GetWeek("All");
                $('#btnPlayNextMatch').attr("disabled", "disabled");
            });

            function GetWeek(week) {
                $.ajax({
                    type: "POST",
                    url: "Simulator.php",
                    data: { week: week },
                    error: function () {
                        alert("Match results could not be loaded!");
                    },
                    success: function (data) {
                        if (data == -1)
                            alert("All weeks loaded");
                        else {
                            //console.log(data);
                            $('#weeks').append(data);
                            $('#weeks').append('<div style="clear:both"></div>');
                            week++;
                            $("#btnPlayNextMatch").attr('next-week', week);
                        }

                    }

                });
            }



        });
    </script>

</body>
</html>