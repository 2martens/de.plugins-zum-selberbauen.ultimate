DirectoryIndex index.php/Index/
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase {@$relDir}
RewriteRule !\. index.php?request=%{REQUEST_URI}&%{QUERY_STRING} [NC,L]
</IfModule>