<?php
$builderFields = isset($availabilityRuleFields) ? $availabilityRuleFields : array();
if(!$builderFields) foreach((array)$visibilityFields as $f){ $f['source']='record'; $f['label']='Record: '.$f['label']; $builderFields[]=$f; }
$hasPageAction = false;
foreach($builderFields as $field) if(isset($field['source'],$field['name']) && $field['source']==='page' && $field['name']==='action') $hasPageAction=true;
if(!$hasPageAction) array_unshift($builderFields,array('source'=>'page','name'=>'action','label'=>'Action','options'=>array(array('value'=>'add','label'=>'Add'),array('value'=>'edit','label'=>'Edit'))));
foreach($builderFields as $index => $field){
    if(isset($field['source'], $field['name']) && $field['source'] === 'workflow' && $field['name'] === 'approval_step_id'){
        unset($builderFields[$index]);
        array_unshift($builderFields, $field);
        break;
    }
}
$availabilityModuleId = !empty($customTable['CustomTable']['id']) ? $customTable['CustomTable']['id'] : '';
$availabilityUnlockUrl = $availabilityModuleId !== '' ? Router::url(array('controller'=>'custom_tables','action'=>'unlock_rule_module',$availabilityModuleId)) : '';
?>
<div class="availability-rule-builder" data-unlock-url="<?php echo h($availabilityUnlockUrl); ?>" data-setting="<?php echo h(json_encode(isset($availabilitySetting)?$availabilitySetting:array())); ?>" data-fields="<?php echo h(json_encode($builderFields)); ?>">
    <p class="help-block" style="font-size:12px;margin:0 0 6px">Visible by default. Rules are checked from top to bottom. A match or an Else result stops further rules. Within a rule: Hide overrides Read-only, which overrides Show.</p>
    <div class="availability-rules"></div>
    <button type="button" class="btn btn-sm btn-default availability-add-rule">Add rule</button>
    <span class="availability-error text-danger" role="alert"></span>
