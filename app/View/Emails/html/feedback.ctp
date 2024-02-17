<?php 

echo $message = "
User Friendly : " . $this->request->data['User']['user_friendly'] ."<br />".
"Coverage : " . $this->request->data['User']['coverage']  ."<br />".
"Message : " . $this->request->data['User']['feedback']  ."<br />".
"By : " . $company . "<br /> User " . $this->Session->read('User.username');
?>
