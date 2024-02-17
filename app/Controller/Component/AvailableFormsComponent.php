<?php
class AvailableFormsComponent extends Component {
    public function forms() {
        $forms = array(
            'Management Review : Parent'=>array(
                'parent'=>'',
                'type'=>'parent',
                'guide'=>'First add this table. Once you add this table add child tables one by one.',
                'fields'=>array('[{"dummy":"","field_name":"meeting_details","old_field_name":"meeting_details","linked_to":"-1","display_type":"0","field_type":"0","length":"255","size":"12","data_type":"text","mandetory":"1","index_show":"0","drop":"0","new":"1","sequence":"","add_disabled":"0","who_can_edit":""},{"dummy":"","field_name":"scheduled_date_time","old_field_name":"scheduled_date_time","linked_to":"-1","display_type":"0","field_type":"6","length":"255","size":"6","data_type":"datetime","mandetory":"1","index_show":"0","drop":"0","new":"1","sequence":"","add_disabled":"0","who_can_edit":"","default_date_number":"0","default_date_type":"-1","default_date_from":"-1"},{"dummy":"-1","field_name":"proposed_by","old_field_name":"proposed_by","linked_to":"Employees","display_type":"3","field_type":"0","length":"36","size":"6","data_type":"dropdown-s","mandetory":"1","index_show":"1","drop":"0","new":"1","sequence":"","add_disabled":"0","who_can_edit":""},{"field_name":"invitees","old_field_name":"invitees","linked_to":"Employees","display_type":"4","field_type":"1","length":"0","size":"12","data_type":"dropdown-m","mandetory":"1","index_show":"0","drop":"0","new":"1","sequence":"","add_disabled":"0","who_can_edit":""},{"dummy":"0","field_name":"meeting_status","old_field_name":"meeting_status","linked_to":"-1","display_type":"1","field_type":"2","length":"1","size":"12","data_type":"radio","csvoptions":"Scheduled,Conducted,Cancled","mandetory":"1","index_show":"1","drop":"0","new":"1","sequence":"","add_disabled":"0","who_can_edit":""},{"field_name":"comments5","show_comments":"Data to be added after meeting","size":"12","display_type":"7","field_type":"0","data_type":"comments","mandetory":"0","index_show":"0","new":"1","sequence":"5","linked_to":"-1","dummy":"0","drop":"0","old_field_name":"0","add_disabled":"0","who_can_edit":""},{"dummy":"","field_name":"actual_meeting_date_time","old_field_name":"actual_meeting_date_time","linked_to":"-1","display_type":"0","field_type":"6","length":"255","size":"5","data_type":"datetime","mandetory":"0","index_show":"0","drop":"0","new":"1","sequence":"","add_disabled":"1","who_can_edit":"","default_date_number":"0","default_date_type":"-1","default_date_from":"-1"},{"field_name":"attainted_by","old_field_name":"attainted_by","linked_to":"Employees","display_type":"4","field_type":"1","length":"0","size":"7","data_type":"dropdown-m","mandetory":"0","index_show":"0","drop":"0","new":"1","sequence":"","add_disabled":"1","who_can_edit":""}]'
            )
            ),
            'Management Review : Agendas'=>array(
                'parent'=>'Management Review : Parent',
                'type'=>'child',
                'guide'=>'You must add parent table "Management Review : Parent" before adding this table.',
                'fields'=>array(
                    '[{"dummy":"","field_name":"agenda_details","old_field_name":"agenda_details","linked_to":"-1","display_type":"0","field_type":"0","length":"255","size":"12","data_type":"text","mandetory":"0","index_show":"0","drop":"0","new":"1","sequence":"0","add_disabled":"0","who_can_edit":""},{"dummy":"-1","field_name":"assigned_to","old_field_name":"assigned_to","linked_to":"Employees","display_type":"3","field_type":"0","length":"36","size":"6","data_type":"dropdown-s","mandetory":"0","index_show":"0","drop":"0","new":"1","sequence":"1","add_disabled":"0","who_can_edit":""},{"dummy":"","field_name":"target_date","old_field_name":"target_date","linked_to":"-1","display_type":"0","field_type":"5","length":"255","size":"6","data_type":"date","mandetory":"0","index_show":"0","drop":"0","new":"1","sequence":"2","add_disabled":"0","who_can_edit":"","default_date_number":"0","default_date_type":"-1","default_date_from":"-1"},{"dummy":"","field_name":"closure_comments","old_field_name":"closure_comments","linked_to":"-1","display_type":"0","field_type":"1","length":"0","size":"12","data_type":"textarea","mandetory":"0","index_show":"0","drop":"0","new":"1","sequence":"3","add_disabled":"1","who_can_edit":""},{"dummy":"0","field_name":"current_status","old_field_name":"current_status","linked_to":"-1","display_type":"1","field_type":"2","length":"1","size":"12","data_type":"radio","csvoptions":"Open,Closed","mandetory":"0","index_show":"0","drop":"0","new":"1","sequence":"4","add_disabled":"1","who_can_edit":""}]'
                )
            )
        );

return $forms;
}
}