</div>
<script>
(function(){
if(!window.FlinkisoAvailabilityRules){
    $('<style/>').text(`
.availability-rule-builder{color:#444;font-size:13px;line-height:1.5}
.availability-rule-builder .availability-rule{border:1px solid #ddd;border-radius:3px;margin:10px 0;padding:12px;background:#fff}
.availability-rule-builder .rule-heading{display:flex;align-items:center;justify-content:space-between;gap:12px;border-bottom:1px solid #eee;padding-bottom:8px;margin-bottom:12px}
.availability-rule-builder .rule-tools{display:flex;gap:4px;flex-wrap:wrap}
.availability-rule-builder .btn-link{font-size:12px;color:#555;padding:3px 5px;text-decoration:underline}
.availability-rule-builder .btn-link:disabled{opacity:.35;text-decoration:none}
.availability-rule-builder .rule-conditions{overflow-x:auto}
.availability-rule-builder .chosen-container-single-nosearch .chosen-search{height:0!important;padding:0!important;margin:0!important}
.availability-rule-builder .chosen-container .chosen-drop.rule-floating-drop{position:fixed!important;z-index:20010!important;box-sizing:border-box}
.availability-rule-builder .availability-condition{display:grid;grid-template-columns:minmax(180px,2fr) minmax(130px,1fr) minmax(180px,2fr) minmax(125px,1fr) auto;gap:8px;align-items:start;min-width:730px;margin:8px 0}
.availability-rule-builder .availability-condition.rule-has-else{grid-template-columns:minmax(180px,2fr) minmax(130px,1fr) minmax(180px,2fr) minmax(125px,1fr) minmax(150px,1fr) auto;min-width:900px}
.availability-rule-builder .rule-conditions+.rule-else-control{width:190px;margin:8px 0}
.availability-rule-builder .rule-control{display:block;margin:0;min-width:0;font-weight:normal}
.availability-rule-builder .rule-control>span{display:block;font-size:12px;color:#666;margin-bottom:3px}
.availability-rule-builder .rule-else-control>span{font-weight:bold}
.availability-rule-builder .rule-control select{width:100%;min-width:0}
.availability-rule-builder .rule-connector{margin:10px 0 2px}
.availability-rule-builder .rule-join{width:90px;font-weight:bold}
.availability-rule-builder .rule-remove-condition{align-self:end;margin-bottom:4px}
.availability-rule-builder .rule-always{display:flex;align-items:center;gap:18px;padding:8px 0}
.availability-rule-builder .rule-always .rule-control{width:145px}
.availability-rule-builder .rule-summary{margin:12px 0 0;border-top:1px solid #eee;padding-top:8px;font-size:12px;color:#555}
@media(max-width:640px){.availability-rule-builder .rule-heading{align-items:flex-start}}
`).appendTo('head');
    var openRuleSelect=null;
    function closeRuleDropdown(){
        if(!openRuleSelect)return;
        var select=$(openRuleSelect),instance=select.data('chosen');
        if(instance&&typeof instance.close_field==='function')instance.close_field();else select.trigger('chosen:close');
    }
    $(document).on('chosen:showing_dropdown.ruleFloating','.availability-rule-builder select',function(){
        if(openRuleSelect&&openRuleSelect!==this)closeRuleDropdown();
        var select=$(this),container=select.next('.chosen-container'),drop=container.find('.chosen-drop').first(),results=drop.find('.chosen-results');
        if(!container.length||!drop.length)return;
        var rect=container[0].getBoundingClientRect(),viewportWidth=document.documentElement.clientWidth,viewportHeight=document.documentElement.clientHeight;
        if(!rect.width)return;
        openRuleSelect=this;
        select.data('rule-floating-before',{drop:drop.attr('style'),results:results.attr('style')});
        drop.addClass('rule-floating-drop').css({left:Math.max(8,Math.min(rect.left,viewportWidth-rect.width-8))+'px',top:rect.bottom+'px',width:rect.width+'px'});
        var below=viewportHeight-rect.bottom-8,above=rect.top-8,up=below<160&&above>below;
        var chrome=drop.outerHeight()-results.outerHeight();
        results.css('max-height',Math.max(40,Math.min(240,(up?above:below)-chrome-8))+'px');
        drop.css('top',(up?Math.max(8,rect.top-drop.outerHeight()):rect.bottom)+'px');
    });
    $(document).on('chosen:hiding_dropdown.ruleFloating','.availability-rule-builder select',function(){
        var select=$(this),before=select.data('rule-floating-before'),drop=select.next('.chosen-container').find('.chosen-drop').first(),results=drop.find('.chosen-results');
        if(before){
            drop.removeClass('rule-floating-drop');
            if(before.drop===undefined)drop.removeAttr('style');else drop.attr('style',before.drop);
            if(before.results===undefined)results.removeAttr('style');else results.attr('style',before.results);
            select.removeData('rule-floating-before');
        }
        if(openRuleSelect===this)openRuleSelect=null;
    });
    window.addEventListener('scroll',function(event){
        if(!$(event.target).closest('.chosen-drop').length)closeRuleDropdown();
    },true);
    $(window).on('resize.ruleFloating',closeRuleDropdown);
    $(document).on('mousedown.ruleFloating','.rule-remove-condition,.rule-remove',closeRuleDropdown);
    function select(options, cls, value, label){
        var el=$('<select/>',{'class':'form-control input-sm '+cls,'aria-label':label});
        $.each(options,function(_,o){$('<option/>',{value:o[0],text:o[1]}).appendTo(el);});
        return el.val(value);
    }
    function fieldKey(c){return c.source+':'+c.field;}
    function fieldsFor(builder){return builder.data('rule-fields')||[];}
    function setting(builder){
        var rules=[];
        builder.find('.availability-rule').each(function(){
            var row=$(this),conditions=[];
            row.find('.availability-condition').each(function(index){
                var condition=$(this),key=condition.find('.rule-field').val()||'',split=key.indexOf(':');
                conditions.push({source:key.slice(0,split),field:key.slice(split+1),operator:condition.find('.rule-operator').val(),values:condition.find('.rule-values').val()||[],join:index?condition.closest('.rule-condition-block').find('.rule-join').val():'and',visibility:condition.find('.rule-row-visibility').val()});
            });
            var rule={action:row.data('rule-action')||'both',visibility:row.find('.rule-always-visibility').val()||'show',match:'all',conditions:conditions};
            var otherwise=row.find('.rule-else-visibility').val();
            if(conditions.length && otherwise)rule.else_visibility=otherwise;
            rules.push(rule);
        });
        return {version:3,rules:rules};
    }
    function savedSettings(form, result, submitted){
        var child=form.attr('id')==='update-child-form-settings';
        var saved=result.settings|| (child?{child_tables:submitted}:submitted);
        form.find('.availability-rule-builder').each(function(){
            var builder=$(this),row=builder.closest('[data-tab-name],[data-child-table]');
            var group=child?saved.child_tables:(row.attr('data-tab-type')==='child_document'?saved.child_documents:saved.tabs);
            var key=child?row.attr('data-child-table'):row.attr('data-tab-name');
            var value=group&&group[key]?group[key]:{version:3,rules:[]};
            builder.attr('data-setting',JSON.stringify(value)).data('setting',value);
            builder.find('.rule-unsaved').remove();
        });
    }
    function validateRule(rule, showErrors){
        var valid=true;
        rule.find('.availability-condition').each(function(){
            var condition=$(this),field=condition.find('.rule-field'),values=condition.find('.rule-values');
            var missingField=!field.val(),missingValues=$.inArray(condition.find('.rule-operator').val(),['in','not_in'])>-1&&!(values.val()||[]).length;
            var invalid=missingField||missingValues;
            if(invalid)valid=false;
            condition.toggleClass('rule-condition-invalid',showErrors&&invalid);
            field.attr('aria-invalid',showErrors&&missingField?'true':'false');
            values.attr('aria-invalid',showErrors&&!missingField&&missingValues?'true':'false');
        });
        rule.find('.rule-validation').text(showErrors&&!valid?'Complete this rule’s conditions, or remove the conditions to apply it always.':'').toggle(showErrors&&!valid);
        return valid;
    }
    function updateElse(rule){
        var conditions=rule.find('.availability-condition'),holder=rule.find('.rule-else-control');
        holder.insertAfter(rule.find('.rule-conditions')).toggle(conditions.length>0);
        conditions.removeClass('rule-has-else');
        if(conditions.length===1){
            conditions.addClass('rule-has-else');
            holder.insertBefore(conditions.find('.rule-remove-condition'));
        }
        var choices=rule.find('.rule-row-visibility').map(function(){return $(this).val();}).get();
        var same=choices.length>0 && choices.every(function(value){return value===choices[0];});
        var select=holder.find('select'),selected=select.val()||'';
        select.empty();
        $.each([['','Continue to next rule'],['show','Show'],['hide','Hide'],['readonly','Read-only']],function(_,option){
            if(same && option[0]===choices[0])return;
            $('<option/>',{value:option[0],text:option[1]}).appendTo(select);
        });
        select.val(selected);
        if(select.val()===null)select.val('');
        select.trigger('chosen:updated');
    }
    function summary(rule){
        var parts=[],incomplete=false,conditions=rule.find('.availability-condition');
        rule.find('.rule-condition-block').each(function(index){$(this).find('.rule-connector').toggle(index>0);});
        conditions.each(function(index){
            var c=$(this),values=[],operator=c.find('.rule-operator').val();
            c.find('.rule-values option:selected').each(function(){values.push($(this).text());});
            if(!c.find('.rule-field').val()||($.inArray(operator,['in','not_in'])>-1&&!values.length))incomplete=true;
            var join=index?c.closest('.rule-condition-block').find('.rule-join option:selected').text()+' ':'';
            parts.push(join+c.find('.rule-field option:selected').text()+' '+c.find('.rule-operator option:selected').text()+(values.length?' '+values.join(' or '):'')+' → '+c.find('.rule-row-visibility option:selected').text());
        });
        rule.find('.rule-always').toggle(!conditions.length);
        updateElse(rule);
        if(rule.data('validation-attempted'))validateRule(rule,true);
        if(parts.length && rule.find('.rule-else-visibility').val()){
            parts.push('Else → '+rule.find('.rule-else-visibility option:selected').text());
        }
        rule.find('.rule-summary').text(incomplete?'Choose a field and values to complete this rule.':parts.length?parts.join('; '):'Always → '+rule.find('.rule-always-visibility option:selected').text());
    }
    function valuesFor(condition, selected){
        var builder=condition.closest('.availability-rule-builder'),key=condition.find('.rule-field').val(),values=condition.find('.rule-values'),options=[];
        $.each(fieldsFor(builder),function(_,f){if(f.source+':'+f.name===key)options=f.options||[];});
        values.empty();
        $.each(options,function(_,item){var value=typeof item==='object'?String(item.value):String(item),label=typeof item==='object'?item.label:item;$('<option/>',{value:value,text:label,selected:$.inArray(value,selected)>-1}).appendTo(values);});
        var noValues=$.inArray(condition.find('.rule-operator').val(),['empty','not_empty'])>-1;
        if(noValues)values.val([]);
        values.prop('disabled',noValues||!options.length).attr('data-placeholder',noValues?'No value needed':options.length?'Choose values':'No available values').trigger('chosen:updated');
    }
    function addCondition(rule, data){
        data=data||{source:'',field:'',operator:'in',values:[]};
        var fields=[['','Choose what to check']];
        $.each(fieldsFor(rule.closest('.availability-rule-builder')),function(_,f){fields.push([f.source+':'+f.name,f.label]);});
        var block=$('<div class="rule-condition-block"></div>').appendTo(rule.find('.rule-conditions'));
        var connector=$('<div class="rule-connector"></div>').appendTo(block);
        select([['and','AND'],['or','OR']],'rule-join',data.join||rule.data('default-join')||'and','Join with previous condition').appendTo(connector);
        var c=$('<div class="availability-condition"></div>').appendTo(block);
        var fieldHolder=$('<label class="rule-control"><span>What to check</span></label>').appendTo(c);
        select(fields,'rule-field',fieldKey(data),'What to check').appendTo(fieldHolder);
        var operatorHolder=$('<label class="rule-control"><span>Comparison</span></label>').appendTo(c);
        select([['in','is any of'],['not_in','is none of'],['empty','is empty'],['not_empty','is not empty']],'rule-operator',data.operator,'Operator').appendTo(operatorHolder);
        var holder=$('<div class="rule-control"><span>Value(s)</span></div>').appendTo(c);
        $('<select multiple class="form-control input-sm rule-values" aria-label="Condition values"></select>').appendTo(holder);
        var visibility=$('<label class="rule-control"><span>Visibility</span></label>').appendTo(c);
        select([['show','Show'],['hide','Hide'],['readonly','Read-only']],'rule-visibility rule-row-visibility',data.visibility||rule.data('default-visibility')||'show','Condition visibility').appendTo(visibility);
        $('<button type="button" class="btn btn-sm btn-link rule-remove-condition" title="Remove this condition">Remove</button>').appendTo(c);
        valuesFor(c,$.map(data.values||[],String));
        if($.fn.chosen){c.find('select').chosen({width:'100%',disable_search_threshold:10,search_contains:true});connector.find('select').chosen({width:'90px',disable_search:true});}
        summary(rule);
    }
    function addRule(builder, data){
        data=data||{action:'both',visibility:'show',match:'all',conditions:[{source:'',field:'',operator:'in',values:[],visibility:'show'}]};
        var rule=$('<div class="availability-rule"></div>').appendTo(builder.find('.availability-rules'));
        rule.data('default-visibility',data.visibility).data('default-join',data.match==='any'?'or':'and');
        rule.data('rule-action',data.action||'both');
        var top=$('<div class="rule-heading"></div>').appendTo(rule);
        $('<strong class="rule-number"></strong>').appendTo(top);
        var tools=$('<div class="rule-tools"></div>').appendTo(top);
        $('<button type="button" class="btn btn-sm btn-link rule-up" title="Move rule up" aria-label="Move rule up">Move up</button><button type="button" class="btn btn-sm btn-link rule-down" title="Move rule down" aria-label="Move rule down">Move down</button><button type="button" class="btn btn-sm btn-link rule-remove">Remove rule</button>').appendTo(tools);
        var always=$('<div class="rule-always"><span>Always <span class="text-muted">(no conditions)</span></span></div>').appendTo(rule);
        var visibility=$('<label class="rule-control"><span>Visibility</span></label>').appendTo(always);
        select([['show','Show'],['hide','Hide'],['readonly','Read-only']],'rule-visibility rule-always-visibility',data.visibility,'Always visibility').appendTo(visibility);
        $('<div class="rule-conditions"></div>').appendTo(rule);
        var otherwise=$('<label class="rule-control rule-else-control"><span>Else</span></label>').appendTo(rule);
        select([['','Continue to next rule'],['show','Show'],['hide','Hide'],['readonly','Read-only']],
            'rule-else-visibility',data.else_visibility||'','Else visibility').appendTo(otherwise);
        otherwise.attr('title','Applies when this rule’s combined conditions do not match.');

        $.each(data.conditions||[],function(_,c){addCondition(rule,c);});
        $('<button type="button" class="btn btn-sm btn-link rule-add-condition">+ Add condition</button>').appendTo(rule);
        $('<p class="rule-validation text-danger" role="alert" style="display:none;margin:8px 0"></p>').appendTo(rule);
        $('<p class="rule-summary" aria-live="polite"></p>').appendTo(rule);
        if($.fn.chosen)always.add(otherwise).find('select').chosen({width:'100%',disable_search_threshold:10});
        renumber(builder);summary(rule);
    }
    function renumber(builder){builder.find('.availability-rule').each(function(i){var row=$(this);row.find('.rule-number').text('Rule '+(i+1));row.find('.rule-up').prop('disabled',i===0);row.find('.rule-down').prop('disabled',i===builder.find('.availability-rule').length-1);});}
    function actionConditions(rules){
        var converted=[];
        $.each(rules,function(_,rule){
            if(rule.else_visibility || $.inArray(rule.action,['add','edit'])===-1){converted.push(rule);return;}
            var page={source:'page',field:'action',operator:'in',values:[rule.action]},conditions=rule.conditions||[];
            // Preserve (Add/Edit) AND (A OR B) as adjacent rules with the same result.
            // Simply appending Action to an ANY group would incorrectly broaden access.
            if(rule.match==='any' && conditions.length>1){
                $.each(conditions,function(_,condition){converted.push({action:'both',visibility:rule.visibility,match:'all',conditions:[page,condition]});});
            }else converted.push({action:'both',visibility:rule.visibility,match:'all',conditions:[page].concat(conditions)});
        });
        return converted;
    }
    function legacyRules(s){
        if($.isArray(s.rules))return actionConditions(s.rules);
        var rules=[],mode=s.action_visibility||'always',hasCondition=s.visibility_field&&(s.visible_when||[]).length;
        function r(action,visibility,conditions){return {action:action,visibility:visibility,match:'all',conditions:conditions||[]};}
        if(mode.indexOf('hide_')===0)rules.push(r(mode==='hide_both'?'both':mode.slice(5),'hide'));
        if(hasCondition)rules.push(r('both','hide',[{source:'record',field:s.visibility_field,operator:'not_in',values:s.visible_when}]));
        if(mode.indexOf('readonly_')===0)rules.push(r(mode==='readonly_both'?'both':mode.slice(9),'readonly'));
        return actionConditions(rules);
    }
    var moduleGrants={}, pendingUnlocks={}, rebuildBusy=false;
    function flowStatus(form, text, failed, retry){
        var box=form.find('.rule-save-status');
        if(!box.length)box=$('<div class="rule-save-status" role="status" aria-live="polite" style="margin:12px 0"></div>').appendTo(form);
        box.empty().toggleClass('text-danger',!!failed).toggleClass('text-success',!failed).append($('<span/>').text(text));
        if(retry)$('<button type="button" class="btn btn-sm btn-default" style="margin-left:8px">Retry rebuild</button>').appendTo(box).on('click',function(){runRebuild(form);});
    }
    function unlockUrl(form){return form.find('.availability-rule-builder').first().attr('data-unlock-url')||'';}
    function flowError(xhr, fallback){return xhr.responseJSON&&xhr.responseJSON.message?xhr.responseJSON.message:fallback;}
    function passwordDialog(form,url,deferred){
        var overlay=$('<div class="rule-password-overlay" style="position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:20000;display:flex;align-items:center;justify-content:center;padding:20px"></div>').appendTo('body');
        var dialog=$('<div role="dialog" aria-modal="true" aria-label="Unlock module" style="background:#fff;border-radius:4px;padding:22px;width:420px;max-width:100%"></div>').appendTo(overlay);
        $('<h4 style="margin-top:0">Unlock this module</h4><p>Enter the parent form password. It unlocks this parent and its child forms for 30 minutes.</p>').appendTo(dialog);
        var password=$('<input type="password" class="form-control" aria-label="Parent form password" autocomplete="current-password">').appendTo(dialog);
        var error=$('<p class="text-danger" role="alert" style="margin:8px 0"></p>').appendTo(dialog);
        var cancel=$('<button type="button" class="btn btn-default">Cancel</button>').appendTo(dialog);
        var unlock=$('<button type="button" class="btn btn-success" style="margin-left:8px">Unlock module</button>').appendTo(dialog);
        var previousFocus=document.activeElement;
        function close(){password.val('');overlay.remove();if(previousFocus&&document.contains(previousFocus))previousFocus.focus();}
        function submit(){
            if(!password.val()){error.text('Enter the parent form password.');password.trigger('focus');return;}
            unlock.prop('disabled',true);cancel.prop('disabled',true);error.empty();
            var entered=password.val();password.val('');
            $.ajax({url:url,type:'POST',dataType:'json',data:{'data[CustomTable][password]':entered},timeout:30000})
            .done(function(result){if(result.success){moduleGrants[url]=result;close();deferred.resolve(result);}else{error.text(result.message||'Unable to unlock this module.');}})
            .fail(function(xhr){error.text(flowError(xhr,'Could not unlock the module. Check your session and try again.'));})
            .always(function(){entered=null;unlock.prop('disabled',false);cancel.prop('disabled',false);password.trigger('focus');});
        }
        cancel.on('click',function(){close();deferred.reject('cancelled');});unlock.on('click',submit);
        dialog.on('keydown',function(event){
            if(event.key==='Escape'&&!unlock.prop('disabled')){event.preventDefault();cancel.trigger('click');}
            if(event.key==='Enter'&&event.target===password[0]){event.preventDefault();if(!unlock.prop('disabled'))submit();}
            if(event.key==='Tab'){
                var inputs=dialog.find('input:enabled,button:enabled'),first=inputs.first()[0],last=inputs.last()[0];
                if(event.shiftKey&&document.activeElement===first){event.preventDefault();last.focus();}
                else if(!event.shiftKey&&document.activeElement===last){event.preventDefault();first.focus();}
            }
        });
        password.trigger('focus');
    }
    function ensureUnlocked(form,force){
        var url=unlockUrl(form),deferred=$.Deferred();
        if(!url)return deferred.resolve({}).promise();
        if(force)delete moduleGrants[url];
        if(moduleGrants[url]&&moduleGrants[url].expires*1000>Date.now()+5000)return deferred.resolve(moduleGrants[url]).promise();
        if(pendingUnlocks[url])return pendingUnlocks[url];
        pendingUnlocks[url]=deferred.promise();
        deferred.always(function(){delete pendingUnlocks[url];});
        $.ajax({url:url,type:'POST',dataType:'json',data:{},timeout:30000})
        .done(function(result){if(result.success){moduleGrants[url]=result;deferred.resolve(result);}else if(result.password_required){passwordDialog(form,url,deferred);}else{flowStatus(form,result.message||'Could not unlock module.',true);deferred.reject();}})
        .fail(function(xhr){if(xhr.status===403&&xhr.responseJSON&&xhr.responseJSON.password_required)passwordDialog(form,url,deferred);else{flowStatus(form,flowError(xhr,'Could not check module access. Refresh the page and try again.'),true);deferred.reject();}});
        return deferred.promise();
    }
    function setBusy(form,busy){
        if(busy){form.find(':input:enabled').attr('data-rule-flow-disabled','1').prop('disabled',true).trigger('chosen:updated');}
        else form.find('[data-rule-flow-disabled="1"]').removeAttr('data-rule-flow-disabled').prop('disabled',false).trigger('chosen:updated');
        form.attr('aria-busy',busy?'true':'false');
    }
    function rebuildRequest(form,grant){
        flowStatus(form,'Rules saved. Rebuilding module… Keep this page open.');
        $.ajax({url:grant.rebuild_url,type:'POST',dataType:'json',data:{},timeout:360000})
        .done(function(result){if(result.success)flowStatus(form,'Ready. Rules have been applied. Refresh the Add or Edit page to see them.');else flowStatus(form,'Rules are saved. '+(result.message||'The rebuild failed.'),true,true);})
        .fail(function(xhr){delete moduleGrants[unlockUrl(form)];flowStatus(form,'Rules are saved. '+flowError(xhr,'The rebuild did not complete. Retry the rebuild.'),true,true);})
        .always(function(){setBusy(form,false);rebuildBusy=false;});
    }
    function runRebuild(form){
        if(rebuildBusy){flowStatus(form,'A module rebuild is already running. Please wait.');return;}
        ensureUnlocked(form).done(function(grant){rebuildBusy=true;setBusy(form,true);rebuildRequest(form,grant);});
    }
    function saveAndRebuild(form,postData,submitted){
        if(rebuildBusy){flowStatus(form,'A module rebuild is already running. Please wait.');return;}
        ensureUnlocked(form).done(function(grant){
            rebuildBusy=true;setBusy(form,true);flowStatus(form,'Saving rules…');
            function save(attempt){
                $.ajax({url:form.attr('action'),type:'POST',dataType:'json',data:postData,timeout:60000})
                .done(function(result){
                    if(result.success){savedSettings(form,result,submitted);rebuildRequest(form,grant);}
                    else{flowStatus(form,result.message||'Rules could not be saved. Your changes remain on this page.',true);setBusy(form,false);rebuildBusy=false;}
                })
                .fail(function(xhr){
                    if(xhr.status===403&&attempt===0){
                        ensureUnlocked(form,true).done(function(refreshed){grant=refreshed;save(1);}).fail(function(){setBusy(form,false);rebuildBusy=false;flowStatus(form,'Rules were not saved. Unlock the module and try again.',true);});
                    }else{flowStatus(form,flowError(xhr,'Rules could not be saved. Your changes remain on this page.'),true);setBusy(form,false);rebuildBusy=false;}
                });
            }
            save(0);
        });
    }
    function init(builder){
        if(builder.data('rules-ready'))return;
        builder.data('rules-ready',true);
        var data={},fields=[];try{data=JSON.parse(builder.attr('data-setting')||'{}');fields=JSON.parse(builder.attr('data-fields')||'[]');}catch(e){}
        builder.data('rule-fields',fields);
        $.each(legacyRules(data),function(_,r){addRule(builder,r);});
        builder.on('click','.availability-add-rule',function(){var form=builder.closest('form');if(rebuildBusy){flowStatus(form,'A module rebuild is already running. Please wait.');return;}ensureUnlocked(form).done(function(){addRule(builder);});});
        builder.on('click','.rule-add-condition',function(){addCondition($(this).closest('.availability-rule'));});
        builder.on('click','.rule-remove-condition',function(){
            var rule=$(this).closest('.availability-rule'),condition=$(this).closest('.availability-condition');
            var visibility=condition.find('.rule-row-visibility').val();
            rule.find('.rule-else-control').insertAfter(rule.find('.rule-conditions'));
            condition.closest('.rule-condition-block').remove();
            if(!rule.find('.availability-condition').length)rule.find('.rule-always-visibility').val(visibility);
            builder.find('.availability-error').empty();
            summary(rule);
        });
        builder.on('click','.rule-remove',function(){$(this).closest('.availability-rule').remove();builder.find('.availability-error').empty();renumber(builder);var hint=builder.find('.rule-unsaved');if(!hint.length)hint=$('<p class="rule-unsaved text-muted" role="status" style="margin:8px 0"></p>').appendTo(builder);hint.text((builder.find('.availability-rule').length?'Rule removed.':'No rules remain. This section will be visible by default.')+' Click Save Rules & Apply to save this change.');});
        builder.on('click','.rule-up,.rule-down',function(){var r=$(this).closest('.availability-rule');if($(this).hasClass('rule-up'))r.prev('.availability-rule').before(r);else r.next('.availability-rule').after(r);renumber(builder);});
        builder.on('change','.rule-field',function(){valuesFor($(this).closest('.availability-condition'),[]);});
        builder.on('change','.rule-operator',function(){var c=$(this).closest('.availability-condition');valuesFor(c,c.find('.rule-values').val()||[]);});
        builder.on('change','select',function(){var rule=$(this).closest('.availability-rule');if($(this).hasClass('rule-always-visibility'))rule.data('default-visibility',$(this).val());summary(rule);builder.find('.availability-error').empty();});
    }
    window.FlinkisoAvailabilityRules={read:setting,init:init};
    window.FlinkisoAvailabilityRules.submit=function(event){
        var form=$(this),valid=true,child=form.attr('id')==='update-child-form-settings',settings=child?{}:{tabs:{},child_documents:{}};
        form.find('.availability-error').empty();
        form.find('.availability-rule-builder').each(function(){
            var builder=$(this),row=builder.closest('[data-tab-name],[data-child-table]'),s=setting(builder);
            builder.find('.availability-rule').each(function(){var rule=$(this);rule.data('validation-attempted',true);if(!validateRule(rule,true))valid=false;});
            if(child)settings[row.attr('data-child-table')]=s;
            else settings[row.attr('data-tab-type')==='child_document'?'child_documents':'tabs'][row.attr('data-tab-name')]=s;
        });
        if(!valid){event.preventDefault();event.stopImmediatePropagation();return false;}
        form.find(child?'#ChildFormAvailabilitySettings':'#CustomTableTabSettings').val(JSON.stringify(settings));
        if(unlockUrl(form)){event.preventDefault();event.stopImmediatePropagation();saveAndRebuild(form,form.serialize(),settings);return false;}
    };
}
$('#update-tab-settings,#update-child-form-settings').off('submit.availabilityRules').on('submit.availabilityRules',window.FlinkisoAvailabilityRules.submit);
$('.availability-rule-builder').each(function(){window.FlinkisoAvailabilityRules.init($(this));});
})();
</script>
