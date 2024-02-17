<canvas id="piechart<?php echo $count;?>"></canvas>
<script>
    var ctx = document.getElementById('piechart<?php echo $count;?>').getContext('2d');
    var myChart = new Chart(ctx, {
        type: '<?php echo $type;?>',
        data: {
            labels: <?php echo $labels;?>,
            datasets: [{
                label: '<?php echo $label;?>',
                data: <?php echo $data;?>,
                backgroundColor: <?php echo $backgroundColor;?>,            
            }]
        },
        options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'bottom',
            },
            title: {
                display: false,            
            }
        }
    }
});
</script>
