<?php 
$ImageWorkshopPath = PLUGIN_PATH . '/Bundle/ImageWorkshop';

require_once($ImageWorkshopPath . '/vendor/autoload.php');
require_once($ImageWorkshopPath . '/src/PHPImageWorkshop/ImageWorkshop.php');

use \PHPImageWorkshop\ImageWorkshop as ImageWorkshop;

class Util_ImageWorkshop extends ImageWorkshop {
	
}


?>