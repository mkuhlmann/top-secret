<?php declare(strict_types=1);

namespace TopSecret\Model;

use \RedBeanPHP\SimpleModel;
use \R;

class Item extends SimpleModel  {
    
    public static function slugExists($slug) : bool {
        return R::count('item', 'slug = ?', [$slug]) > 0;
    }
}