<?php
$groups = array(
    'main' => 'Main Table',
    'child' => 'Child Tables',
    'linked' => 'Linked Tables'
);
?>
<div class="document-tables-panel table-responsive">
    <table class="table document-tables-list">
        <thead>
            <tr>
                <th>Table</th>
                <th>System Name</th>
                <th>Document</th>
                <th>Status</th>
                <th class="text-right">Actions</th>
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
                                <span class="label label-success">Published</span>
                            <?php } else { ?>
                                <span class="label label-default">Unpublished</span>
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
                                            '<i class="fa fa-plus"></i> Add',
                                            array_merge(array(
                                                'controller' => $documentTable['CustomTable']['table_name'],
                                                'action' => 'add'
                                            ), $tableContext),
                                            array('class' => 'btn btn-sm btn-default', 'escape' => false)
                                        );
                                    }
                                    echo $this->Html->link(
                                        '<i class="fa fa-table"></i> Index',
                                        array_merge(array(
                                            'controller' => $documentTable['CustomTable']['table_name'],
                                            'action' => 'index'
                                        ), $tableContext),
                                        array('class' => 'btn btn-sm btn-default', 'escape' => false)
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
