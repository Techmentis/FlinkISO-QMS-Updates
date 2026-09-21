(function (window, $) {
  'use strict';

  if (!$) { return; }

  var $panel;
  var $backdrop;
  var $toggle;
  var $prompt;
  var $send;
  var $cancel;
  var requestInProgress = false;
  var activeAiRequest = null;
  var activeAiId = '';
  var guidedAction = null;
  var fieldContext = {};
  var tabContext = [];
  var historyLoaded = false;
  var historyLoading = false;
  var historyHasMore = true;
  var historyBefore = '';
  var instanceBusy = false;
  var instanceRequestId = '';
  var instanceStatusLoading = false;
  var instanceStatusTimer = null;
  var instanceStatusGeneration = 0;
  var instanceStatusReady = false;

  function openPanel() {
    if (!$panel || !$panel.length) { return; }
    $panel.addClass('is-open').attr('aria-hidden', 'false');
    $('body').addClass('fi-ai-panel-open');
    $backdrop.addClass('is-open').attr('aria-hidden', 'false');
    $toggle.attr('aria-expanded', 'true').parent().addClass('is-active');
    $toggle.parent().removeClass('has-ai-completion');
    $toggle.attr('title', 'FlinkISO AI');
    if (!historyLoaded) { loadHistory(false); }
    refreshInstanceStatus();
    activateFieldSelection();
    setPageFormSubmissionLocked(true);
    window.setTimeout(function () { $(window).trigger('resize'); }, 260);
    window.setTimeout(function () { $prompt.focus(); }, 260);
  }

  function closePanel() {
    if (!$panel || !$panel.length) { return; }
    $panel.removeClass('is-open is-wide').attr('aria-hidden', 'true');
    $('body').removeClass('fi-ai-panel-open fi-ai-panel-wide');
    $backdrop.removeClass('is-open').attr('aria-hidden', 'true');
    $toggle.attr('aria-expanded', 'false').parent().removeClass('is-active');
    $('#fi-ai-expand').attr('title', 'Expand panel').find('.fa').removeClass('fa-compress').addClass('fa-expand');
    $('.fi-ai-selectable-field, .fi-ai-selected-field').removeClass('fi-ai-selectable-field fi-ai-selected-field').removeAttr('data-fi-ai-field');
    setPageFormSubmissionLocked(false);
    if (!instanceBusy && !requestInProgress) { stopInstanceStatusPolling(); }
    window.setTimeout(function () { $(window).trigger('resize'); }, 260);
  }

  function togglePanel() {
    if ($panel.hasClass('is-open')) { closePanel(); } else { openPanel(); }
  }

  function resizePrompt() {
    $prompt.css('height', 'auto');
    $prompt.css('height', Math.min($prompt[0].scrollHeight, 150) + 'px');
    $send.prop('disabled', !instanceStatusReady || requestInProgress || instanceBusy || $.trim($prompt.val()).length === 0);
  }

  function appendMessage(kind, message) {
    var $item = $('<div/>', {'class': 'fi-ai-message fi-ai-message-' + kind});
    var $content = $('<div/>', {'class': 'fi-ai-message-content'}).append($('<p/>').text(message));
    $item.append($content);
    $('#fi-ai-messages').append($item).scrollTop($('#fi-ai-messages')[0].scrollHeight);
    return $item;
  }

  function historyItem(item) {
    var $fragment = $();
    var $user = $('<div/>', {'class': 'fi-ai-message fi-ai-message-user fi-ai-history-item'})
      .append($('<div/>', {'class': 'fi-ai-message-content'})
        .append($('<p/>').text(item.request || ''))
        .append($('<div/>', {'class': 'fi-ai-history-meta'}).text((item.user_name || 'User') + ' · ' + (item.created || ''))));
    var responseText = item.response_message || (item.status === 'processing' ? 'AI request is still processing.' : 'No response was recorded.');
    var meta = [item.status || '', item.operation || ''];
    if (item.duration_ms) { meta.push((item.duration_ms / 1000).toFixed(1) + 's'); }
    var $assistantContent = $('<div/>', {'class': 'fi-ai-message-content'})
      .append($('<p/>').text(responseText))
      .append($('<div/>', {'class': 'fi-ai-history-meta'}).text(meta.join(' · ')));
    if (item.form_url) {
      $assistantContent.append($('<a/>', {'class': 'fi-ai-form-link', href: item.form_url}).text('Open generated form'));
    }
    if (item.can_cancel) {
      $assistantContent.append($('<button/>', {
        type: 'button',
        'class': 'fi-ai-history-cancel',
        'data-ai-id': item.id
      }).append('<i class="fa fa-stop" aria-hidden="true"></i> Stop'));
    }
    if (item.clarification_question) {
      renderClarification($assistantContent, item.clarification_question);
    }
    var $assistant = $('<div/>', {'class': 'fi-ai-message fi-ai-message-assistant fi-ai-history-item'})
      .append($('<div/>', {'class': 'fi-ai-message-icon', 'aria-hidden': 'true'}).append('<i class="fa fa-magic"></i>'))
      .append($assistantContent);
    return $fragment.add($user).add($assistant);
  }

  function loadHistory(older) {
    if (historyLoading || (older && !historyHasMore)) { return; }
    var url = $panel.attr('data-history-url');
    if (!url) { historyLoaded = true; return; }
    historyLoading = true;
    var $messages = $('#fi-ai-messages');
    var oldHeight = $messages[0].scrollHeight;
    $.ajax({
      url: url,
      type: 'GET',
      dataType: 'json',
      data: {
        source_controller: $panel.attr('data-controller'),
        custom_table_id: $panel.attr('data-custom-table-id'),
        qc_document_id: $panel.attr('data-qc-document-id'),
        record_id: $panel.attr('data-record-id'),
        before: older ? historyBefore : ''
      }
    }).done(function (result) {
      if (!result || result.success !== true) { return; }
      var items = $.isArray(result.items) ? result.items : [];
      var $anchor = older ? $messages.children('.fi-ai-history-item').first() : $('#fi-ai-welcome');
      $.each(items, function (_, item) { historyItem(item).insertBefore($anchor); });
      historyBefore = result.next_before || historyBefore;
      historyHasMore = result.has_more === true;
      historyLoaded = true;
      if (older) {
        $messages.scrollTop($messages[0].scrollHeight - oldHeight);
      } else if (items.length) {
        $messages.scrollTop($messages[0].scrollHeight);
      }
    }).always(function () {
      historyLoading = false;
      historyLoaded = true;
    });
  }

  function setLoading(isLoading) {
    requestInProgress = isLoading;
    $prompt.prop('disabled', !instanceStatusReady || isLoading || instanceBusy);
    $('#fi-ai-send-current-document').prop('disabled', !instanceStatusReady || isLoading || instanceBusy);
    $cancel.toggleClass('is-visible', isLoading).attr('aria-hidden', isLoading ? 'false' : 'true');
    $send.find('.fa')
      .toggleClass('fa-arrow-up', !isLoading)
      .toggleClass('fa-spinner fa-spin', isLoading);
    resizePrompt();
  }

  function cancelAiRequest(id, $button) {
    if (!id || !$panel.attr('data-cancel-url')) { return; }
    stopInstanceStatusPolling();
    if ($button && $button.length) { $button.prop('disabled', true).text('Stopping…'); }
    $.ajax({
      url: $panel.attr('data-cancel-url'),
      type: 'POST',
      dataType: 'json',
      data: {id: id}
    }).done(function (result) {
      instanceBusy = true;
      instanceRequestId = id;
      if ($button && $button.length) {
        var $content = $button.closest('.fi-ai-message-content');
        $content.children('p').text(result && result.message ? result.message : 'Stop requested.');
        $content.find('.fi-ai-history-meta').text('cancelled');
        if ($button.is('#fi-ai-instance-stop')) {
          $button.hide().prop('disabled', false).html('<i class="fa fa-stop" aria-hidden="true"></i> Stop');
        } else {
          $button.remove();
        }
      }
      $('#fi-ai-instance-busy').show()
        .find('.fi-ai-message-content > strong').first().text('Stopping AI task');
      $('#fi-ai-instance-busy').find('.fi-ai-message-content > p').first()
        .text('FlinkISO AI is cancelling the current request. A new request can be submitted after it has stopped.');
      $('#fi-ai-instance-stop').hide().attr('data-ai-id', '');
      $prompt.prop('disabled', true);
      $('#fi-ai-send-current-document').prop('disabled', true);
      window.setTimeout(refreshInstanceStatus, 500);
    }).fail(function (xhr) {
      if ($button && $button.length) { $button.prop('disabled', false).text('Stop'); }
      appendMessage('system', responseError(xhr));
    });
  }

  function applyInstanceStatus(result) {
    var wasBusy = instanceBusy;
    instanceStatusReady = !!(result && result.success === true);
    instanceBusy = !!(result && result.success === true && result.active === true);
    var $busy = $('#fi-ai-instance-busy');
    var $stop = $('#fi-ai-instance-stop');
    if (instanceBusy) {
      instanceRequestId = result.request_id || '';
      var isCancelling = result.cancelling === true;
      $('#fi-ai-welcome, .fi-ai-suggestions').hide();
      // The status poll can see the newly-created history row before the
      // streaming response delivers its request_id. During that short window,
      // this panel's live activity card is already the correct UI; do not also
      // show the instance-wide busy alert for the same submission.
      var isCurrentTabRequest = requestInProgress && (!activeAiId || !result.request_id || result.request_id === activeAiId);
      $busy.toggle(!isCurrentTabRequest);
      $busy.find('.fi-ai-message-content > strong').first().text(isCancelling ? 'Stopping AI task' : 'AI task in progress');
      $busy.find('.fi-ai-message-content > p').first().text(isCancelling
        ? 'FlinkISO AI is cancelling the current request. A new request can be submitted after it has stopped.'
        : 'FlinkISO AI is currently processing a request for this instance.');
      var $meta = $('#fi-ai-instance-busy-meta').empty();
      $meta.append(document.createTextNode('Started by ' + (result.user_name || 'User')));
      if (result.source) $meta.append('<br>').append($('<strong/>').text(result.source));
      if (result.started) $meta.append('<br>').append(document.createTextNode(result.started));
      if (result.prompt) {
        $meta.append(
          $('<div/>', {'class': 'fi-ai-instance-prompt'})
            .append($('<span/>').text('Prompt'))
            .append($('<p/>').text(result.prompt))
        );
      }
      $stop.toggle(!isCancelling && !isCurrentTabRequest && result.can_cancel === true).attr('data-ai-id', result.request_id || '');
      if (!isCurrentTabRequest) { scheduleInstanceStatusPolling(); }
    } else {
      stopInstanceStatusPolling();
      $busy.hide();
      $stop.hide().attr('data-ai-id', '');
      $('#fi-ai-welcome, .fi-ai-suggestions').show();
      // When this tab owns the request, API V2 can release its lock a moment
      // before the successful response finishes rendering. Let the request's
      // own completion path report the result instead of showing a false
      // instance-level failure during that hand-off.
      if (wasBusy && !requestInProgress) {
        var finishedSameRequest = !result.last_request_id || !instanceRequestId || result.last_request_id === instanceRequestId;
        var finalStatus = finishedSameRequest ? (result.last_status || 'completed') : 'completed';
        var completionMessage = finalStatus === 'cancelled'
          ? 'The AI task was cancelled. FlinkISO AI is ready for another request.'
          : (finalStatus === 'failed'
            ? 'The AI task finished with an error. Open the AI panel to review it.'
            : 'The AI task is complete. FlinkISO AI is ready for another request.');
        appendMessage('system', completionMessage);
        if (!$panel.hasClass('is-open') && finalStatus !== 'cancelled') {
          $toggle.parent().addClass('has-ai-completion');
          $toggle.attr('title', finalStatus === 'failed' ? 'AI task needs attention' : 'AI task complete');
        }
        instanceRequestId = '';
      } else if (wasBusy) {
        instanceRequestId = '';
      }
    }
    $prompt.prop('disabled', !instanceStatusReady || requestInProgress || instanceBusy);
    $('#fi-ai-send-current-document').prop('disabled', !instanceStatusReady || requestInProgress || instanceBusy);
    resizePrompt();
  }

  function stopInstanceStatusPolling() {
    if (!instanceStatusTimer) { return; }
    window.clearTimeout(instanceStatusTimer);
    instanceStatusTimer = null;
  }

  function scheduleInstanceStatusPolling(delay) {
    stopInstanceStatusPolling();
    if (!instanceBusy || requestInProgress) { return; }
    instanceStatusTimer = window.setTimeout(function () {
      instanceStatusTimer = null;
      refreshInstanceStatus();
    }, delay || 8000);
  }

  function refreshInstanceStatus() {
    var url = $panel && $panel.attr('data-status-url');
    if (!url || instanceStatusLoading) { return; }
    var generation = instanceStatusGeneration;
    instanceStatusLoading = true;
    $.ajax({
      url: url,
      type: 'GET',
      dataType: 'json',
      cache: false,
      data: {request_id: instanceRequestId || activeAiId || ''}
    })
      .done(function (result) {
        if (generation === instanceStatusGeneration) applyInstanceStatus(result);
      })
      .always(function () {
        instanceStatusLoading = false;
        if (generation !== instanceStatusGeneration && !requestInProgress) refreshInstanceStatus();
      });
  }

  function startActivity(isDocumentRequest) {
    var startedAt = new Date().getTime();
    var $item = appendMessage('system', isDocumentRequest
      ? 'Reading the current document from FlinkISO…'
      : 'Reading the current form definition…');
    var $text = $item.find('p');
    var $live = $('<pre/>', {'class': 'fi-ai-live-output', 'aria-label': 'Live AI output'}).hide();
    $item.find('.fi-ai-message-content').append($live);

    function update() {
      var elapsedSeconds = Math.max(0, Math.floor((new Date().getTime() - startedAt) / 1000));
      var elapsedText = elapsedSeconds < 60
        ? elapsedSeconds + 's'
        : Math.floor(elapsedSeconds / 60) + 'm ' + (elapsedSeconds % 60) + 's';
      var message;
      if (isDocumentRequest && elapsedSeconds < 5) {
        message = 'Reading and extracting the current document…';
      } else if (elapsedSeconds < 15) {
        message = isDocumentRequest ? 'Sending document content to FlinkISO AI…' : 'Sending the field request to FlinkISO AI…';
      } else if (elapsedSeconds < 45) {
        message = isDocumentRequest ? 'FlinkISO AI is analyzing the document structure…' : 'FlinkISO AI is interpreting the requested field change…';
      } else {
        message = isDocumentRequest ? 'FlinkISO AI is generating the form fields…' : 'FlinkISO AI is preparing the field operation…';
      }
      $text.text(message + ' (' + elapsedText + ')');
    }

    update();
    var timer = window.setInterval(update, 1000);
    return {
      complete: function (message) {
        window.clearInterval(timer);
        $text.text(message || 'AI analysis complete. Validating the result…');
      },
      fail: function (message) {
        window.clearInterval(timer);
        $text.text(message);
      },
      stream: function (chunk) {
        if (!chunk) { return; }
        $live.show();
        if (chunk.message && typeof chunk.message.content === 'string') {
          $live.text($live.text() + chunk.message.content);
        }
        if (chunk.error) {
          var serviceError = typeof chunk.error === 'string'
            ? chunk.error
            : (chunk.error.message || JSON.stringify(chunk.error));
          $live.text($live.text() + '\n[AI service error] ' + serviceError);
        }
        if (chunk.done) {
          var metrics = {
            done_reason: chunk.done_reason,
            total_duration: chunk.total_duration,
            load_duration: chunk.load_duration,
            prompt_eval_count: chunk.prompt_eval_count,
            prompt_eval_duration: chunk.prompt_eval_duration,
            eval_count: chunk.eval_count,
            eval_duration: chunk.eval_duration
          };
          $live.text($live.text() + '\n\n[AI metrics]\n' + JSON.stringify(metrics, null, 2));
        }
        $live.scrollTop($live[0].scrollHeight);
        $('#fi-ai-messages').scrollTop($('#fi-ai-messages')[0].scrollHeight);
      }
    };
  }

  function renderPreview(response) {
    var $item = appendMessage('assistant', response.message || 'A FlinkISO field change was prepared.');
    var $content = $item.find('.fi-ai-message-content');
    var proposedFields = $.isArray(response.proposed_fields) ? response.proposed_fields : [];
    var fields = proposedFields.length
      ? proposedFields
      : ($.inArray(response.operation, ['create_form', 'add_fields']) !== -1 && $.isArray(response.field_details) ? response.field_details : []);
    var warnings = $.isArray(response.warnings) ? response.warnings : [];
    var childTables = $.isArray(response.child_tables) ? response.child_tables : [];

    if (response.form_name) {
      $content.prepend($('<strong/>').text(response.form_name));
    }

    if (fields.length) {
      var fieldWord = fields.length === 1 ? 'field' : 'fields';
      $content.append($('<div/>', {'class': 'fi-ai-result-meta'}).text(fields.length + ' ' + fieldWord + ' prepared for FlinkISO.'));
    }

    if (childTables.length) {
      var $children = $('<div/>', {'class': 'fi-ai-child-table-preview'});
      $children.append($('<strong/>').text(childTables.length === 1 ? 'Detected child table' : 'Detected child tables'));
      var $childList = $('<ul/>');
      $.each(childTables, function (_, table) {
        var count = table && $.isArray(table.fields) ? table.fields.length : 0;
        $childList.append($('<li/>').text((table.name || 'Child table') + ' · ' + count + (count === 1 ? ' field' : ' fields')));
      });
      $children.append($childList);
      if (response.requires_child_confirmation && response.apply_url && response.apply_token) {
        var submitConfirmedBuild = function (parentOnly, $button) {
          $children.find('button').prop('disabled', true);
          $button.text('Creating…');
          var $status = appendMessage('system', parentOnly
            ? 'Creating the parent form without the detected child tables…'
            : 'Creating the parent form and confirmed child tables…');
          $.ajax({
            url: response.apply_url,
            type: 'POST',
            dataType: 'json',
            timeout: 930000,
            data: {token: response.apply_token, confirm_child_tables: parentOnly ? 0 : 1, parent_only: parentOnly ? 1 : 0}
          }).done(function (result) {
            $status.find('p').text(result && result.message ? result.message : 'Form structure created successfully.');
            if (result && result.form_url) {
              $status.find('.fi-ai-message-content').append($('<a/>', {'class': 'fi-ai-form-link', href: result.form_url}).text('Open generated form'));
            }
            if (result && result.design_url) {
              $status.find('.fi-ai-message-content').append($('<a/>', {'class': 'fi-ai-form-link fi-ai-design-link', href: result.design_url}).text('Go to Form Builder'));
            }
          }).fail(function (xhr) {
            $status.find('p').text(responseError(xhr));
            $children.find('button').prop('disabled', false);
            $button.text(parentOnly ? 'Create parent only' : 'Create parent and child tables');
          });
        };
        var $actions = $('<div/>', {'class': 'fi-ai-child-table-actions'});
        var $confirm = $('<button/>', {type: 'button', 'class': 'fi-ai-clarification-choice is-primary', text: 'Create parent and child tables'});
        var $parentOnly = $('<button/>', {type: 'button', 'class': 'fi-ai-clarification-choice', text: 'Create parent only'});
        $confirm.on('click', function () { submitConfirmedBuild(false, $confirm); });
        $parentOnly.on('click', function () { submitConfirmedBuild(true, $parentOnly); });
        $actions.append($confirm).append($parentOnly);
        $children.append($actions);
      }
      $content.append($children);
    }

    if (response.insert_after) {
      $content.append($('<div/>', {'class': 'fi-ai-result-meta'}).text('Insert after: ' + response.insert_after));
    }

    if (response.target_field && $.isArray(response.options_to_add) && response.options_to_add.length) {
      $content.append($('<div/>', {'class': 'fi-ai-result-meta'}).text('Add to ' + response.target_field + ': ' + response.options_to_add.join(', ')));
    }

    if (response.target_field && response.new_field_label) {
      $content.append($('<div/>', {'class': 'fi-ai-result-meta'}).text('Rename label for ' + response.target_field + ': ' + response.new_field_label));
    }

    if ($.isArray(response.fields_to_remove) && response.fields_to_remove.length) {
      $content.append($('<div/>', {'class': 'fi-ai-result-meta'}).text('Remove field: ' + response.fields_to_remove.join(', ')));
    }

    if (response.clarification_question) {
      renderClarification($content, response.clarification_question);
    }

    if (warnings.length) {
      var $warnings = $('<div/>', {'class': 'fi-ai-warnings'}).append($('<strong/>').text('Review notes'));
      var $list = $('<ul/>');
      $.each(warnings, function (_, warning) { $list.append($('<li/>').text(warning)); });
      $warnings.append($list);
      $content.append($warnings);
    }

    var seconds = response.duration_ms ? (response.duration_ms / 1000).toFixed(1) + 's' : '';
    var status = response.read_only ? 'Read-only preview' : 'Preview';
    $content.append($('<div/>', {'class': 'fi-ai-result-meta'}).text(status + (seconds ? ' · ' + seconds : '')));
    $('#fi-ai-messages').scrollTop($('#fi-ai-messages')[0].scrollHeight);
  }

  function submitClarifiedPrompt(message) {
    message = $.trim(message || '');
    if (!message) { return; }
    $prompt.val(message);
    resizePrompt();
    window.setTimeout(function () { $('#fi-ai-form').trigger('submit'); }, 0);
  }

  function submitGuidedAction(promptText, action, fieldName, value, options) {
    if (requestInProgress) { return; }
    guidedAction = {action: action, field: fieldName, value: value || '', options: options || ''};
    $('#fi-ai-field-actions button, #fi-ai-field-actions input, #fi-ai-field-actions select').prop('disabled', true);
    submitClarifiedPrompt(promptText);
  }

  function activateFieldSelection() {
    if (!$panel || $panel.attr('data-controller').indexOf('tbl_') !== 0 && $panel.attr('data-controller').indexOf('chd_') !== 0) { return; }
    $('.content-wrapper form :input[name]').each(function () {
      var match = String($(this).attr('name') || '').match(/^data\[[^\]]+\]\[([^\]]+)\]/);
      if (!match || !fieldContext[match[1]]) { return; }
      var $wrapper = $(this).closest('.input');
      if (!$wrapper.length) { $wrapper = $(this).parent(); }
      $wrapper.addClass('fi-ai-selectable-field').attr('data-fi-ai-field', match[1]);
    });
  }

  function setPageFormSubmissionLocked(locked) {
    var $controls = $('.content-wrapper form').find('button[type="submit"], button:not([type]), input[type="submit"], input[type="image"]');
    if (locked) {
      $controls.each(function () {
        var $control = $(this);
        if ($control.hasClass('fi-ai-submit-locked')) { return; }
        $control.data('fi-ai-was-disabled', $control.prop('disabled') ? 1 : 0);
        $control.data('fi-ai-old-title', $control.attr('title') || '');
        $control.prop('disabled', true)
          .attr('title', 'Close FlinkISO AI before submitting this form.')
          .addClass('fi-ai-submit-locked');
      });
    } else {
      $('.fi-ai-submit-locked').each(function () {
        var $control = $(this);
        $control.prop('disabled', $control.data('fi-ai-was-disabled') === 1);
        var oldTitle = $control.data('fi-ai-old-title');
        if (oldTitle) $control.attr('title', oldTitle); else $control.removeAttr('title');
        $control.removeClass('fi-ai-submit-locked').removeData('fi-ai-was-disabled').removeData('fi-ai-old-title');
      });
    }
  }

  function actionButton(label, handler, danger) {
    return $('<button/>', {type: 'button', 'class': 'fi-ai-field-action' + (danger ? ' is-danger' : '')})
      .text(label).on('click', handler);
  }

  function fieldActionInput($card, placeholder, buttonLabel, callback) {
    var $editor = $card.find('.fi-ai-field-action-editor').empty();
    var $input = $('<input/>', {type: 'text', 'class': 'fi-ai-clarification-input', placeholder: placeholder});
    var $button = actionButton(buttonLabel, function () {
      var value = $.trim($input.val());
      if (!value) { $input.focus(); return; }
      callback(value);
    });
    $input.on('keydown', function (event) { if (event.which === 13) { event.preventDefault(); $button.trigger('click'); } });
    $editor.append($input).append($button);
    $input.focus();
  }

  function showFieldActions(fieldName) {
    var field = fieldContext[fieldName];
    if (!field) { return; }
    $('#fi-ai-field-actions').remove();
    var label = field.field_label || fieldName.replace(/_/g, ' ');
    var $item = $('<div/>', {id: 'fi-ai-field-actions', 'class': 'fi-ai-message fi-ai-message-assistant fi-ai-field-actions'})
      .append($('<div/>', {'class': 'fi-ai-message-icon', 'aria-hidden': 'true'}).append('<i class="fa fa-magic"></i>'));
    var $card = $('<div/>', {'class': 'fi-ai-message-content'})
      .append($('<strong/>').text(label))
      .append($('<p/>').text('What would you like to modify?'));
    var metaText = 'Type: ' + (field.data_type || 'unknown') + (field.linked_to && field.linked_to !== '-1' ? ' · Linked to: ' + field.linked_to : '') + (field.tab_name ? ' · Tab: ' + field.tab_name : '');
    if (field.data_type === 'checkbox') {
      metaText += ' · Layout: ' + (field.checkbox_layout || 'vertical');
      if ($.isArray(field.options) && field.options.length) metaText += ' · Items: ' + field.options.join(', ');
    }
    var $meta = $('<div/>', {'class': 'fi-ai-field-current'}).text(metaText);
    var $actions = $('<div/>', {'class': 'fi-ai-field-action-grid'});

    $actions.append(actionButton(field.mandatory ? 'Make optional' : 'Make mandatory', function () {
      submitGuidedAction((field.mandatory ? 'Make ' + fieldName + ' optional.' : 'Make ' + fieldName + ' mandatory.'), 'mandatory', fieldName, field.mandatory ? '0' : '1');
    }));
    var $defaultAction = actionButton('Make default', function () {
      submitGuidedAction('Make ' + fieldName + ' the default field.', 'default', fieldName, '1');
    });
    if (field.default_field === true || field.default_field === 1 || field.default_field === '1') {
      $defaultAction.prop('disabled', true).attr('title', 'This is the current default field');
    }
    $actions.append($defaultAction);
    $actions.append(actionButton(field.index_show ? 'Hide from list' : 'Show on list', function () {
      submitGuidedAction((field.index_show ? 'Hide ' + fieldName + ' from the index page.' : 'Show ' + fieldName + ' on the index page.'), 'index', fieldName, field.index_show ? '0' : '1');
    }));

    if (field.data_type === 'checkbox') {
      $actions.append(actionButton('Add checkbox items', function () {
        fieldActionInput($card, 'New items separated by commas', 'Add items', function (items) {
          submitGuidedAction('Add ' + items + ' to the ' + fieldName + ' checkbox items.', 'add_option', fieldName, '', items);
        });
      }));
      if ($.isArray(field.options) && field.options.length) {
        $actions.append(actionButton('Remove checkbox item', function () {
          var $editor = $card.find('.fi-ai-field-action-editor').empty();
          var $select = $('<select/>', {'class': 'fi-ai-field-action-select'});
          $.each(field.options, function (_, option) { $select.append($('<option/>', {value: option, text: option})); });
          $editor.append($select).append(actionButton('Remove item', function () {
            var item = $select.val();
            submitGuidedAction('Remove ' + item + ' from the ' + fieldName + ' checkbox items.', 'remove_option', fieldName, '', item);
          }, true));
        }));
      }
      $.each(['inline', 'vertical'], function (_, layout) {
        var $layoutButton = actionButton(layout === 'inline' ? 'Inline layout' : 'Vertical layout', function () {
          submitGuidedAction('Make ' + fieldName + ' checkboxes ' + layout + '.', 'checkbox_layout', fieldName, layout);
        });
        if ((field.checkbox_layout || 'vertical') === layout) $layoutButton.prop('disabled', true).attr('title', 'Current layout');
        $actions.append($layoutButton);
      });
    }

    $.each([
      {label: 'Text', value: 'text'}, {label: 'Long text', value: 'textarea'}, {label: 'Date', value: 'date'}
    ], function (_, type) {
      $actions.append(actionButton('Change to ' + type.label, function () {
        submitGuidedAction('Change ' + fieldName + ' to ' + type.label + '.', 'type', fieldName, type.value);
      }));
    });
    $.each([{label: 'Radio', value: 'radio'}, {label: 'Checkbox', value: 'checkbox'}], function (_, type) {
      $actions.append(actionButton('Change to ' + type.label, function () {
        fieldActionInput($card, 'Choices separated by commas', 'Apply ' + type.label, function (choices) {
          submitGuidedAction('Change ' + fieldName + ' to ' + type.label + ' with choices ' + choices + '.', 'type', fieldName, type.value, choices);
        });
      }));
    });

    $.each(['Employee', 'Branch', 'Department', 'Designation', 'QcDocument'], function (_, model) {
      var modelLabel = model === 'QcDocument' ? 'Quality Document' : model;
      $actions.append(actionButton('Link to ' + modelLabel, function () {
        submitGuidedAction('Link ' + fieldName + ' to ' + model + ' model.', 'link', fieldName, model);
      }));
    });
    $actions.append(actionButton('Link to another…', function () {
      fieldActionInput($card, 'Exact FlinkISO model or form name', 'Link field', function (model) {
        submitGuidedAction('Link ' + fieldName + ' to ' + model + ' model.', 'link', fieldName, model);
      });
    }));
    $actions.append(actionButton('Rename', function () {
      fieldActionInput($card, 'New field label', 'Rename', function (newLabel) {
        submitGuidedAction('Rename ' + fieldName + ' to ' + newLabel + '.', 'rename', fieldName, newLabel);
      });
    }));
    $actions.append(actionButton(field.tab_name ? 'Move to tab' : 'Add to tab', function () {
      var $editor = $card.find('.fi-ai-field-action-editor').empty();
      var $select = $('<select/>', {'class': 'fi-ai-field-action-select'});
      $.each(tabContext, function (_, tab) {
        if (tab && tab.name && tab.name !== field.tab_name) $select.append($('<option/>', {value: tab.name, text: tab.name}));
      });
      $select.append($('<option/>', {value: '__new__', text: 'Create a new tab…'}));
      var $newTab = $('<input/>', {type: 'text', 'class': 'fi-ai-clarification-input fi-ai-new-tab-name', placeholder: 'New tab name'}).hide();
      function toggleNewTab() { $newTab.toggle($select.val() === '__new__'); }
      $select.on('change', toggleNewTab);
      var $apply = actionButton('Apply tab', function () {
        var tabName = $select.val() === '__new__' ? $.trim($newTab.val()) : $select.val();
        if (!tabName) { $newTab.show().focus(); return; }
        submitGuidedAction('Add ' + fieldName + ' to the ' + tabName + ' tab.', 'tab', fieldName, tabName);
      });
      $newTab.on('keydown', function (event) { if (event.which === 13) { event.preventDefault(); $apply.trigger('click'); } });
      $editor.append($select).append($newTab).append($apply);
      toggleNewTab();
    }));
    $actions.append(actionButton('Move', function () {
      var $editor = $card.find('.fi-ai-field-action-editor').empty();
      var $select = $('<select/>', {'class': 'fi-ai-field-action-select'}).append($('<option/>', {value: '', text: 'Beginning of form'}));
      $.each(fieldContext, function (name, candidate) {
        if (name !== fieldName) $select.append($('<option/>', {value: name, text: 'After ' + (candidate.field_label || name)}));
      });
      $editor.append($select).append(actionButton('Move field', function () {
        var after = $select.val();
        submitGuidedAction('Move ' + fieldName + (after ? ' after ' + after : ' to the beginning') + '.', 'reorder', fieldName, after);
      }));
    }));
    $actions.append(actionButton('Delete field', function () {
      var $button = $(this);
      if ($button.attr('data-confirmed') === '1') {
        submitGuidedAction('Delete the field ' + fieldName + '.', 'delete', fieldName, '');
      } else {
        $button.attr('data-confirmed', '1').text('Confirm delete');
      }
    }, true));
    if ($panel.attr('data-form-builder-url')) {
      $actions.append($('<a/>', {
        'class': 'fi-ai-field-action fi-ai-form-builder-action',
        href: $panel.attr('data-form-builder-url')
      }).append('<i class="fa fa-wrench" aria-hidden="true"></i> Go to Form Builder'));
    }

    $card.append($meta).append($actions).append($('<div/>', {'class': 'fi-ai-field-action-editor'}));
    $item.append($card).appendTo('#fi-ai-messages');
    $('#fi-ai-messages').scrollTop($('#fi-ai-messages')[0].scrollHeight);
  }

  function renderFieldWizard($box, question) {
    var typeLabels = {};
    $.each(question.field_types || [], function (_, item) { if (item && item.value) typeLabels[item.value] = item.label || item.value; });
    var state = {
      label: question.label || 'New field',
      insertAfter: question.insert_after || '',
      type: '', linkedTo: '', options: [], checkboxLayout: 'vertical',
      mandatory: false, width: 6, indexShow: false
    };
    // Start at type because the request already supplied a name. Back from
    // this first visible step still lets the user correct that name.
    var stepIndex = 1;

    function hasStandardSettings() {
      return $.inArray(state.type, ['break', 'comments', 'linked_documents']) === -1;
    }

    function steps() {
      var result = ['name', 'type'];
      if (state.type === 'dropdown-s' || state.type === 'dropdown-m') result.push('linked');
      if (state.type === 'radio' || state.type === 'checkbox') result.push('options');
      if (state.type === 'checkbox') result.push('layout');
      if (hasStandardSettings()) {
        result.push('mandatory', 'width');
        if (state.type !== 'hidden') result.push('index');
      }
      result.push('review');
      return result;
    }

    function addChoice($container, label, callback, primary) {
      return $('<button/>', {type: 'button', 'class': 'fi-ai-clarification-choice' + (primary ? ' is-primary' : '')})
        .text(label).on('click', callback).appendTo($container);
    }

    function next() { stepIndex++; render(); }
    function back() { if (stepIndex > 0) { stepIndex--; render(); } }

    function finalPrompt() {
      var prompt = 'Add a ' + state.type + ' field called ' + JSON.stringify(state.label);
      if (state.linkedTo) prompt += ' linked to ' + state.linkedTo;
      if (state.options.length) prompt += ' with choices ' + state.options.join(', ');
      if (state.type === 'checkbox') prompt += ' using a ' + state.checkboxLayout + ' layout';
      if (hasStandardSettings()) {
        prompt += state.mandatory ? ', mandatory' : ', optional';
        prompt += ', width ' + state.width + ' of 12';
        if (state.type !== 'hidden') prompt += state.indexShow ? ', and shown on the index page' : ', and hidden from the index page';
      }
      prompt += state.insertAfter ? ' after ' + state.insertAfter + '.' : ' at the end of the form.';
      return prompt;
    }

    function renderSummary($target) {
      var parts = ['Field: ' + state.label];
      if (state.type) parts.push(typeLabels[state.type] || state.type);
      if (state.linkedTo) parts.push('Linked to ' + state.linkedTo);
      if (state.options.length) parts.push(state.options.join(', '));
      if (state.type && hasStandardSettings()) parts.push(state.mandatory ? 'Mandatory' : 'Optional');
      $.each(parts, function (_, part) { $target.append($('<span/>').text(part)); });
    }

    function render() {
      var currentSteps = steps();
      if (stepIndex >= currentSteps.length) stepIndex = currentSteps.length - 1;
      var step = currentSteps[stepIndex];
      $box.empty();
      var $summary = $('<div/>', {'class': 'fi-ai-wizard-summary'});
      renderSummary($summary);
      if ($summary.children().length) $box.append($summary);
      var $question = $('<div/>', {'class': 'fi-ai-wizard-question'});
      var $choices = $('<div/>', {'class': 'fi-ai-clarification-options'});

      if (step === 'name') {
        $question.text('What should this field be called?');
        var $nameInput = $('<input/>', {type: 'text', 'class': 'fi-ai-clarification-input', placeholder: 'Field name'}).val(state.label);
        var continueName = function () {
          var newName = $.trim($nameInput.val());
          if (!newName) { $nameInput.focus(); return; }
          state.label = newName; next();
        };
        $nameInput.on('keydown', function (event) { if (event.which === 13) { event.preventDefault(); continueName(); } });
        $box.append($question).append($nameInput);
        addChoice($choices, 'Continue', continueName, true);
      } else if (step === 'type') {
        $question.text('Select the FlinkISO field type.');
        $.each(question.field_types || [], function (_, item) {
          if (!item || !item.value) return;
          addChoice($choices, item.label || item.value, function () {
            if (state.type !== item.value) {
              state.type = item.value;
              state.linkedTo = '';
              state.options = [];
              state.checkboxLayout = 'vertical';
              state.width = $.inArray(item.value, ['textarea', 'file', 'comments', 'linked_documents', 'break']) !== -1 ? 12 : 6;
            }
            next();
          });
        });
      } else if (step === 'linked') {
        $question.text('Which FlinkISO model should supply the dropdown values?');
        $.each(question.linked_models || [], function (_, item) {
          if (!item || !item.value) return;
          addChoice($choices, item.label || item.value, function () { state.linkedTo = item.value; next(); });
        });
      } else if (step === 'options') {
        $question.text('Enter the available choices, separated by commas.');
        var $input = $('<input/>', {type: 'text', 'class': 'fi-ai-clarification-input', placeholder: 'For example: Yes, No, Not Applicable'}).val(state.options.join(', '));
        var continueOptions = function () {
          var values = [];
          $.each(String($input.val() || '').split(','), function (_, value) { value = $.trim(value); if (value && $.inArray(value, values) === -1) values.push(value); });
          if (!values.length) { $input.focus(); return; }
          state.options = values; next();
        };
        $input.on('keydown', function (event) { if (event.which === 13) { event.preventDefault(); continueOptions(); } });
        $box.append($question).append($input);
        addChoice($choices, 'Continue', continueOptions, true);
      } else if (step === 'layout') {
        $question.text('How should the checkboxes be displayed?');
        addChoice($choices, 'Vertical', function () { state.checkboxLayout = 'vertical'; next(); });
        addChoice($choices, 'Inline', function () { state.checkboxLayout = 'inline'; next(); });
      } else if (step === 'mandatory') {
        $question.text('Is this field mandatory?');
        addChoice($choices, 'Mandatory', function () { state.mandatory = true; next(); });
        addChoice($choices, 'Optional', function () { state.mandatory = false; next(); });
      } else if (step === 'width') {
        $question.text('How wide should the field be?');
        $.each(question.widths || [4, 6, 12], function (_, width) {
          addChoice($choices, width + '/12', function () { state.width = parseInt(width, 10) || 12; next(); });
        });
      } else if (step === 'index') {
        $question.text('Show this field on the form’s index/list page?');
        addChoice($choices, 'Show on list', function () { state.indexShow = true; next(); });
        addChoice($choices, 'Do not show', function () { state.indexShow = false; next(); });
      } else {
        $question.text('Review the selections before creating the field.');
        var $review = $('<dl/>', {'class': 'fi-ai-wizard-review'});
        $review.append('<dt>Field</dt>').append($('<dd/>').text(state.label));
        $review.append('<dt>Type</dt>').append($('<dd/>').text(typeLabels[state.type] || state.type));
        if (state.linkedTo) $review.append('<dt>Linked to</dt>').append($('<dd/>').text(state.linkedTo));
        if (state.options.length) $review.append('<dt>Choices</dt>').append($('<dd/>').text(state.options.join(', ')));
        if (state.type === 'checkbox') $review.append('<dt>Layout</dt>').append($('<dd/>').text(state.checkboxLayout));
        if (hasStandardSettings()) {
          $review.append('<dt>Required</dt>').append($('<dd/>').text(state.mandatory ? 'Yes' : 'No'));
          $review.append('<dt>Width</dt>').append($('<dd/>').text(state.width + '/12'));
          if (state.type !== 'hidden') $review.append('<dt>Show on list</dt>').append($('<dd/>').text(state.indexShow ? 'Yes' : 'No'));
        }
        $review.append('<dt>Position</dt>').append($('<dd/>').text(state.insertAfter ? 'After ' + state.insertAfter : 'End of form'));
        $box.append($question).append($review);
        addChoice($choices, 'Create field', function () {
          $choices.find('button').prop('disabled', true);
          guidedAction = {
            action: 'add_field',
            field: '',
            value: JSON.stringify({
              field: {
                field_label: state.label,
                data_type: state.type,
                linked_to: state.linkedTo || '-1',
                options: state.options,
                checkbox_layout: state.checkboxLayout,
                mandatory: state.mandatory,
                index_show: state.indexShow,
                size: state.width
              },
              insert_after: state.insertAfter
            }),
            options: ''
          };
          submitClarifiedPrompt(finalPrompt());
        }, true);
      }

      if (!$question.parent().length) $box.append($question);
      var $navigation = $('<div/>', {'class': 'fi-ai-wizard-navigation'});
      if (stepIndex > 0) addChoice($navigation, 'Back', back);
      $navigation.append($choices);
      $box.append($navigation);
    }

    render();
  }

  function renderClarification($content, question) {
    var $box = $('<div/>', {'class': 'fi-ai-clarification'});
    if (question.type === 'field_wizard') {
      renderFieldWizard($box, question);
    } else if (question.type === 'choice' && $.isArray(question.options)) {
      var $choices = $('<div/>', {'class': 'fi-ai-clarification-options'});
      $.each(question.options, function (_, option) {
        if (!option || !option.label || !option.prompt) { return; }
        $('<button/>', {type: 'button', 'class': 'fi-ai-clarification-choice'})
          .text(option.label)
          .on('click', function () {
            $choices.find('button').prop('disabled', true);
            submitClarifiedPrompt(option.prompt);
          })
          .appendTo($choices);
      });
      $box.append($choices);
    } else if (question.type === 'text') {
      var $answer = $('<input/>', {
        type: 'text',
        'class': 'fi-ai-clarification-input',
        placeholder: question.placeholder || 'Type your answer'
      });
      var $continue = $('<button/>', {type: 'button', 'class': 'fi-ai-clarification-continue'}).text('Continue');
      function continueWithAnswer() {
        var answer = $.trim($answer.val());
        if (!answer) { $answer.focus(); return; }
        $answer.prop('disabled', true);
        $continue.prop('disabled', true);
        submitClarifiedPrompt((question.prompt_prefix || '') + answer + (question.prompt_suffix || ''));
      }
      $continue.on('click', continueWithAnswer);
      $answer.on('keydown', function (event) {
        if (event.which === 13) { event.preventDefault(); continueWithAnswer(); }
      });
      $box.append($answer).append($continue);
      window.setTimeout(function () { $answer.focus(); }, 0);
    }
    $content.append($box);
  }

  function rebuildForm(response) {
    if (!response.auto_apply || !response.apply_url || !response.apply_token) {
      return $.Deferred().resolve().promise();
    }

    var $status = appendMessage('system', 'Calling the FlinkISO API and rebuilding the form…');
    return $.ajax({
      url: response.apply_url,
      type: 'POST',
      dataType: 'json',
      timeout: 930000,
      data: {token: response.apply_token}
    }).done(function (result) {
      $status.find('p').text(result && result.message ? result.message : 'Form rebuilt successfully. Reload this page to use the updated form.');
      if (result && result.form_url) {
        $status.find('.fi-ai-message-content').append(
          $('<a/>', {'class': 'fi-ai-form-link', href: result.form_url}).text('Open generated form')
        );
      }
      if (result && result.design_url) {
        $status.find('.fi-ai-message-content').append(
          $('<a/>', {'class': 'fi-ai-form-link fi-ai-design-link', href: result.design_url}).text('Go to Form Builder')
        );
      }
    }).fail(function (xhr) {
      var message = responseError(xhr);
      if (xhr && xhr.status) { message += ' (HTTP ' + xhr.status + ')'; }
      $status.find('p').text(message);
      if (xhr && xhr.responseText && !xhr.responseJSON) {
        var detail = $.trim($('<div/>').html(xhr.responseText).text()).replace(/\s+/g, ' ');
        if (detail) {
          $status.find('.fi-ai-message-content').append(
            $('<pre/>', {'class': 'fi-ai-live-output'}).text(detail.substring(0, 500))
          );
        }
      }
      var $retry = $('<button/>', {
        type: 'button',
        'class': 'btn btn-default btn-xs fi-ai-retry-build',
        text: 'Retry form creation'
      });
      $retry.one('click', function () {
        $(this).prop('disabled', true).text('Retrying…');
        rebuildForm(response);
      });
      $status.find('.fi-ai-message-content').append($retry);
    });
  }

  function responseError(xhr) {
    if (xhr && xhr.responseJSON && xhr.responseJSON.message) { return xhr.responseJSON.message; }
    if (xhr && xhr.responseText) {
      try {
        var parsed = JSON.parse(xhr.responseText);
        if (parsed.message) { return parsed.message; }
      } catch (ignore) {}
    }
    if (xhr && xhr.status === 0) { return 'The request could not reach FlinkISO. Check the application and local AI service.'; }
    return 'FlinkISO AI could not generate a preview. Please try again.';
  }

  $(function () {
    $panel = $('#load_ai_container.fi-ai-sidebar');
    if (!$panel.length) { return; }

    $backdrop = $('#fi-ai-backdrop');
    $toggle = $('#ask_ai_icon');
    $prompt = $('#fi-ai-prompt');
    $send = $('#fi-ai-send');
    $cancel = $('#fi-ai-cancel');
    // Do not accept a prompt until the first instance-wide status check has
    // confirmed that the shared AI worker is available.
    $prompt.prop('disabled', true);
    $('#fi-ai-send-current-document').prop('disabled', true);
    $send.prop('disabled', true);
    try {
      var contextItems = JSON.parse($('#fi-ai-field-context').text() || '[]');
      $.each(contextItems, function (_, field) { if (field && field.field_name) fieldContext[field.field_name] = field; });
      tabContext = JSON.parse($('#fi-ai-tab-context').text() || '[]');
      if (!$.isArray(tabContext)) { tabContext = []; }
    } catch (ignore) {}

    // Capture submit before page-specific handlers so Enter keys and custom
    // submit scripts cannot save the underlying form while AI edit mode is on.
    if (document.addEventListener) {
      document.addEventListener('submit', function (event) {
        if (!$('body').hasClass('fi-ai-panel-open')) { return; }
        if ($(event.target).closest('.content-wrapper').length) {
          event.preventDefault();
          event.stopImmediatePropagation();
        }
      }, true);
    }

    $('.content-wrapper').on('click', '.fi-ai-selectable-field', function (event) {
      if (!$('body').hasClass('fi-ai-panel-open')) { return; }
      event.preventDefault();
      event.stopPropagation();
      if (requestInProgress) { return; }
      $('.fi-ai-selected-field').removeClass('fi-ai-selected-field');
      $(this).addClass('fi-ai-selected-field');
      showFieldActions($(this).attr('data-fi-ai-field'));
    });

    $('#fi-ai-messages').on('scroll', function () {
      if (this.scrollTop <= 40) { loadHistory(true); }
    });

    $cancel.on('click', function () {
      if (!activeAiRequest) { return; }
      if (activeAiId) { cancelAiRequest(activeAiId); }
      activeAiRequest.abort();
    });

    $('#fi-ai-messages').on('click', '.fi-ai-history-cancel', function () {
      cancelAiRequest($(this).attr('data-ai-id'), $(this));
    });

    $toggle.on('click', function (event) { event.preventDefault(); togglePanel(); });
    $('#fi-ai-close, #fi-ai-backdrop').on('click', closePanel);

    $('#fi-ai-expand').on('click', function () {
      var isWide = $panel.toggleClass('is-wide').hasClass('is-wide');
      $('body').toggleClass('fi-ai-panel-wide', isWide);
      $(this).attr('title', isWide ? 'Restore panel' : 'Expand panel');
      $(this).find('.fa').toggleClass('fa-expand', !isWide).toggleClass('fa-compress', isWide);
      window.setTimeout(function () { $(window).trigger('resize'); }, 260);
    });

    $(document).on('keydown', function (event) {
      if (event.which === 27 && $panel.hasClass('is-open')) { closePanel(); }
    });

    $('.fi-ai-suggestion[data-prompt]').on('click', function () {
      $prompt.val($(this).attr('data-prompt'));
      resizePrompt();
      $prompt.focus();
    });

    $prompt.on('input', resizePrompt);
    $prompt.on('keydown', function (event) {
      if (event.which === 13 && !event.shiftKey) {
        event.preventDefault();
        $('#fi-ai-form').trigger('submit');
      }
    });

    $('#fi-ai-form').on('submit', function (event) {
      event.preventDefault();
      var message = $.trim($prompt.val());
      if (!message || !instanceStatusReady || requestInProgress || instanceBusy) { return; }
      // Any status response already in flight belongs to the state before this
      // submission. Do not allow it to restore an earlier task's busy card.
      instanceStatusGeneration++;
      stopInstanceStatusPolling();
      instanceRequestId = '';
      $('#fi-ai-instance-busy').hide();
      $('#fi-ai-instance-stop').hide().attr('data-ai-id', '');
      var sendCurrentDocument = $panel.attr('data-controller') === 'qc_documents' && $('#fi-ai-send-current-document').prop('checked');
      appendMessage('user', message);
      $prompt.val('');
      setLoading(true);
      var activity = startActivity(sendCurrentDocument);

      var streamOffset = 0;
      var streamPending = '';
      var streamedResponse = null;
      var submittedGuidedAction = guidedAction;
      guidedAction = null;
      activeAiId = '';
      function processStream(rawText, isFinal) {
        var added = rawText.substring(streamOffset);
        streamOffset = rawText.length;
        streamPending += added;
        var lines = streamPending.split('\n');
        streamPending = isFinal ? '' : lines.pop();
        $.each(lines, function (_, line) {
          line = $.trim(line);
          if (!line) { return; }
          try {
            var event = JSON.parse(line);
            if (event.type === 'ai_model') { activity.stream(event.data); }
            if (event.type === 'status' && event.data && event.data.message) {
              if (event.data.request_id) { activeAiId = event.data.request_id; }
              activity.stream({message: {content: '[' + event.data.message + ']\n'}});
            }
            if (event.type === 'result') { streamedResponse = event.data; }
          } catch (ignore) {}
        });
      }

      activeAiRequest = $.ajax({
        url: $panel.attr('data-preview-url'),
        type: 'POST',
        dataType: 'text',
        timeout: 0,
        xhr: function () {
          var xhr = $.ajaxSettings.xhr();
          xhr.onprogress = function () { processStream(xhr.responseText || '', false); };
          return xhr;
        },
        data: {
          prompt: message,
          source_controller: $panel.attr('data-controller'),
          source_action: $panel.attr('data-action'),
          custom_table_id: $panel.attr('data-custom-table-id'),
          qc_document_id: $panel.attr('data-qc-document-id'),
          send_current_document: sendCurrentDocument ? 1 : 0,
          record_id: $panel.attr('data-record-id'),
          guided_action: submittedGuidedAction ? submittedGuidedAction.action : '',
          guided_field: submittedGuidedAction ? submittedGuidedAction.field : '',
          guided_value: submittedGuidedAction ? submittedGuidedAction.value : '',
          guided_options: submittedGuidedAction ? submittedGuidedAction.options : '',
          debug_stream: 1
        }
      }).done(function (rawResponse) {
        // A status poll started while this request was being created may hold
        // a now-obsolete "processing" snapshot. Invalidate it before the
        // completed result changes requestInProgress back to false.
        instanceStatusGeneration++;
        // A completed preview response means the API request is no longer
        // running. Clear this tab's instance card immediately; the following
        // authoritative status refresh will still reveal any newer request.
        instanceBusy = false;
        instanceRequestId = '';
        $('#fi-ai-instance-busy').hide();
        $('#fi-ai-instance-stop').hide().attr('data-ai-id', '');
        processStream(rawResponse || '', true);
        var response = streamedResponse;
        if (!response) {
          try { response = JSON.parse($.trim(rawResponse)); } catch (ignore) {}
        }
        activeAiRequest = null;
        activeAiId = '';
        $cancel.removeClass('is-visible').attr('aria-hidden', 'true');
        if (!response || response.success !== true) {
          activity.fail(response && response.message ? response.message : 'The AI response was not valid.');
          setLoading(false);
          refreshInstanceStatus();
          $prompt.focus();
          return;
        }
        activity.complete(response.handled_locally
          ? 'The selected field action was validated by FlinkISO.'
          : 'AI analysis complete. The result was validated by FlinkISO.');
        renderPreview(response);
        rebuildForm(response).always(function () {
          setLoading(false);
          refreshInstanceStatus();
          $prompt.focus();
        });
      }).fail(function (xhr, textStatus) {
        // Do not let an older status response restore a busy card after this
        // request has already failed or been cancelled.
        instanceStatusGeneration++;
        activeAiRequest = null;
        activeAiId = '';
        if (textStatus === 'abort') {
          activity.fail('AI generation cancelled.');
          $prompt.val(message);
          setLoading(false);
          $prompt.focus();
          return;
        }
        if (submittedGuidedAction) { guidedAction = submittedGuidedAction; }
        activity.fail(responseError(xhr));
        setLoading(false);
        refreshInstanceStatus();
        $prompt.focus();
      });
    });

    $('#fi-ai-instance-stop').on('click', function () {
      var id = $(this).attr('data-ai-id');
      if (id) { cancelAiRequest(id, $(this)); }
    });

    // Status is checked when the panel opens and while another active AI
    // request is being monitored. An idle page performs no status polling.
  });

  window.FlinkISOAI = { open: openPanel, close: closePanel, toggle: togglePanel };
})(window, window.jQuery);
