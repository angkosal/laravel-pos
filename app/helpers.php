<?php
if (!function_exists('activeSegment')) {
    function activeSegment($name, $segment = 2, $class = 'active')
    {
        return request()->segment($segment) == $name ? $class : '';
    }
}
