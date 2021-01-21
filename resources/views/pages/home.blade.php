@extends('layouts.app')

@section('content')

    <div id="loaded">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card-body ss-card-left">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card-body ss-card-left">
                        <canvas id="myPieChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-body ss-card-left">
                        <canvas id="myPieChart1"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div id="loading" class="text-center">
        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div> <p>Data aan het ophalen bij Snelstart</p>
    </div>

<script>
    document.getElementById('loaded').style.display = "none";
    fetch('getCharts')
        .then(response => response.json())
        .then(data => {
                let periods = data['periods'];
                let chartData = data['chartData'];
            let thisFullYear = new Date().getFullYear();
            let lastFullYear = thisFullYear - 1;
            let lastYear = [];
            let thisYear = [];
            let lastYearCosts = [];
            let thisYearCosts = [];
            let totals = {
                lastYear: 0,
                thisYear: 0,
                lastYearCosts: 0,
                thisYearCosts: 0
            };
            let revenue_2019, revenue_2020, thisMonthlastFullYear, thisMonththisFullYear,thisMonthlastFullYearCosts,thisMonththisFullYearCosts
            for(var i = 1; i < 13; i++){
                revenue_lastFullYear = 0;
                revenue_2020 = 0;
                thisMonthlastFullYear = 0;
                thisMonthlastFullYearCosts = 0;
                thisMonththisFullYear = 0;
                thisMonththisFullYearCosts = 0;
                for(var v = 8000; v < 8003; v++){
                    if(chartData[lastFullYear][i].omzet[v]){
                        totals.lastYear +=  Math.abs(chartData[lastFullYear][i].omzet[v]);
                        thisMonthlastFullYear += Math.abs(chartData[lastFullYear][i].omzet[v]);
                    }
                    if(chartData[thisFullYear][i].omzet[v]){
                        totals.thisYear +=  Math.abs(chartData[thisFullYear][i].omzet[v]);
                        thisMonththisFullYear += Math.abs(chartData[thisFullYear][i].omzet[v]);
                    }
                }
                for(var v = 4000; v < 7000; v++){
                    if(chartData[lastFullYear][i].kosten[v]){
                        totals.lastYearCosts +=  Math.abs(chartData[lastFullYear][i].kosten[v]);
                        thisMonthlastFullYearCosts += Math.abs(chartData[lastFullYear][i].kosten[v]);
                    }
                    if(chartData[thisFullYear][i].kosten[v]){
                        totals.thisYearCosts +=  Math.abs(chartData[thisFullYear][i].kosten[v]);
                        thisMonththisFullYearCosts += Math.abs(chartData[thisFullYear][i].kosten[v]);
                    }
                }
                for(var v = 9000; v < 9999; v++){
                    if(chartData[lastFullYear][i].kosten[v]){
                        totals.lastYearCosts +=  Math.abs(chartData[lastFullYear][i].kosten[v]);
                        thisMonthlastFullYearCosts += Math.abs(chartData[lastFullYear][i].kosten[v]);
                    }
                    if(chartData[thisFullYear][i].kosten[v]){
                        totals.thisYearCosts +=  Math.abs(chartData[thisFullYear][i].kosten[v]);
                        thisMonththisFullYearCosts += Math.abs(chartData[thisFullYear][i].kosten[v]);
                    }
                }
                lastYear.push(Math.abs(thisMonthlastFullYear));
                thisYear.push(Math.abs(thisMonththisFullYear));
                lastYearCosts.push(Math.abs(thisMonthlastFullYearCosts));
                thisYearCosts.push(Math.abs(thisMonththisFullYearCosts));

            }

            let ctx = document.getElementById('myChart');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    datasets: [{
                        label: lastFullYear+' kosten',
                        borderColor: 'rgb(0, 59, 153)',
                        data: lastYearCosts,
                        order: 1,
                        type: 'line'
                    }, {
                        label: thisFullYear+' kosten',
                        borderColor: 'rgb(77, 43, 0)',
                        data: thisYearCosts,
                        order: 3,
                        type: 'line'
                    }, {
                        label: lastFullYear+' omzet',
                        backgroundColor: 'rgb(0, 98, 255)',
                        borderColor: 'rgb(0, 98, 255)',
                        data: lastYear,
                        order: 2
                    },{
                        label: thisFullYear+' omzet',
                        backgroundColor: 'rgb(153, 87, 0)',
                        borderColor: 'rgb(153, 87, 0)',
                        data: thisYear,
                        order: 4
                    }],
                    labels: Object.values(periods)
                },

                options: {
                    responsive: true,
                    title:{
                        display:true,
                        text: 'Omzet versus kosten per maand -  '+ lastFullYear + ' ('+ totals.lastYear + ') ten opzichte van '+ thisFullYear +' ('+ totals.thisYear + ')'
                    }
                }
            });


            let ctx1 = document.getElementById('myPieChart');
            new Chart(ctx1, {
                type: 'pie',
                data: {
                    labels: ["Omzet", "Kosten"],
                    datasets: [{
                        backgroundColor: ["#3e95cd", "#8e5ea2"],
                        data: [totals['thisYear'],totals['thisYearCosts']]
                    }]
                },
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: 'Kosten versus omzet huidige jaar'
                    }
                }
            });
            let ctx2 = document.getElementById('myPieChart1');
            new Chart(ctx2, {
                type: 'pie',
                data: {
                    labels: ["Omzet", "Kosten"],
                    datasets: [{
                        backgroundColor: ["#3e95cd", "#8e5ea2"],
                        data: [totals['lastYear'],totals['lastYearCosts']]
                    }]
                },
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: 'Kosten versus omzet vorige jaar'
                    }
                }
            });
            document.getElementById('loading').style.display = "none";
            document.getElementById('loaded').style.display = "initial";
        });

</script>

@endsection
