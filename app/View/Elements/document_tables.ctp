<?php
$groups = array(
    'main' => __('Main Table'),
    'child' => __('Child Tables'),
    'linked' => __('Linked Tables')
);
?>
<div class="document-tables-panel table-responsive">
    <table class="table document-tables-list">
        <thead>
            <tr>
                <th><?php echo h(__('Table')); ?></th>
                <th><?php echo h(__('System Name')); ?></th>
                <th><?php echo h(__('Document')); ?></th>
                <th><?php echo h(__('Status')); ?></th>
                <th class="text-right"><?php echo h(__('Actions')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($groups as $groupKey => $groupLabel) { ?>
                <tr class="document-table-divider">
                    <td colspan="5">
                        <?php echo h($groupLabel); ?>
                        <span class="badge"><?php echo count($documentTables[$groupKey]); ?></span>
                    </td>
                </tr>
                <?php foreach ($documentTables[$groupKey] as $documentTable) { ?>
                    <tr>
                        <td><?php echo h($documentTable['CustomTable']['name']); ?></td>
                        <td><?php echo h($documentTable['CustomTable']['table_name']); ?></td>
                        <td>
                            <?php
                            $documentLabel = trim($documentTable['QcDocument']['document_number'] . ' - ' . $documentTable['QcDocument']['name'], ' -');
                            echo h($documentLabel);
                            ?>
                        </td>
                        <td>
                            <?php if ($documentTable['CustomTable']['publish']) { ?>
                                <span class="label label-success"><?php echo h(__('Published')); ?></span>
                            <?php } else { ?>
                                <span class="label label-default"><?php echo h(__('Unpublished')); ?></span>
                            <?php } ?>
                        </td>
                        <td class="text-right">
                            <?php if ($groupKey === 'main') { ?>
                                <div class="btn-group">
                                    <?php
                                    $tableContext = array(
                                        'custom_table_id' => $documentTable['CustomTable']['id'],
                                        'qc_document_id' => $documentTable['CustomTable']['qc_document_id']
                                    );
                                    if (!empty($documentTable['CustomTable']['process_id'])) {
                                        $tableContext['process_id'] = $documentTable['CustomTable']['process_id'];
                                    }
                                    if ($documentTable['CustomTable']['publish'] && !$documentTable['CustomTable']['table_locked']) {
                                        echo $this->Html->link(
                                            '<i class="fa fa-plus"></i> ' . h(__('Add')),
                                            array_merge(array(
                                                'controller' => $documentTable['CustomTable']['table_name'],
                                                'action' => __('add')
                                            ), $tableContext),
                                            array('class' => __('btn btn-sm btn-default'), 'escape' => false)
                                        );
                                    }
                                    echo $this->Html->link(
                                        '<i class="fa fa-table"></i> ' . h(__('Index')),
                                        array_merge(array(
                                            'controller' => $documentTable['CustomTable']['table_name'],
                                            'action' => __('index')
                                        ), $tableContext),
                                        array('class' => __('btn btn-sm btn-default'), 'escape' => false)
                                    );
                                    ?>
                                </div>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
        </tbody>
    </table>
</div>

<style type="text/css">
    .document-tables-panel { padding-top: 15px; }
    .document-tables-list > tbody > tr > td { vertical-align: middle; }
    .document-tables-list .document-table-divider > td {
        background: #f2f2f2;
        color: #333;
        font-weight: 700;
    }
    .document-table-divider .badge { margin-left: 6px; }
</style>
