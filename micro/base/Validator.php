<?php /** MicroValidator */

namespace Micro\base;

use Micro\Micro;

/**
 * Validator is a runner validation process
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/antivir88/micro
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package micro
 * @version 1.0
 * @since 1.0
 */
class Validator
{
    /** @var array $rule current rule */
    private $rule = [];
    /** @var array $errors errors summary */
    public $errors = [];
    /** @var array $elements validation elements */
    public $elements = [];
    /** @var array $params validation parameters */
    public $params = [];
    /** @var array $validators supported validations */
    protected $validators = [
        'required' => 'RequiredValidator',
        'captcha' => 'CaptchaValidator',
        'boolean' => 'BooleanValidator',
        'compare' => 'CompareValidator',
        'string' => 'StringValidator',
        'regexp' => 'RegexpValidator',
        'number' => 'NumberValidator',
        'unique' => 'UniqueValidator',
        'range' => 'RangeValidator',
        'email' => 'EmailValidator',
        'url' => 'UrlValidator',
        'file' => 'FileValidator',
    ];


    /**
     * Constructor validator object
     *
     * @access public
     * @param array $rule configuration array
     * @result void
     */
    public function __construct(array $rule = [])
    {
        $this->rule = $rule;
    }

    /**
     * Check is empty property
     *
     * @access protected
     * @param mixed $value check value is empty
     * @param bool $trim run trim?
     * @return bool
     */
    protected function isEmpty($value, $trim = false)
    {
        return $value === null || $value === [] || $value === '' || $trim && is_scalar($value) && trim($value) === '';
    }

    /**
     * Get errors after run validation
     *
     * @access public
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Running validation process
     *
     * @access public
     * @param \Micro\web\FormModel $model model
     * @param bool $client run on client side?
     * @return bool|string
     * @throws Exception
     */
    public function run($model, $client = false)
    {
        $elements = explode(',', str_replace(' ', '', array_shift($this->rule)));
        $name = array_shift($this->rule);
        $className = null;

        if (isset($this->validators[$name])) {
            $className = '\\Micro\\validators\\' . $this->validators[$name];
        } elseif (file_exists(Micro::getInstance()->config['AppDir'] . '/validators/' . $name . '.php')) {
            $className = '\\App\\validators\\' . $name . '.php';
        } else {
            if (function_exists($name)) {
                foreach ($elements AS $element) {
                    if (property_exists($model, $element)) {
                        $model->$element = call_user_func($name, $model->$element);
                    }
                }
                return true;
            } else {
                throw new Exception('Validator ' . $name . ' not defined.');
            }
        }

        $valid = new $className;
        $valid->elements = $elements;
        $valid->params = $this->rule;
        if ($client AND method_exists($valid, 'client')) {
            $result = $valid->clientValidate($model);
        } else {
            $result = $valid->validate($model);
        }

        if ($valid->errors) {
            $this->errors[] = $valid->errors;
        }
        return $result;
    }

    /**
     * Client validation making
     *
     * @access public
     * @param \Micro\web\FormModel $model model
     * @return string
     */
    public function clientValidate($model)
    {
        $object = substr(get_class($model), strrpos(get_class($model), '\\') + 1);

        $result = null;
        foreach ($this->elements AS $element) {
            $id = $object . '_' . $element;
            $result .= 'jQuery("#' . $id . '").bind("change blur submit", function(e){ ' . $this->client($model) . ' });';
        }
        return $result;
    }
}