<?php

$p = '123456';
echo $p;
echo '<br/>';
echo password_hash($p,PASSWORD_DEFAULT);
