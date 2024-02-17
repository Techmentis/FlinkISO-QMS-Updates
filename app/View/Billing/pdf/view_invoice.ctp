<div  id="users_ajax"> 
<?php echo $this->Session->flash(); ?>

<?php echo $this->element('billing_header_lists');?>
<?php //echo $this->element('subscription');?>

<?php echo $this->element('comp',array('current_charges'=>$total));?>
  <style type="text/css">
    .invoice{margin: 0px !important;}
  </style>
  <div class="row">
    <div class="col-md-12">

      <section class="invoice">
        <!-- title row -->
        <div class="row">
          <div class="col-xs-12">
            <h2 class="page-header">
              <!-- <i class="fa fa-globe"></i>  -->
              INVOICE
              <small class="pull-right"><strong>Invoice Date: <?php echo date('d M Y',strtotime($invoice['Invoice']['invoice_date']))?></strong></small>
            </h2>
          </div>
          <!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row invoice-info">
          <div class="col-sm-4 invoice-col">
            From
            <address>
              <strong>TECHMENTIS GLOBAL SERVICES PVT LTD.</strong><br>
              D-502, Om Elegance-3 CHSL<br>
              Chincholi, Malad West, Mumbai 400064<br>
              Phone: +91 9820567801<br>
              Email: sales@flinkiso.com
            </address>
          </div>
          <!-- /.col -->
          <div class="col-sm-4 invoice-col">
            To
            <address>
              <strong><?php echo $this->Session->read('User.company_name')?></strong><br>
              <!-- 795 Folsom Ave, Suite 600<br>
              San Francisco, CA 94107<br> -->
              Phone: <?php echo $comp['Company']['phone']?><br>
              Email: <?php echo $comp['Company']['email']?>
            </address>
          </div>
          <!-- /.col -->
          <div class="col-sm-4 invoice-col">
            <b>Invoice #<?php echo $invoice['Invoice']['invoice_number'];?></b><br>
            <br>
            <!-- <b>Order ID:</b> 4F3S8J<br> -->
            <b>Payment Due:</b> <?php echo $invoice['Invoice']['expire_by'];?><br>
            <!-- <b>Account:</b> 968-34567 -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- Table row -->
        <div class="row">
          <div class="col-xs-12 table-responsive">
            <table class="table table-striped">
              <thead>
              <tr>
                <th>#</th>
                <th>Product</th>
                <!-- <th>Serial #</th> -->
                <th>Description</th>
                <th>Subtotal</th>
              </tr>
              </thead>
              <tbody>
                <?php $lineitems = json_decode($invoice['Invoice']['item_details'],true);
                foreach($lineitems as $lineitem){ ?>
                  <tr>
                    <td>1</td>
                    <td><?php echo $lineitem['name']?></td>
                    <!-- <td>455-981-221</td> -->
                    <td> <?php echo $total_file_size;?> <?php echo $lineitem['description']?></td>
                    <td><?php echo $lineitem['currency']?> <?php echo $lineitem['amount']?></td>
                  </tr>
                <?php } ?>              
              <!-- <tr>
                <td>1</td>
                <td>Monsters DVD</td>
                <td>735-845-642</td>
                <td>Terry Richardson helvetica tousled street art master</td>
                <td>$10.70</td>
              </tr>
              <tr>
                <td>1</td>
                <td>Grown Ups Blue Ray</td>
                <td>422-568-642</td>
                <td>Tousled lomo letterpress</td>
                <td>$25.99</td>
              </tr> -->
                <tr>
                  <td colspan="2">FlinkISO Ver-2.1 Clould Edition.</td>
                  <th></th>
                  <td><strong>Total: </strong><?php echo $lineitem['currency']?> <?php echo $invoice['Invoice']['amount']?></td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->

        <div class="row">
          <!-- accepted payments column -->        
          <!-- /.col -->        
          <!-- /.col -->
          <div class="col-xs-12">
            <p class="lead">Payment Methods:</p>
            <?php echo $this->Html->image('/img/img/credit/visa.png');?>
            <?php echo $this->Html->image('/img/img/credit/mastercard.png');?>
            <?php echo $this->Html->image('/img/img/credit/paypal2.png');?>
            <!-- <?php echo $this->Html->image('/img/img/credit/visa.png');?> -->
            <!-- <img src="../../dist/img/credit/visa.png" alt="Visa"> -->
            <!-- <img src="../../dist/img/credit/mastercard.png" alt="Mastercard">
            <img src="../../dist/img/credit/american-express.png" alt="American Express">
            <img src="../../dist/img/credit/paypal2.png" alt="Paypal"> -->

            <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
              Invoice due date is <?php echo $invoice['Invoice']['expire_by'];?>.<br> If not paid, this instace will be deleted and all the data will be lost.
            </p>
          </div>
        </div>
        <!-- /.row -->

        <!-- this row will not appear when printing -->
        <div class="row no-print">
          <div class="col-xs-12">
            <!-- <a href="invoice-print.html" target="_blank" class="btn btn-default"><i class="fa fa-print"></i> Print</a> -->
            
            <button type="button" class="btn btn-success pull-right">
              <i class="fa fa-credit-card"></i>&nbsp;&nbsp;<?php echo $this->Html->link('Pay',array('controller'=>'billing','action'=>'renew', date('Ymd').'- '.(count($invoices) + 1)),array('style'=>'font-weight:600;color:#fff;margin-left:8px'))?> </button>
            <!-- <button type="button" class="btn btn-primary pull-right" style="margin-right: 5px;"><i class="fa fa-download"></i> Generate PDF</button> -->
          </div>
        </div>
      </section>
    </div>
  </div>
</div>
