<div class="row">
  <div class="col-md-12">
    <div class="box box-default box-solid collapsed-box">
      <!-- <div class="box box-default box-solid"> -->
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo h(__('HELP')); ?></h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
            </button>
          </div>
          <!-- /.box-tools -->
        </div>
        <!-- /.box-header -->
        <div class="box-body" style="">
          <ol>
            <li><strong><?php echo h(__('Varchar')); ?></strong></li>
            <ol>
              <li><?php echo h(__('Fields like name, title, phone, email, etc which generally consist alphanumrical charactors could be defined as VARCHAR')); ?></li>
              <li><?php echo h(__('Max size')); ?> <strong><?php echo h(__('(Lenght)')); ?></strong>  <?php echo h(__('for these fields is 255')); ?></li>              
            </ol>          
            <li><strong><?php echo h(__('Int')); ?></strong></li>
            <ol>
              <li><?php echo h(__('Fields like numbers, count, quantity etc which generally consist only numbers could be defined as INT')); ?></li>
              <li><?php echo h(__('Size')); ?> <strong><?php echo h(__('(Lenght)')); ?></strong>  <?php echo h(__('for these fields is generally 11')); ?></li>
            </ol>
            <li><strong><?php echo h(__('Float')); ?></strong></li>
            <ol>
              <li><?php echo h(__('Fields like currency which generally consist only numbers with decimal could be defined as FLOAT')); ?></li>
              <li><?php echo h(__('Size')); ?> <strong><?php echo h(__('(Lenght)')); ?></strong>  <?php echo h(__('for these fields is generally 11,2 etc')); ?></li>
            </ol>
            <li><strong><?php echo h(__('Int/Tinyint')); ?></strong></li>
            <ol>
              <li><?php echo h(__('Fields like numbers with lenght 1 can be defined as INT/TINYINT')); ?></li>
              <li><?php echo h(__('Size')); ?> <strong><?php echo h(__('(Lenght)')); ?></strong>  <?php echo h(__('for these fields is generally 1')); ?></li>
              <li><?php echo h(__('You can use these fields to create instant Radio Buttons or Checkboxes')); ?></li>
              <li><?php echo h(__('When you select INT/TINYINT and Lenght as 1 and Display Type as Radio/Checkbox, system will automatically ask you to enter Options.')); ?></li>
              <li><?php echo h(__('These options are values to be displayed as Radio Button/Checkbox Labels e.g. Yes,No or Shitf 1, Shift 2, Shift 3 etc')); ?></li>
            </ol>
            <li><strong><?php echo h(__('Textarea')); ?></strong></li>
            <ol>
              <li><?php echo h(__('Textarea is a alphanumerical fields used to store large data.')); ?></li>
              <li><?php echo h(__('No need to specify any lengh for textfields.')); ?></li>
            </ol>
            <li><strong><?php echo h(__('Date/Datetime')); ?></strong></li>
            <ol>
              <li><?php echo h(__('To store date or timestamps')); ?></li>
              <li><?php echo h(__('No need to specify any lengh for these fields.')); ?></li>
            </ol>
            <li><strong><?php echo h(__('Linked To')); ?></strong></li>
            <ol>
              <li><?php echo h(__('These fields are fields where the data is fetched from another table.')); ?></li>
              <li><?php echo h(__('E.g. in any of the table, which you have a field called "location", you can used "Linked To" option and link the "Location" field with "Branch" table in the system')); ?></li>
              <li><?php echo h(__('Syetm will then display list of branches for this field as Drop Down List')); ?></li>
              <li><?php echo h(__('Linked To fields are always "VHARCHAR(36)".')); ?> </li>
            </ol>
          </ol>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->        
      <!-- /.col -->
    </div>
  </div>
