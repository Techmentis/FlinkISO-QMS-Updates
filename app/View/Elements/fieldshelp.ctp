<div class="row">
  <div class="col-md-12">
    <div class="box box-default box-solid collapsed-box">
      <!-- <div class="box box-default box-solid"> -->
        <div class="box-header with-border">
          <h3 class="box-title">HELP</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
            </button>
          </div>
          <!-- /.box-tools -->
        </div>
        <!-- /.box-header -->
        <div class="box-body" style="">
          <ol>
            <li><strong>Varchar</strong></li>
            <ol>
              <li>Fields like name, title, phone, email, etc which generally consist alphanumrical charactors could be defined as VARCHAR</li>
              <li>Max size <strong>(Lenght)</strong>  for these fields is 255</li>              
            </ol>          
            <li><strong>Int</strong></li>
            <ol>
              <li>Fields like numbers, count, quantity etc which generally consist only numbers could be defined as INT</li>
              <li>Size <strong>(Lenght)</strong>  for these fields is generally 11</li>
            </ol>
            <li><strong>Float</strong></li>
            <ol>
              <li>Fields like currency which generally consist only numbers with decimal could be defined as FLOAT</li>
              <li>Size <strong>(Lenght)</strong>  for these fields is generally 11,2 etc</li>
            </ol>
            <li><strong>Int/Tinyint</strong></li>
            <ol>
              <li>Fields like numbers with lenght 1 can be defined as INT/TINYINT</li>
              <li>Size <strong>(Lenght)</strong>  for these fields is generally 1</li>
              <li>You can use these fields to create instant Radio Buttons or Checkboxes</li>
              <li>When you select INT/TINYINT and Lenght as 1 and Display Type as Radio/Checkbox, system will automatically ask you to enter Options.</li>
              <li>These options are values to be displayed as Radio Button/Checkbox Labels e.g. Yes,No or Shitf 1, Shift 2, Shift 3 etc</li>
            </ol>
            <li><strong>Textarea</strong></li>
            <ol>
              <li>Textarea is a alphanumerical fields used to store large data.</li>
              <li>No need to specify any lengh for textfields.</li>
            </ol>
            <li><strong>Date/Datetime</strong></li>
            <ol>
              <li>To store date or timestamps</li>
              <li>No need to specify any lengh for these fields.</li>
            </ol>
            <li><strong>Linked To</strong></li>
            <ol>
              <li>These fields are fields where the data is fetched from another table.</li>
              <li>E.g. in any of the table, which you have a field called "location", you can used "Linked To" option and link the "Location" field with "Branch" table in the system</li>
              <li>Syetm will then display list of branches for this field as Drop Down List</li>
              <li>Linked To fields are always "VHARCHAR(36)". </li>
            </ol>
          </ol>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->        
      <!-- /.col -->
    </div>
  </div>
