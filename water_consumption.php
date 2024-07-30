
<!DOCTYPE html>
<html>
    <style>
        .form1{
            float: right;
        }
    </style>

<script>
    const displayData = async() =>{
        let query = input.value;
        console.log("Query: ", query);
    }
</script>



    <div class="water-consumption">
        <h2>Water Consumption</h2>

        <div class="form1">
            <form name="form1" id="form1">
                <!--

                <select name="subject" id="subject">
                    <option value="" selected="selected">Day</option>
                    <option value="" selected="selected">Week</option>
                    <option value="" selected="selected">Month</option>
                </select>
                -->
                
                <label for="startDate">Start Date:</label>
                <input type="date" id="startDate" name="startDate" required>
                <label for="endDate">End Date:</label>
                <input type="date" id="endDate" name="endDate" required>
                <button type="submit">OK<i class="search"></i></button>
            </form>
        </div>


  



        <canvas id="waterChart"></canvas>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        var ctx = document.getElementById('waterChart').getContext('2d');
        var waterConsumptionChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['2024-07-01', '2024-07-02', '2024-07-03', '2024-07-04', '2024-07-05', '2024-07-06', '2024-07-07'], // Placeholder dates
                datasets: [{
                    label: 'Water Consumption (m³)',
                    data: [2.5, 3.0, 2.8, 3.2, 3.5, 3.0, 3.3], // Placeholder data
                    borderColor: '#36a2eb',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: false,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Water Consumption (m³)'
                        },
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y + ' m³';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    </script>

        <!--
        <script>
            var ctx = document.getElementById('waterChart').getContext('2d');
            var waterConsumptionChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [$INF_Date], // dates 
                    datasets: 
                    [
                        {
                        label: 'Water Consumption (m³)',
                        data: [$Metering1], 
                        borderColor: '#36a2eb',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        fill: true,
                        tension: 0.4
                        },{ 
                        data: [$Metering2],  
                        borderColor: 'rgba(54, 162, 0, 0.2)',
                        fill: true,
                        tension: 0.4
                        },{ 
                        data: [$Metering3], 
                        borderColor: 'rgba(54, 0, 235, 0.2)',
                        fill: true,
                        tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: false,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Water Consumption (m³)'
                            },
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += context.parsed.y + ' m³';
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
       

            </script>
 
   -->
        <div class="waterConsumptionFilter">
            <form name="meteringFilter" id="meteringFilter" action= 'meteringFilterFunction'>
                <select name="INF_VALUE" id="INF_VALUE">
                    <option value="metering1" selected="selected">Metering 1</option>
                    <option value="metering2" selected="selected">Metering 2</option>
                    <option value="metering3" selected="selected">Metering 3</option>
                </select>
            </form>
            <script>
                    var e = document.getElementById("INF_VALUE");
                    var value = e.value;
                    var text = e.options[e.selectedIndex].text;
            </script>
     

                 





<style>
.search{
    color: blue;
}
.dropbtn {
  background-color: #FF00FF;
  color: white;
  padding: 2px;
  font-size: 10px;
  border: none;
  cursor: pointer;
}

#myInput {
  box-sizing: border-box;
  background-image: url('searchicon.png');
  background-position: 7px 7px;
  background-repeat: no-repeat;
  font-size: 16px;
  padding: 7px 10px 6px 22px;
  border: none;
  border-bottom: 1px solid #ddd;
}

#myInput:focus {outline: 1px solid #ddd;}

.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f6f6f6;
  min-width: 115px;
  overflow: auto;
  border: 1px solid #ddd;
  z-index: 1;
}

.dropdown-content a {
  color: black;
  padding: 6px 8px;
  text-decoration: none;
  display: block;
}

