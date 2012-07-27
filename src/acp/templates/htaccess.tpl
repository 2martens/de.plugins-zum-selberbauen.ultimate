AddType 'text/html; charset=UTF-8' html
DirectoryIndex index.php/Index/
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase {@$relDir}
RewriteRule !\. index.php?request=%{literal}{{/literal}REQUEST_URI{literal}}{/literal}&%{literal}{{/literal}QUERY_STRING{literal}}{/literal} [NC,L]
</IfModule>