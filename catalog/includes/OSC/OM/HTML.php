<?php
/**
  * osCommerce Online Merchant
  *
  * @copyright Copyright (c) 2015 osCommerce; http://www.oscommerce.com
  * @license GPL; http://www.oscommerce.com/gpllicense.txt
  */

namespace OSC\OM;

class HTML
{
    public static function output($string, $translate = null)
    {
        if (!isset($translate)) {
            $translate = [
                '"' => '&quot;'
            ];
        }

        return strtr(trim($string), $translate);
    }

    public static function outputProtected($string)
    {
        return htmlspecialchars(trim($string));
    }

    public static function sanitize($string)
    {
        $patterns = [
            '/ +/',
            '/[<>]/'
        ];

        $replace = [
            ' ',
            '_'
        ];

        return preg_replace($patterns, $replace, trim($string));
    }

    public static function image($src, $alt = '', $width = '', $height = '', $parameters = '', $responsive = true, $bootstrap_css = '')
    {
        if ((empty($src) || ($src == DIR_WS_IMAGES)) && (IMAGE_REQUIRED == 'false')) {
            return false;
        }

// alt is added to the img tag even if it is null to prevent browsers from outputting
// the image filename as default
        $image = '<img src="' . static::output($src) . '" alt="' . static::output($alt) . '"';

        if (!empty($alt)) {
            $image .= ' title="' . static::output($alt) . '"';
        }

        if ((CONFIG_CALCULATE_IMAGE_SIZE == 'true') && (empty($width) || empty($height))) {
            if ($image_size = @getimagesize($src)) {
                if (empty($width) && !empty($height)) {
                    $ratio = $height / $image_size[1];
                    $width = (int)($image_size[0] * $ratio);
                } elseif (!empty($width) && empty($height)) {
                    $ratio = $width / $image_size[0];
                    $height = (int)($image_size[1] * $ratio);
                } elseif (empty($width) && empty($height)) {
                    $width = $image_size[0];
                    $height = $image_size[1];
                }
            } elseif (IMAGE_REQUIRED == 'false') {
                return false;
            }
        }

        if (!empty($width) && !empty($height)) {
            $image .= ' width="' . static::output($width) . '" height="' . static::output($height) . '"';
        }

        $class = [];

        if ($responsive === true) {
            $class[] = 'img-responsive';
        }

        if (!empty($bootstrap_css)) {
            $class[] = $bootstrap_css;
        }

        if (!empty($class)) {
            $image .= ' class="' . implode(' ', $class) . '"';
        }

        if (!empty($parameters)) {
            $image .= ' ' . $parameters;
        }

        $image .= ' />';

        return $image;
    }

    public static function form($name, $action, $method = 'post', $parameters = '', array $flags = [])
    {
        if (!isset($flags['tokenize']) || !is_bool($flags['tokenize'])) {
            $flags['tokenize'] = false;
        }

        if (!isset($flags['session_id']) || !is_bool($flags['session_id'])) {
            $flags['session_id'] = false;
        }

        $form = '<form name="' . static::output($name) . '" action="' . static::output($action) . '" method="' . static::output($method) . '"';

        if (!empty($parameters)) {
            $form .= ' ' . $parameters;
        }

        $form .= '>';

        if (isset($flags['action'])) {
            $form .= static::hiddenField('action', $flags['action']);
        }

        if (($flags['session_id'] === true) && (session_status() === PHP_SESSION_ACTIVE) && defined('SID') && !empty(SID)) {
            $form .= static::hiddenField(session_name(), session_id());
        }

        if (($flags['tokenize'] === true) && isset($_SESSION['sessiontoken'])) {
            $form .= static::hiddenField('formid', $_SESSION['sessiontoken']);
        }

        return $form;
    }

    public static function inputField($name, $value = '', $parameters = '', $type = 'text', $reinsert_value = true, $class = 'form-control')
    {
        $field = '<input type="' . static::output($type) . '" name="' . static::output($name) . '"';

        if (($reinsert_value == true) && ((isset($_GET[$name]) && is_string($_GET[$name])) || (isset($_POST[$name]) && is_string($_POST[$name])))) {
            if (isset($_GET[$name]) && is_string($_GET[$name])) {
                $value = $_GET[$name];
            } elseif (isset($_POST[$name]) && is_string($_POST[$name])) {
                $value = $_POST[$name];
            }
        }

        if (!empty($value)) {
            $field .= ' value="' . static::output($value) . '"';
        }

        if (!empty($parameters)) {
            $field .= ' ' . $parameters;
        }

        if (!empty($class)) {
            $field .= ' class="' . $class . '"';
        }

        $field .= ' />';

        return $field;
    }

    public static function passwordField($name, $value = '', $parameters = 'maxlength="40"')
    {
        return static::inputField($name, $value, $parameters, 'password', false);
    }

    protected static function selectionField($name, $type, $value = '', $checked = false, $parameters = '')
    {
        $selection = '<input type="' . static::output($type) . '" name="' . static::output($name) . '"';

        if (!empty($value)) {
            $selection .= ' value="' . static::output($value) . '"';
        }

        if (($checked == true) || (isset($_GET[$name]) && is_string($_GET[$name]) && (($_GET[$name] == 'on') || ($_GET[$name] == $value))) || (isset($_POST[$name]) && is_string($_POST[$name]) && (($_POST[$name] == 'on') || ($_POST[$name] == $value)))) {
            $selection .= ' checked="checked"';
        }

        if (!empty($parameters)) {
            $selection .= ' ' . $parameters;
        }

        $selection .= ' />';

        return $selection;
    }

    public static function checkboxField($name, $value = '', $checked = false, $parameters = '')
    {
        return static::selectionField($name, 'checkbox', $value, $checked, $parameters);
    }

