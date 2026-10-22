<?php
App::uses('AppController', 'Controller');
App::uses('TranslationCatalog', 'Lib');
class LanguagesController extends AppController {
    public function beforeFilter() {
        $this->_check_login();
        $this->_applyUserLanguage();
        if (!$this->Session->read('User.id') || (!$this->Session->read('User.is_mr') && !$this->Session->read('User.is_super_user'))) throw new ForbiddenException(__('Only administrators and super users can manage languages.'));
        if (!$this->Language->available()) throw new RuntimeException(__('Run the language database update first.'));
        if (!$this->Session->read('Languages.token')) $this->Session->write('Languages.token', bin2hex(openssl_random_pseudo_bytes(32)));
        $this->set('languageToken', $this->Session->read('Languages.token'));
        if ($this->request->is('post')) {
            $token = isset($this->request->data['token']) ? $this->request->data['token'] : '';
            if (!is_string($token) || !hash_equals($this->Session->read('Languages.token'), $token)) throw new ForbiddenException(__('Reload the page and try again.'));
        }
    }
    public function beforeRender() { $this->set('menus', array()); }
    public function index() {
        if ($this->request->is('post')) {
            $row = isset($this->request->data['Language']) ? $this->request->data['Language'] : array();
            if (!is_array($row)) throw new BadRequestException(__('Invalid language.'));
            $row = array_intersect_key($row, array_flip(array('name', 'code', 'plural_forms')));
            if ((isset($row['name']) && !is_string($row['name'])) || (isset($row['code']) && !is_string($row['code']))) throw new BadRequestException(__('Invalid language.'));
            $row['code'] = isset($row['code']) ? strtolower(trim($row['code'])) : '';
            $row['enabled'] = 1;
            $row['plural_forms'] = isset($row['plural_forms']) && is_string($row['plural_forms']) ? trim($row['plural_forms']) : 'nplurals=2; plural=(n != 1);';
            $catalog = new TranslationCatalog();
            $this->Language->create(); $this->Language->set($row);
            if ($this->Language->validates()) {
                try {
                    $entries = $catalog->entries($row['code']);
                    $catalog->save($row['code'], $entries, $row['plural_forms']);
                    if (!$this->Language->save(null, array('validate' => false))) throw new RuntimeException(__('Could not save language.'));
                    return $this->redirect(array('action' => 'edit', $this->Language->id));
                } catch (Exception $e) { $this->Session->setFlash($e->getMessage()); }
            }
        }
        $this->set('languages', $this->Language->find('all', array('recursive' => -1, 'order' => array('Language.name' => 'ASC'))));
    }
    public function edit($id = null) {
        $record = $this->Language->find('first', array('recursive' => -1, 'conditions' => array('Language.id' => $id)));
        if (!$record) throw new NotFoundException(__('Invalid language.'));
        $language = $record['Language']; $catalog = new TranslationCatalog();
        $entries = $catalog->entries($language['code']);
        preg_match('/nplurals=([1-6])/', $language['plural_forms'], $pluralMatch);
        $pluralCount = isset($pluralMatch[1]) ? (int)$pluralMatch[1] : 2;
        if ($this->request->is('post')) {
            try {
                if (!isset($this->request->data['version']) || $this->request->data['version'] !== $catalog->version($language['code'])) throw new RuntimeException(__('The catalog changed. Reload before saving.'));
                $settings = isset($this->request->data['Language']) ? $this->request->data['Language'] : array();
                $forms = isset($settings['plural_forms']) && is_string($settings['plural_forms']) ? trim($settings['plural_forms']) : $language['plural_forms'];
                if (!preg_match('/^nplurals=([1-6]);\s*plural=[n0-9\s()!<>=&|%?:+*\/\-]+;$/D', $forms, $pluralMatch)) throw new RuntimeException(__('Invalid plural forms.'));
                $pluralCount = (int)$pluralMatch[1];
                $name = isset($settings['name']) && is_string($settings['name']) ? trim($settings['name']) : $language['name'];
                if ($name === '' || strlen($name) > 100) throw new RuntimeException(__('Enter a language name.'));
                $entries = $catalog->update($entries, isset($this->request->data['translations']) ? $this->request->data['translations'] : array(), $pluralCount);
                $catalog->save($language['code'], $entries, $forms);
                $enabled = $language['code'] === 'eng' || !empty($settings['enabled']) ? 1 : 0;
                $this->Language->id = $language['id'];
                if (!$this->Language->save(array('Language' => array('id' => $language['id'], 'name' => $name, 'enabled' => $enabled, 'plural_forms' => $forms)), array('validate' => false, 'fieldList' => array('name', 'enabled', 'plural_forms')))) throw new RuntimeException(__('Could not save language.'));
                $this->Session->setFlash(__('Translations saved.'));
                return $this->redirect(array('action' => 'edit', $id, '?' => $this->request->query));
            } catch (Exception $e) { $this->Session->setFlash($e->getMessage()); }
        }
        $query = isset($this->request->query['q']) && is_string($this->request->query['q']) ? trim($this->request->query['q']) : '';
        if ($query !== '') $entries = array_filter($entries, function ($entry) use ($query) { return stripos($entry['msgid'], $query) !== false || stripos(implode(' ', $entry), $query) !== false; });
        $total = count($entries); $pages = max(1, (int)ceil($total / 40));
        $page = min($pages, max(1, isset($this->request->query['page']) ? (int)$this->request->query['page'] : 1));
        $entries = array_slice($entries, ($page - 1) * 40, 40, true);
        $version = $catalog->version($language['code']);
        $this->set(compact('language', 'entries', 'version', 'query', 'page', 'pages', 'total', 'pluralCount'));
    }
}
