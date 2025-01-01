# php_templates

Allows easily use templates in php for just any text content - html,css whatever, using your own tags structure

**Usage**


$tmpl =  'path to file';
$page = tmpl_use($tmpl);

tmpl_respawn(
      page, 
      '/tag/subitem', 
      [ 'somevar' => $persacc ] 
);
        
echo glue(page);


Template for this code

<h1>sometitle</h1>
[t:tag]
<h1>sometext</h1>
[t:subitem]
text {somevar} text  text  text 
[/t:subitem]

[/t:tag]