.dropdown a:hover {background-color: #ddd;}

.show {display: block;}
</style>
</head>
<body style="background-color:white;">




                    
  <button onclick="myFunction()" class="dropbtn"><input type="text" placeholder="STA_Label" id="myInput" onkeyup="filterFunction()"></button>
  <div id="myDropdown" class="dropdown-content">    
                    <a href="#SAMB Recron 5124">SAMB Recron 5124</a>
                    <a href="#SAMB Infineon 5451">SAMB Infineon 5451</a>
                    <a href="#SAMB Recron 5133">SAMB Recron 5133</a>
                    <a href="#SAMB UITM Lendu">SAMB UITM Lendu</a>
                    <a href="#SAMB Aeon Bandar Melaka">SAMB Aeon Bandar Melaka</a>
                    <a href="#SAMB Kem Terendak Markas Garison">SAMB Kem Terendak Markas Garison</a>
                    <a href="#SAMB Infineon 5290">SAMB Infineon 5290</a>
                    <a href="#SAMB Infineon 7879">SAMB Infineon 7879</a>
                    <a href="#SAMB -SOFREL AMR">SAMB -SOFREL AMR</a>
                    <a href="#LS-Flow - A Famosa Water Tank">LS-Flow - A Famosa Water Tank</a>
                    <a href="#SAMB - A Famosa Reservoir (Melaka)">SAMB - A Famosa Reservoir (Melaka)</a>
                    <a href="#SAMB UTeM">SAMB UTeM</a>
                    <a href="#SAMB Sunpower Malaysia DN100">SAMB Sunpower Malaysia DN100</a>
                    <a href="#Scientex Reservoir DN30">Scientex Reservoir DN30</a>
                    <a href="#SAMB Sunpower Malaysia DN300">SAMB Sunpower Malaysia DN300</a>
                    <a href="#SAMB Xin Yi Glass DN100 6631">SAMB Xin Yi Glass DN100 6631</a>
                    <a href="#SAMB Serkam Reservoir">SAMB Serkam Reservoir</a>
                    <a href="#SAMB Politeknik Merlimau">SAMB Politeknik Merlimau</a>
                    <a href="#Scientex Reservoir DN400">Scientex Reservoir DN400</a>
                    <a href="#SAMB Maxter Glove">SAMB Maxter Glove</a>
                    <a href="#SAMB Ansell 1280">SAMB Ansell 1280</a>
                    <a href="#SAMB Ansell 1299">SAMB Ansell 1299</a>
                    <a href="#SAMB Ansell 0762">SAMB Ansell 0762</a>
                    <a href="#SAMB Hospital Besar Melaka">SAMB Hospital Besar Melaka</a>
                    <a href="#SAMB Petronas 1673 DN350">SAMB Petronas 1673 DN350</a>
                    <a href="#SAMB Petronas 4317 DN300">SAMB Petronas 4317 DN300</a>
                    <a href="#SAMB Kem Komando">SAMB Kem Komando</a>
                    <a href="#SAMB Kuarters Kem Desa Taming Sari">SAMB Kuarters Kem Desa Taming Sari</a>
                    <a href="#SAMB Penjara Sg Udang">SAMB Penjara Sg Udang</a>
                    <a href="#SAMB Kem Terendak Tg Bidara">SAMB Kem Terendak Tg Bidara</a>
                    <a href="#SAMB Kuarters Kem Terendak">SAMB Kuarters Kem Terendak</a>
                    <a href="#SAMB Xinyi Glass 4526 DN250">SAMB Xinyi Glass 4526 DN250</a>
                </select>



  </div>
</div>

<script>
/* When the user clicks on the button,
toggle between hiding and showing the dropdown content */
function myFunction() {
  document.getElementById("myDropdown").classList.toggle("show");
}

function filterFunction() {
  const input = document.getElementById("myInput");
  const filter = input.value.toUpperCase();
  const div = document.getElementById("myDropdown");
  const a = div.getElementsByTagName("a");
  for (let i = 0; i < a.length; i++) {
    txtValue = a[i].textContent || a[i].innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      a[i].style.display = "";
    } else {
      a[i].style.display = "none";
    }
  }
}
</script>
</div>
</div>
</html>







<!--



<script>
function myFunction() {
  // Declare variables
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("INF_VALUE");
  filter = input.value.toUpperCase();   

</script>\

 -->
