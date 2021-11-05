const leagueList = document.querySelector('select#league');
const seasonList = document.querySelector('select#season');
const roundList  = document.querySelector('select#round');

const search     = document.querySelector('input#search');

roundList.disabled = true;

const initForms = function () {

    const getRounds = function (season, league) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var response = this.responseText;
                next( JSON.parse(response) );
            }
        };
        xhttp.open("GET", '/rounds?season=' + season + '&league=' +  league , true); 
        xhttp.send();       
    }

    // Triggered by AJAX callback function.
    const next = function( request ) {
        const rounds = request.response;

        roundList.length = 0;
        roundList.appendChild(new Option('Select a round', ''));
        roundList.options[0].disabled = true;             

        for (var i = 0; i < rounds.length; i++) {
            var opt = document.createElement('option');
            opt.value = rounds[i];
            opt.innerHTML = rounds[i];
            if (currentRound == rounds[i] ) opt.selected = true;
            roundList.appendChild(opt);
        }
        roundList.disabled = false;
    }

    const filter_table = function (text) {

        var table = document.getElementById('result');

        for (var i = 0, row; row = table.rows[i]; i++) {                        
            if ( text.length < 3) {                
                row.style.display = '';
            } else {                
                if (row.dataset.teams == 'title') {
                    row.style.display = '';
                } else {                    
                    console.log( row.dataset.teams + ' ? ' + text + ':' + row.dataset.teams.search(text) );
                    if ( row.dataset.teams.toUpperCase().search(text.toUpperCase()) == -1 ) {
                        row.style.display = 'none';
                    } else {
                        row.style.display = '';
                    }
                }
            }
        }
    }

    const refreshSeasons = function (leagueIndex, currentSeason = '') {
        seasonList.length = 0;
        seasonList.appendChild(new Option('Select a season', ''));
        seasonList.options[0].disabled = true;

        const json_seasons  = leagueList.options[leagueIndex].dataset.seasons;
        const seasons       = JSON.parse(json_seasons);  
        for (var i = 0; i < seasons.length; i++) {
            var opt = document.createElement('option');
            opt.value = seasons[i].year;
            opt.innerHTML = seasons[i].year;
            if (currentSeason == seasons[i].year ) opt.selected = true;
            seasonList.appendChild(opt);
        }
        seasonList.disabled = false;
    }

    // Refresh seasons list when the selected league have changed
    leagueList.addEventListener('change', function (event) {        
        if ( event.target.selectedIndex > 0 ) {
            refreshSeasons( event.target.selectedIndex );
        }
    });

    seasonList.addEventListener('change', function (event) {
        const season = seasonList[ event.target.selectedIndex ].value;        
        const league = leagueList.options[leagueList.selectedIndex].value;        
        getRounds( season, league);
    });

    search.addEventListener('input', function (event) {
        filter_table(event.target.value);        
    });

    currentLeague = document.getElementById('currentleague').value;
    currentSeason = document.getElementById('currentseason').value;
    currentRound  = document.getElementById('currentround').value;
    
    refreshSeasons( leagueList.selectedIndex, currentSeason);
    getRounds(currentSeason, currentLeague)    
}

document.addEventListener('DOMContentLoaded', function () {
    initForms();    
});