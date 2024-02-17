<div class="row">
  <div class="col-md-12">
    <div class="box box-default box-solid collapsed-box">
      <!-- <div class="box box-default box-solid"> -->
        <div class="box-header with-border">
          <h3 class="box-title">READ ME</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
            </button>
          </div>
          <!-- /.box-tools -->
        </div>
        <!-- /.box-header -->
        <div class="box-body" style="">
          <ol>
            <li><strong>QC Documents</strong></li>
            <ol>
              <li>These documents are foundation of any QMS system. You can create your manuals, formats, checklists, work instructions or any such doucment with in the system.</li>
              <li><strong><span class="text-info">FlinkISO  &trade; Ver 2.x</span></strong> is now integreated with <strong>ONLYOFFICE&trade; Document Editor</strong> which allows users to create, store, edit, download documents without using any native application like Office/Open Office/Libre Office/Pages etc.</li>
              <li>You can define (or additioanlly add) Standards, Clauses, Categories etc as masters for each document.</li>
              <li>You can create documents with collobration or also prepare a document and send it to mulitple users for approval.</li>
              <li>You can chose with whom these documents are share with (branches/departments/designations/users).</li>
              <li>With each document, you can define if the document (format/checklist etc) requries any data entry (yes/no) and if yes, then its frequency. </li>
              <li>In such cases, you can create <strong>Custom Tables </strong> for each document.</li>
              <li>These Custom Tables will generate unique HTML forms which will be automatially assigned to users on their dashboard, as per defiend data entry frequency and sharing.</li>
              <li class="hide">You can also directly upload files which are not supported under ONLYOFFICE. (These documents will not be version controlled.)</li>
            </ol> 
            <li><strong>Processes</strong></li>
            <ol>
              <li>Similar to documents, you can also create processes by uploading existing process document. </li>
              <li>Process will have input process & out process, owners, responsible departments etc</li>
              <li>Once the process is created, you can create custom tables for each process.</li>
            </ol>          
            <li><strong>Masters Data Required Befor You Start</strong>
              <ol>
                <li>You need to prepare initial masters like branches/locations, departments, designations, employees, users etc to start with.</li>
                <li>Use appropriate menu to goto the form and add the data.</li>
                <li>You need to first add Employees so that you can create users.</li>
                <li>You can create unlimited masters, employees, users etc.</li>
                <li>For each user you can define access control from access control option, available on user's view page</li>
                <li>It is mandatory to assign branches to users from access control page</li>
                <li>Proceed to Document Creation once you are ready with the masters.</li>
                <li><p class="text-warning"><strong>Missing masters :</strong> You can add additional masters like products, material, services, customers etc from <strong><i>Custom Tables</i></strong>. However you need to first prepare required document from <strong><i>QC Document's</i></strong> section.</p></li>
              </ol>
            </li>
            <li><strong>Custom Tables (HTML Forms)</strong></li>
            <ol>
              <li>For each document you create, you can create multiple Custom Tables (HTML forms).</li>
              <li>These forms will be available on user's dashboard for schedule data entry.</li>
              <li>Even tho, we have build a UI Interface to generate the forms, having basic knowladge of SQL/MySQL while creating these database tables is a plus.</li>
              <li>You can create multiple forms for each document. System will automatically add version numbers to these forms.</li>
              <li>Each table is <strong><u>password protected</u></strong> to avoid any accidental data loss.</li>
              <li>you can chose to hold/unpublish/lock/unlock each Custom Table.</li>
              <li><strong>Schedules data entry will be on hold incase tables are unlocked & un-published. However, users will be able access view & edit exiting data.</strong></li>              
              <li>You can link multiple tables as Parent & Child where a single table may have multiple child tables. E.g. Purchase Orders : You can have a Purchase Order table as parent table and items under purchase order as child table.</li>
            </ol>
            <li><strong>Approvals</strong></li>
            <ol>
              <li>Each document/record/s from Custom Tables can be sent for Approvals.</li>
              <li>Each record can be sent for approval to multiple users simultaniously.</li>
              <li>Creator can define if the Approvar can only VIEW the record or can also EDIT the record.</li>
              <li>Creator can also define if either EVERYONE needs to approve the record or ANYONE can approve the record.</li>
              <li>Entire Approval History is stored and linked wuth the record and will be avaiable on opening that record.</li>
            </ol>
            <li><strong>Triggers</strong></li>
            <ol>
              <li>You can create triggers against each table from the table's view page.</li>
              <li>These triggers will be executed after any user changes a field value which you have defined while creating a trigger.</li>
              <li>It will then available on a user's (user which is defined while creating trigger) dashboard for action.</li>
            </ol>
            <li><strong>Typical Basic Flow</strong>
              <br ><br ><?php echo $this->Html->image('flow.png');?>
            </li>

          </ol>
          
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->        
      <!-- /.col -->
    </div>