    public static function radioField($name, $value = '', $checked = false, $parameters = '')
    {
        return static::selectionField($name, 'radio', $value, $checked, $parameters);
    }

    public static function textareaField($name, $width, $height, $text = '', $parameters = '', $reinsert_value = true, $class = 'form-control')
    {
        $field = '<textarea name="' . static::output($name) . '" cols="' . static::output($width) . '" rows="' . static::output($height) . '"';

        if (!empty($parameters)) {
            $field .= ' ' . $parameters;
        }

        if (!empty($class)) {
            $field .= ' class="' . $class . '"';
        }

        $field .= '>';

        if (($reinsert_value == true) && ((isset($_GET[$name]) && is_string($_GET[$name])) || (isset($_POST[$name]) && is_string($_POST[$name])))) {
            if (isset($_GET[$name]) && is_string($_GET[$name])) {
                $field .= static::outputProtected($_GET[$name]);
            } elseif (isset($_POST[$name]) && is_string($_POST[$name])) {
                $field .= static::outputProtected($_POST[$name]);
            }
        } elseif (!empty($text)) {
            $field .= static::outputProtected($text);
        }

        $field .= '</textarea>';

        return $field;
    }

    public static function selectField($name, array $values, $default = null, $parameters = '', $required = false, $class = 'form-control')
    {
        $group = false;

        $field = '<select name="' . static::output($name) . '"';

        if ($required == true) {
            $field .= ' required aria-required="true"';
        }

        if (!empty($parameters)) {
            $field .= ' ' . $parameters;
        }

        if (!empty($class)) {
            $field .= ' class="' . $class . '"';
        }

        $field .= '>';

        if ($required == true) {
            $field .= '<option value="">' . PULL_DOWN_DEFAULT . '</option>';
        }

        if (empty($default) && ((isset($_GET[$name]) && is_string($_GET[$name])) || (isset($_POST[$name]) && is_string($_POST[$name])))) {
            if (isset($_GET[$name]) && is_string($_GET[$name])) {
                $default = $_GET[$name];
            } elseif (isset($_POST[$name]) && is_string($_POST[$name])) {
                $default = $_POST[$name];
            }
        }

        $ci = new \CachingIterator(new \ArrayIterator($values), \CachingIterator::TOSTRING_USE_CURRENT); // used for hasNext() below

        foreach ($ci as $v) {
            if (isset($v['group'])) {
                if ($group != $v['group']) {
                    $group = $v['group'];

                    $field .= '<optgroup label="' . static::output($v['group']) . '">';
                }
            }

            $field .= '<option value="' . static::output($v['id']) . '"';

            if (isset($default) && ($v['id'] == $default)) {
                $field .= ' selected="selected"';
            }

            if (isset($v['params'])) {
                $field .= ' ' . $v['params'];
            }

            $field .= '>' . static::outputProtected($v['text']) . '</option>';

            if (($group !== false) && (($group != $v['group']) || ($ci->hasNext() === false))) {
                $group = false;

                $field .= '</optgroup>';
            }
        }

        $field .= '</select>';

        return $field;
    }

    public static function hiddenField($name, $value = '', $parameters = '')
    {
        $field = '<input type="hidden" name="' . static::output($name) . '"';

        if (!empty($value)) {
            $field .= ' value="' . static::output($value) . '"';
        } elseif ((isset($_GET[$name]) && is_string($_GET[$name])) || (isset($_POST[$name]) && is_string($_POST[$name]))) {
            if (isset($_GET[$name]) && is_string($_GET[$name])) {
                $field .= ' value="' . static::output($_GET[$name]) . '"';
            } elseif (isset($_POST[$name]) && is_string($_POST[$name])) {
                $field .= ' value="' . static::output($_POST[$name]) . '"';
            }
        }

        if (!empty($parameters)) {
            $field .= ' ' . $parameters;
        }

        $field .= ' />';

        return $field;
    }

    public static function button($title = null, $icon = null, $link = null, $priority = null, $params = null, $class = null)
    {
        static $button_counter = 1;

        $types = ['submit', 'button', 'reset'];

        if (!isset($params['type'])) {
            $params['type'] = 'submit';
        }

        if (!in_array($params['type'], $types)) {
            $params['type'] = 'submit';
        }

        if (($params['type'] == 'submit') && isset($link)) {
              $params['type'] = 'button';
        }

        $button = '';

        if (($params['type'] == 'button') && isset($link)) {
            $button .= '<a id="tdb' . $button_counter . '" href="' . $link . '"';

            if (isset($params['newwindow'])) {
                $button .= ' target="_blank"';
            }
        } else {
            $button .= '<button id="tdb' . $button_counter . '" type="' . static::output($params['type']) . '"';
        }

        if (isset($params['params'])) {
            $button .= ' ' . $params['params'];
        }

        $button .= ' class="btn ' . (isset($class) ? $class : 'btn-default') . '">';

        if (isset($icon) && !empty($icon)) {
            $button .= '<span class="' . $icon . '"></span> ';
        }

        $button .= $title;

        if (($params['type'] == 'button') && isset($link)) {
            $button .= '</a>';
        } else {
            $button .= '</button>';
        }

        $button_counter++;

        return $button;
    }

    public static function stars($rating = 0, $meta = false)
    {
        $stars = str_repeat('<span class="glyphicon glyphicon-star"></span>', (int)$rating) .
                 str_repeat('<span class="glyphicon glyphicon-star-empty"></span>', 5-(int)$rating);

        if ($meta !== false) {
            $stars .= '<meta itemprop="rating" content="' . (int)$rating . '" />';
        }

        return $stars;
    }
}
