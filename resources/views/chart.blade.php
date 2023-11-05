<!DOCTYPE html>
<html>
<head>
  <title>Stock Comparison Chart</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" crossorigin="anonymous">
  
  <style>
    
.loader {
  transition: 0.3s cubic-bezier(1, 0.01, 0.29, 1.01);
  background-color: #fa8072;
  left: 0;
  right: 100%;
  position: absolute; 
  height: 100%;
  z-index: 101;
}

.loader i {
  position: fixed;
  left: 50%;
  transform: translate3d(-50%, -50%, 0);
  top: 50%;
color: #fff;
}

/* .loading:after {
  background-color: #fff;
  content: " ";
  position: absolute;
  top: 0;
  bottom: 0;
  width: 100%;
  z-index: 100;
} */

.form-container{
  background-color: #EEEEEE;
  padding: 2em;
  margin: 0px 50px;
}




.form-container .form-group{
  width: 700px;
  display: flex;
  justify-content: start
}
.form-container .form-group label{
  min-width: 90px;
}
.form-container .form-group:nth-child(1){
  display: flex;
    width: 700px;
  align-items: center;
  justify-content: space-between;
}

.form-container .form-group input{
  padding: 5px;
  border-radius: 10px;
  border: none;
  outline: none;
  box-shadow: 1px 1px 5px 1px;
width: 230px
}
.form-container .form-group select{
  padding: 5px;
  border-radius: 10px;
  border: none;
  outline: none;
  box-shadow: 1px 1px 5px 1px;
  flex: 1;
  max-width: 400px;

}

#monthSelectDiv select{
    padding: 5px;
  border-radius: 10px;
  border: none;
  outline: none;
  box-shadow: 1px 1px 5px 1px;
width: 250px
}
#monthSelectDiv button {
  padding: 3px 20px;
  margin-left:  40px;
  border: 1px solid darkblue;
  border-radius: 20px;
  background-color: #2788CC;
  color: #EEEEEE;
  font-size: 18px;
}

  </style>

</head>
<body>
    
    <div class="form-container">
        <div class="form-group">
          <div>
          <label for="startDate">Start Date:</label>
          <input type="date" id="date1" name="startDate">
          </div>
          
        <div>
        <label for="endDate">End Date:</label>
          <input type="date" id="date2" name="endDate">
        </div>
        </div>
    
        <div class="form-group">
            <label for="branch">Branch:</label>
            <select id="branch">
            
            </select>
        </div>

        <div class="form-group">
            <label for="itemCode">Item Code:</label>
            <select id="itemCode">
                <option value="SAL009">SAL009</option>
                
            </select>
        </div>

        <div id="monthSelectDiv" style="display: none;">
            <label for="monthSelect">Select Month:</label>
            <select id="monthSelect" onchange="handleMonthSelectChange()"></select>
            <button id="test">fetch</button>
        </div>
    </div>

  


    <h2 style="text-align: center;">Stock Chart</h2>
   
    <!-- <div class="loading">
  <div class="loader">
    <i class="fa fa-2x fa fa-circle-o-notch	fa-spin"></i>
  </div>
  
</div>  -->
<div class="loader">
    <i class="fa fa-2x fa fa-circle-o-notch fa-spin"></i>
</div>


<div class="card text-center m-5">
        <canvas id="lineChart"></canvas>
    </div>
   

    

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const date1Input = document.getElementById('date1');
        const date2Input = document.getElementById('date2');
        const branchSelect = document.getElementById('branch');
        const itemCodeSelect = document.getElementById('itemCode');
        const monthSelectDiv = document.getElementById('monthSelectDiv');
        const monthSelect = document.getElementById('monthSelect');
        const lineChart = document.getElementById('lineChart');
        let chartInstance;

        date1Input.addEventListener('change', () => {
            date2Input.min = date1Input.value;
            monthSelectDiv.style.display = 'none';
        });

        date2Input.addEventListener('change', () => {
            date1Input.max = date2Input.value;
            populateMonthSelect();
            monthSelectDiv.style.display = 'block';
        });

        function populateMonthSelect() {
            monthSelect.innerHTML = '';
            const startDate = new Date(date1Input.value);
            const endDate = new Date(date2Input.value);
            while (startDate <= endDate) {
                const option = document.createElement('option');
                option.value = startDate.getMonth() + 1;
                option.text = new Intl.DateTimeFormat('en-US', { month: 'long' }).format(startDate);
                monthSelect.appendChild(option);
                startDate.setMonth(startDate.getMonth() + 1);
            }
        }

        document.getElementById('test').addEventListener('click', function() {
            const selectedBranch = branchSelect.value;
            const selectedItemCode = itemCodeSelect.value;
            const selectedMonth = monthSelect.value;

            console.log('Branch:', selectedBranch);
            console.log('Item Code:', selectedItemCode);
            console.log('Selected Month:', selectedMonth);

            const apiUrl = 'http://127.0.0.1:8000/api/get_data';

            const requestData = {
                startDate: date1Input.value,
                endDate: date2Input.value,
                branch: selectedBranch,
                itemCode: selectedItemCode,
                selectedMonth: selectedMonth,
            };

            fetch(apiUrl, {
                method: 'POST',
                body: JSON.stringify(requestData),
                headers: {
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
               
                createChart(data);
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
        });

        function populateBranches(){
            const apiUrl2 = 'http://127.0.0.1:8000/api/getUniqueBranches';
            fetch(apiUrl2, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                data.forEach((item) => {
                    const option = document.createElement("option");
                    option.value = item.branch;
                    option.text = item.branch;  
                    branchSelect.appendChild(option);
                });             
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
        }
        
        function populateItem(branchCode){
            itemCodeSelect.innerHTML = "";
            const apiUrl2 = `http://127.0.0.1:8000/api/getItemsByBranch/${branchCode}`;
            fetch(apiUrl2, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                data.forEach((item) => {
                    const option = document.createElement("option");
                    option.value = item.item_code;
                    option.text = item.item_code;  
                    itemCodeSelect.appendChild(option);
                });             
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
        }

        populateBranches();

        branchSelect.addEventListener('change', function(){
            populateItem(this.value);

        })

        function createChart(data) {
            
            let chart = lineChart.getContext('2d');
            if (chartInstance) {
                chartInstance.destroy();
            }

            chartInstance = new Chart(chart, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Qty In',
                            data: data.qtyIn,
                            borderColor: 'blue',
                            fill: false,
                        },
                        {
                            label: 'Qty Out',
                            data: data.qtyOut,
                            borderColor: 'red',
                            fill: false,
                        },
                        {
                            label: 'Running Stock',
                            data: data.runningStock,
                            borderColor: 'green',
                            fill: false,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                },
            });

            console.log(data);
        }


        function startLoader() {
  const loader = document.querySelector(".loader");

  if (loader) {
    setTimeout(function() {
      loader.style.right = "0";
    }, 1000);

    window.addEventListener("DOMContentLoaded", function(event) {
      setTimeout(function() {
        loader.style.left = "100%";
        document.body.classList.remove("loading");
      }, 4000);
    });
  } else {
    console.error("Loader element not found in the HTML.");
  }


startLoader();
        }

    </script>
</body>
</html>
