# php_templates

Allows easily use templates in php for just any text content - html,css whatever, using your own tags structure

**Usage:**



**Code:**
**functional approache //////////////////////////**

$tmpl =  'path to file';
$page = tmpl_use($tmpl);

tmpl_respawn(
      page, 
      '/tag/subitem', 
      [ 'somevar' => $persacc ] 
);
        
echo glue(page);

**oop approache //////////////////////////**
will be added later, currently depends on DI container


**Template for this code:**
**//////////////////////////**

<p>sometitle</p>
[t:tag]
<p>sometext</p>
[t:subitem]
text {somevar} text  text  text 
[/t:subitem]

[/t:tag]
