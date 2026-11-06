<div class="table-responsive">
<table class="table table-bordered tab-configuration-table" id="tab-configuration-table">
<thead><tr><th>Tab</th><th>Position</th><th>Rules</th></tr></thead><tbody>
<?php foreach((array)$tabConfigurationRows as $formTab):
    $group=$formTab['type']==='child_document'?(array)$childDocumentSettings:(array)$tabSettings;
    $availabilitySetting=isset($group[$formTab['key']])?$group[$formTab['key']]:array(); ?>
<tr class="tab-configuration-row" data-tab-name="<?php echo h($formTab['key']); ?>" data-tab-type="<?php echo h($formTab['type']); ?>">
<td><strong><?php echo h($formTab['name']); ?></strong></td><td><?php echo h($formTab['position']); ?></td>
<td style="min-width:500px"><?php echo $this->element('availability_rule_builder',array('availabilitySetting'=>$availabilitySetting)); ?></td>
</tr>
<?php endforeach; ?>
</tbody></table>
</div>
