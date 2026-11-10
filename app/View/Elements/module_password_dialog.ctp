<script>
(function($){
    if (window.FlinkisoModulePasswordDialog) return;
    window.FlinkisoModulePasswordDialog = function(url){
        var deferred = $.Deferred();
        var overlay=$('<div class="rule-password-overlay" style="position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:20000;display:flex;align-items:center;justify-content:center;padding:20px"></div>').appendTo('body');
        var dialog=$('<div role="dialog" aria-modal="true" aria-label="Unlock module" style="background:#fff;border-radius:4px;padding:22px;width:420px;max-width:100%"></div>').appendTo(overlay);
        $('<h4 style="margin-top:0">Unlock this module</h4><p>Enter the parent form password. The module stays unlocked for 30 minutes.</p>').appendTo(dialog);
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
            .done(function(result){if(result.success){close();deferred.resolve(result);}else{error.text(result.message||'Unable to unlock this module.');}})
            .fail(function(xhr){error.text(xhr.responseJSON && xhr.responseJSON.message || 'Could not unlock the module. Check your session and try again.');})
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
        return deferred.promise();
    };
})(jQuery);
</script>
