<?php
if ( ! defined( 'ABSPATH' ) )
    exit;
?>

<div class="tutor-report-chart">

	<?php
    echo '<div class="report-graph-title ">';
    switch ($sub_page){
        case 'this_year';
	        echo sprintf(__("Showing results for the year %s", 'tutor-pro'), $currentYear);
	        break;
	    case 'last_year';
		    echo sprintf(__("Showing results for the year %s", 'tutor-pro'), $lastYear);
		    break;
	    case 'last_month';
		    echo sprintf(__("Showing results for the month of %s", 'tutor-pro'), date("F, Y", strtotime($start_date)));
		    break;
	    case 'this_month';
		    echo sprintf(__("Showing results for the month of %s", 'tutor-pro'), date("F, Y"));
		    break;
	    case 'last_week';
		    echo sprintf(__("Showing results from %s to %s", 'tutor-pro'), $begin->format('d F, Y'), $end->format('d F, Y'));
		    break;
	    case 'this_week';
		    echo sprintf(__("Showing results from %s to %s", 'tutor-pro'), $begin->format('d F, Y'), $end->format('d F, Y'));
		    break;
	    case 'date_range';
		    echo sprintf(__("Showing results from %s to %s", 'tutor-pro'), $begin->format('d F, Y'), $end->format('d F, Y'));
		    break;
    }
    echo '</div>';
	?>

	<?php
	include TUTOR_REPORT()->path.'views/pages/courses/graph/top_menu.php';
	?>

    <canvas id="myChart" style="width: 100%; height: 250px;"></canvas>
    <script>
        var ctx = document.getElementById("myChart").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_keys($chartData)); ?>,
                datasets: [{
                    label: 'Sales',
                    backgroundColor: '#3057D5',
                    borderColor: '#3057D5',
                    data: <?php echo json_encode(array_values($chartData)); ?>,
                    borderWidth: 2,
                    fill: false,
                    lineTension: 0,
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            min: 0, // it is for ignoring negative step.
                            beginAtZero: true,
                            callback: function(value, index, values) {
                                if (Math.floor(value) === value) {
                                    return value;
                                }
                            }
                        }
                    }]
                },

                legend: {
                    display: false
                }
            }
        });
    </script>
</div>