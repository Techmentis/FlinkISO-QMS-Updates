<canvas id="linechart<?php echo $count;?>" ></canvas>
<script>
    var ctx = document.getElementById('linechart<?php echo $count;?>').getContext('2d');
    var myChart = new Chart(ctx, {
        type: '<?php echo $type;?>',
        data: {
            labels: <?php echo $labels;?>,
            datasets: [{
                label: '<?php echo $title;?>',
                data: <?php echo $data;?>,
                backgroundColor: <?php echo $backgroundColor;?>,
                borderColor: <?php echo $borderColor;?>,
                borderWidth: 3,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            interaction: {
              mode: 'index',
              intersect: false
          },
          scales: {
            x: {
                display: true,
                title: {
                  display: true,
                  text: '<?php echo $title;?>'
              }
          },
          y: {
            display: true,
            title: {
              display: true,
              text: 'Value'
          }
      }
  }
}
});
</script>
