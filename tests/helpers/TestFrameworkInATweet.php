<?php
/**
 * Unit test in a tweet framework
 * 
 * https://gist.github.com/mathiasverraes/9046427
 */
function it($m,$p){echo ($p?'✔︎':'✘')." It $m\n"; if(!$p){$GLOBALS['f']=1;}}function done(){if(@$GLOBALS['f'])die(1);}
