<?php
$out = shell_exec('grep -r "class Register" vendor/filament');
echo $out;
