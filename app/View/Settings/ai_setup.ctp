<?php echo $this->Session->flash(); ?>
<div class="settings form panel panel-default">
    <div class="panel-heading"><h3 class="panel-title"><?php echo __('AI Setup'); ?></h3></div>
    <div class="panel-body">
        <?php echo $this->Form->create('AiSetting', array('role'=>'form')); ?>
        <div class="row">
            <div class="col-md-6">
                <div><h4><?php echo h(__('How to use AI along with FlinkISO.')); ?></h4><p><?php echo h(__('Visit:')); ?> <a href="https://www.flinkiso.com/manual/ai-setup.html" target="_blank">https://www.flinkiso.com/manual/ai-setup.html</a> <?php echo h(__('for details.')); ?></p><hr /></div>
                <?php echo $this->Form->input('ai_enabled', array('type'=>'checkbox','label'=>__('Turn AI On'))); ?>
                <?php echo $this->Form->input('ai_provider', array('class'=>'form-control','label'=>__('AI Provider'),'options'=>array('ollama'=>__('Self-hosted AI (Ollama)'),'openai_compatible'=>__('Cloud / OpenAI-compatible')))); ?>
                <div class="ai-server-field"><?php echo $this->Form->input('ai_api', array('class'=>'form-control','label'=>__('AI API URL'),'placeholder'=>'https://ai.example.com/v1')); ?></div>
                <div class="ai-server-field"><?php echo $this->Form->input('ai_model', array('class'=>'form-control','label'=>__('AI Model'),'placeholder'=>__('qwen3:8b or provider model name'))); ?></div>
                <div class="ai-server-field"><?php echo $this->Form->input('ai_vision_model', array('class'=>'form-control','label'=>__('Vision Model'),'placeholder'=>__('Required; may be the same as the text model'))); ?></div>
                <div class="ai-server-field"><?php echo $this->Form->input('ai_api_key_plain', array('type'=>'password','class'=>'form-control','label'=>__('AI API Key'),'autocomplete'=>'new-password','placeholder'=>$hasApiKey ? __('Saved — leave blank to keep existing key') : __('Required for cloud AI'))); ?></div>
                <div class="ai-provider-advanced"><?php echo $this->Form->input('ai_timeout', array('type'=>'number','class'=>'form-control','label'=>__('Timeout (seconds)'),'min'=>30,'max'=>1800)); ?></div>
                <div class="ai-provider-advanced"><?php echo $this->Form->input('ai_vision_context', array('type'=>'number','class'=>'form-control','label'=>__('Vision Context'),'min'=>4096,'max'=>131072)); ?></div>
                <?php echo $this->Form->input('document_converter', array(
                    'class'=>'form-control',
                    'label'=>__('Document Conversion Engine'),
                    'options'=>array(
                        'onlyoffice'=>__('ONLYOFFICE (default)'),
                        'local'=>__('Local LibreOffice + Poppler')
                    )
                )); ?>
                <div><?php echo $this->Form->input('vision_pdf_max_pages', array('type'=>'number','class'=>'form-control','label'=>__('Maximum Document Pages'),'min'=>1,'max'=>50)); ?></div>
                <div><?php echo $this->Form->input('vision_page_pixels', array('type'=>'number','class'=>'form-control','label'=>__('Vision Page Pixels'),'min'=>600,'max'=>2400)); ?></div>
                <div class="ai-local-converter"><?php echo $this->Form->input('pdf_to_ppm_path', array('class'=>'form-control','label'=>__('PDF to PPM Path'))); ?></div>
                <div class="ai-local-converter"><?php echo $this->Form->input('libreoffice_path', array('class'=>'form-control','label'=>__('LibreOffice Path'))); ?></div>
                <hr>
    <?php echo $this->Form->button(__('Save AI Setup'), array('class'=>'btn btn-primary')); ?>
    <?php echo $this->Form->end(); ?>
    <p class="help-block" style="margin-top:15px"><?php echo h(__('AI UI and API operations remain disabled until AI is turned on and all values required by the selected provider are saved.')); ?></p>
            </div>
            <div class="col-md-6">
                                            <table class="table table-bordered">
                                <tr>
                                    <td>
                                        <strong><?php echo h(__('Turn AI On')); ?></strong>
                                        <p class="help-block"><?php echo h(__('Controls whether AI features are available for this company. When turned off, the AI interface, background status requests, and AI API operations are disabled.')); ?></p></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo h(__('AI Provider')); ?></strong><p class="help-block"><?php echo h(__('Selects where AI requests are processed. Use Ollama for your own self-hosted server, Cloud/OpenAI-compatible for a hosted service that accepts OpenAI-style requests.')); ?></p></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo h(__('AI API URL')); ?></strong><div class="ai-server-field"><p class="help-block"><?php echo h(__('The base URL used to contact your AI server. For Ollama, enter its server URL, such as')); ?> <code>http://127.0.0.1:11434</code><?php echo h(__('. For a cloud provider, enter its OpenAI-compatible API base URL, usually ending in https://ollama.com')); ?><code>/v1</code>.</p></div></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo h(__('AI Model')); ?></strong><div class="ai-server-field"><p class="help-block"><?php echo h(__('The model used for text instructions, form creation, and field changes. The value must exactly match a model available on the selected AI server. (e.g:gemma4:31b, qwen3:8b )')); ?></p></div></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo h(__('Vision Model')); ?></strong><div class="ai-server-field"><p class="help-block"><?php echo h(__('The vision-capable model used when AI reads PDF pages, images, or visually structured documents. It may be the same as the text model when that model supports image input. (e.g:qwen2.5vl:7b)')); ?></p></div></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo h(__('AI API Key')); ?></strong><div class="ai-server-field"><p class="help-block"><?php echo h(__('Authenticates requests to a cloud AI provider. It is required for Cloud/OpenAI-compatible services and normally not required for a local Ollama server. The key is encrypted before storage; leave this field blank to retain the saved key.')); ?></p></div></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo h(__('Timeout')); ?></strong><div class="ai-provider-advanced"><p class="help-block"><?php echo h(__('The maximum time allowed for an AI operation before it is treated as unavailable or timed out. Increase it for large documents or slower self-hosted models.')); ?></p></div></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo h(__('Vision Context')); ?></strong><div class="ai-provider-advanced"><p class="help-block"><?php echo h(__('Sets the context-window size used for document and image analysis. A larger value lets the model consider more content together but requires more memory and processing time.')); ?></p></div></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo h(__('Document Conversion Engine')); ?></strong><p class="help-block"><?php echo h(__('ONLYOFFICE is the default and uses the Document Server already configured for FlinkISO. Select Local LibreOffice + Poppler only when both executables are installed on this application server and you explicitly want local conversion.')); ?></p></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo h(__('Maximum Document Pages')); ?></strong><p class="help-block"><?php echo h(__('Limits how many converted pages are sent to the vision model. This controls processing time, memory use, and cloud AI cost.')); ?></p></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo h(__('Vision Page Pixels')); ?></strong><p class="help-block"><?php echo h(__('Controls the maximum rendered size of each document page. Higher values improve small-text recognition but increase memory use, request size, and processing time.')); ?></p></td>
                                </tr>
                                <tr class="ai-local-converter">
                                    <td><strong><?php echo h(__('Local conversion paths')); ?></strong><p class="help-block"><?php echo h(__('PDF to PPM renders PDF pages as images. LibreOffice converts Office documents to PDF before Poppler renders them. Both absolute paths are validated when Local LibreOffice + Poppler is selected.')); ?></p></td>
                                </tr>
                    </table>

            </div>
        </div>
    </div>    
</div>
<script>
    jQuery(function($){
        function providerFields(){
            var subscription=$('#AiSettingAiProvider').val()==='flinkiso_subscription';
            $('.ai-server-field, .ai-provider-advanced').toggle(!subscription);
        }
        function converterFields(){
            $('.ai-local-converter').toggle($('#AiSettingDocumentConverter').val()==='local');
        }
        $('#AiSettingAiProvider').on('change',providerFields);
        $('#AiSettingDocumentConverter').on('change',converterFields);
        providerFields();
        converterFields();
    });
</script>
