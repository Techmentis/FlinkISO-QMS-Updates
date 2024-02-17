<?php str_replace(' ','_', Inflector::Humanize($this->request->params['pass'][0])) ;?>
<?php echo "<h4>&nbsp;&nbsp;" . __("Evidence Files") . "<small> ". $this->Html->link('Upload file with approval',array(
    'controller'=>'evidences','action'=>'lists','model'=>'dashboard_files','record'=>$this->request->params['pass'][0]),array('class'=>'pull-right btn btn-xs btn-warning')) ."</small></h4>"; ?>

    <form id="live-search<?php echo str_replace(' ','_', Inflector::Humanize($this->request->params['pass'][0])) ;?>" action="" class="styled" method="post">
       <div class="row">
          <div class="col-md-8"><h2 class="no-margin no-padding"><?php echo Inflector::Humanize($this->request->params['pass'][0]) ;?></h2></div>
          <div class="col-md-4">
             <form id="live-search" action="" class="no-padding no-margin" method="post">                
                <div class="input-group">
                    <input type="text" class="form-control btn-group pull-left" id="filter-<?php echo str_replace(' ','_', Inflector::Humanize($this->request->params['pass'][0])) ;?>" value="" /> 
                    <span id="filter-count<?php echo str_replace(' ','_', Inflector::Humanize($this->request->params['pass'][0])) ;?>" class="text-default input-group-addon">0</span>
                </div>                    
            </form>
        </div>	
    </div>

    <?php 
    if($this->request->params['pass'][0] != 'formats') echo $this->Element('files',array('filesData' => array('files'=>$files,'action'=>$this->request->params['pass'][0]))); 
    else echo 'Data added to FlinkISO&trade; application will be considred as a "Records" & Forms are available under ' . $this->Html->link("MR Dashboard",array('controller'=>'dashboards','action'=>'mr#tabs')) ;
    ?>        
</div>
</form>
<script type="text/javascript">
    $(document).ready(function(){
        $("#filter-<?php echo str_replace(' ','_', Inflector::Humanize($this->request->params['pass'][0])) ;?>").keyup(function(){
            var filter = $(this).val(), count = 0;
            $(".table .src_<?php echo str_replace(' ','_', Inflector::Humanize($this->request->params['pass'][0])) ;?>").each(function(){
                if ($(this).text().search(new RegExp(filter, "i")) < 0) {
                    $(this).hide();
                } else {             
                    $(this).show();
                    count++;
                }
            });
            var numberItems = count;
            $("#filter-count-<?php echo str_replace(' ','_', Inflector::Humanize($this->request->params['pass'][0])) ;?>").text(""+count);
        });
    });</script>

