<?php echo $this->Session->flash(); ?>
<div class="settings form panel panel-default">
  <div class="panel-heading"><h3 class="panel-title"><?php echo __('AI Setup'); ?></h3></div>
  <div class="panel-body">
    <?php echo $this->Form->create('AiSetting', array('role'=>'form')); ?>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">
                <tr>
                    <td width="40%"><?php echo $this->Form->input('ai_enabled', array('type'=>'checkbox','label'=>'Turn AI On')); ?></td>
                    <td><p class="help-block">Controls whether AI features are available for this company. When turned off, the AI interface, background status requests, and AI API operations are disabled.</p></td>
                </tr>
                <tr>
                    <td><?php echo $this->Form->input('ai_provider', array('class'=>'form-control','label'=>'AI Provider','options'=>array('ollama'=>'Self-hosted AI (Ollama)','openai_compatible'=>'Cloud / OpenAI-compatible','flinkiso_subscription'=>'FlinkISO AI subscription'),'empty'=>'Select')); ?></td>
                    <td><p class="help-block">Selects where AI requests are processed. Use Ollama for your own self-hosted server, Cloud/OpenAI-compatible for a hosted service that accepts OpenAI-style requests, or FlinkISO AI subscription to use the centrally configured service.</p></td>
                </tr>
                <tr>
                    <td><div class="ai-server-field"><?php echo $this->Form->input('ai_api', array('class'=>'form-control','label'=>'AI API URL','placeholder'=>'https://ai.example.com/v1')); ?></div></td>
                    <td><div class="ai-server-field"><p class="help-block">The base URL used to contact your AI server. For Ollama, enter its server URL, such as <code>http://127.0.0.1:11434</code>. For a cloud provider, enter its OpenAI-compatible API base URL, usually ending in <code>/v1</code>.</p></div></td>
                </tr>
                <tr>
                    <td><div class="ai-server-field"><?php echo $this->Form->input('ai_model', array('class'=>'form-control','label'=>'AI Model','placeholder'=>'qwen3:8b or provider model name')); ?></div></td>
                    <td><div class="ai-server-field"><p class="help-block">The model used for text instructions, form creation, and field changes. The value must exactly match a model available on the selected AI server.</p></div></td>
                </tr>
                <tr>
                    <td><div class="ai-server-field"><?php echo $this->Form->input('ai_vision_model', array('class'=>'form-control','label'=>'Vision Model','placeholder'=>'Required; may be the same as the text model')); ?></div></td>
                    <td><div class="ai-server-field"><p class="help-block">The vision-capable model used when AI reads PDF pages, images, or visually structured documents. It may be the same as the text model when that model supports image input.</p></div></td>
                </tr>
                <tr>
                    <td><div class="ai-server-field"><?php echo $this->Form->input('ai_api_key_plain', array('type'=>'password','class'=>'form-control','label'=>'AI API Key','autocomplete'=>'new-password','placeholder'=>$hasApiKey ? 'Saved — leave blank to keep existing key' : 'Required for cloud AI')); ?></div></td>
                    <td><div class="ai-server-field"><p class="help-block">Authenticates requests to a cloud AI provider. It is required for Cloud/OpenAI-compatible services and normally not required for a local Ollama server. The key is encrypted before storage; leave this field blank to retain the saved key.</p></div></td>
                </tr>
                <tr>
                    <td><div class="ai-advanced"><?php echo $this->Form->input('ai_timeout', array('type'=>'number','class'=>'form-control','label'=>'Timeout (seconds)','min'=>30,'max'=>1800)); ?></div></td>
                    <td><div class="ai-advanced"><p class="help-block">The maximum time allowed for an AI operation before it is treated as unavailable or timed out. Increase it for large documents or slower self-hosted models.</p></div></td>
                </tr>
                <tr>
                    <td><div class="ai-advanced"><?php echo $this->Form->input('ai_vision_context', array('type'=>'number','class'=>'form-control','label'=>'Vision Context','min'=>4096,'max'=>131072)); ?></div></td>
                    <td><div class="ai-advanced"><p class="help-block">Sets the context-window size used for document and image analysis. A larger value lets the model consider more content together but requires more memory and processing time.</p></div></td>
                </tr>
                <tr>
                    <td><div class="ai-advanced"><?php echo $this->Form->input('vision_pdf_max_pages', array('type'=>'number','class'=>'form-control','label'=>'Maximum PDF Pages','min'=>1,'max'=>50)); ?></div></td>
                    <td><div class="ai-advanced"><p class="help-block">Limits how many pages of a PDF are converted to images and sent to the vision model. This controls processing time, memory use, and cloud AI cost.</p></div></td>
                </tr>
                <tr>
                    <td><div class="ai-advanced"><?php echo $this->Form->input('vision_page_pixels', array('type'=>'number','class'=>'form-control','label'=>'Vision Page Pixels','min'=>600,'max'=>2400)); ?></div></td>
                    <td><div class="ai-advanced"><p class="help-block">Controls the rendered image size for each document page. Higher values improve small-text recognition but increase memory use, request size, and processing time.</p></div></td>
                </tr>
                <tr>
                    <td><div class="ai-advanced"><?php echo $this->Form->input('pdf_to_ppm_path', array('class'=>'form-control','label'=>'PDF to PPM Path')); ?></div></td>
                    <td><div class="ai-advanced"><p class="help-block">The full server path to the <code>pdftoppm</code> executable. It is used to convert PDF pages into images before sending them to the vision model.</p></div></td>
                </tr>
                <tr>
                    <td><div class="ai-advanced"><?php echo $this->Form->input('libreoffice_path', array('class'=>'form-control','label'=>'LibreOffice Path')); ?></div></td>
                    <td><div class="ai-advanced"><p class="help-block">The full server path to the LibreOffice executable. It is used to convert supported office documents into a format that can be extracted or visually analysed by AI.</p></div></td>
                </tr>

            </table>
        </div>
    </div>
    <hr>
    <?php echo $this->Form->button(__('Save AI Setup'), array('class'=>'btn btn-primary')); ?>
    <?php echo $this->Form->end(); ?>
    <p class="help-block" style="margin-top:15px">AI UI and API operations remain disabled until AI is turned on and all values required by the selected provider are saved.</p>
  </div>
</div>
<script>
jQuery(function($){
  function providerFields(){
    var subscription=$('#AiSettingAiProvider').val()==='flinkiso_subscription';
    $('.ai-server-field, .ai-advanced').toggle(!subscription);
  }
  $('#AiSettingAiProvider').on('change',providerFields);
  providerFields();
});
</script>
