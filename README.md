# php_templates

Allows easily use templates in php for just any text content - html,css whatever, using your own tags structure

Usage

$tmpl =  'path to file';
$page = tmpl_use($tmpl);

tmpl_respawn(page, '/dashboard/persacc', [ 
      'accno' => $persacc, 
      'debt' => ceil($data['resdata']['result']['dolg'])??0,
      'address' => $data['resdata']['result']['address']??0,
      's_all' => $data['resdata']['result']['s_all']??0,
      'purpose' => 'Оплата коммунальных услуг'
  ] );

        
echo glue(page);
